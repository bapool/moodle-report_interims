<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace report_interims\local;

use context_course;
use moodle_exception;
use stdClass;

/**
 * LOA grade export for Ohio DASL student information systems.
 *
 * Writes one line per student group membership into a one-column spreadsheet:
 * "<course number>           <section>  000<student ID>         <grade>".
 *
 *  - Course number: the course ID number without its last two characters (teacher initials),
 *    only for course numbers 101 to 69999.
 *  - Section: the last two characters of the group name with any "-" removed. Sections 1 to 9
 *    are followed by a space, 10 to 45 are used as they are, anything else becomes "0 ".
 *  - Student ID: the user ID number.
 *  - Grade: the course total rounded to a whole number, at most 100.
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2026 Brian Pool (version 3.0)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class loa_export {
    /**
     * Sends the export as a download. The caller must already have called require_login($course).
     *
     * @param stdClass $course
     * @param bool $thiscourseonly True for the single class export, false for all of the students' courses
     */
    public static function run(stdClass $course, bool $thiscourseonly): void {
        global $CFG;
        require_once($CFG->libdir . '/excellib.class.php');

        $context = context_course::instance($course->id);
        require_capability('report/interims:view', $context);
        require_capability('report/interims:viewadminreports', $context);
        if (get_config('report_interims', 'enableloaexport') === '0') {
            throw new moodle_exception('reportnotavailable', 'report_interims');
        }

        $students = get_enrolled_users(
            $context,
            'mod/assign:submit',
            0,
            'u.id',
            'u.lastname ASC, u.firstname ASC',
            0,
            0,
            true
        );

        $filename = clean_filename($course->shortname . ' ' . get_string('grades') . '.xls');
        $workbook = new \MoodleExcelWorkbook('-');
        $workbook->send($filename);
        $sheet = $workbook->add_worksheet($filename);
        $row = 0;
        foreach ($students as $student) {
            foreach (self::student_lines($student->id, $thiscourseonly ? (int)$course->id : 0, $thiscourseonly) as $line) {
                $sheet->write_string($row, 0, $line);
                $row++;
            }
        }
        $workbook->close();
    }

    /**
     * Export lines for one student.
     *
     * @param int $userid
     * @param int $courseid Only this course, or 0 for all courses
     * @param bool $singleround True to round the grade once (single class export), false to round
     *                          to 2 decimals first (full export), as in the original exports
     * @return string[]
     */
    public static function student_lines(int $userid, int $courseid, bool $singleround): array {
        global $DB;
        $sql = "SELECT gm.id AS gmid, g.name AS groupname, c.idnumber AS coursenum,
                       u.idnumber AS studentidnumber, gg.finalgrade
                  FROM {groups_members} gm
                  JOIN {user} u ON gm.userid = u.id
                  JOIN {groups} g ON gm.groupid = g.id
                  JOIN {course} c ON g.courseid = c.id
                  JOIN {grade_grades} gg ON gg.userid = u.id
                  JOIN {grade_items} gi ON gg.itemid = gi.id AND gi.courseid = g.courseid
                 WHERE gm.userid = :userid
                   AND gi.itemtype = 'course'";
        $params = ['userid' => $userid];
        if ($courseid) {
            $sql .= " AND c.id = :courseid";
            $params['courseid'] = $courseid;
        }
        $lines = [];
        foreach ($DB->get_records_sql($sql, $params) as $record) {
            $coursenum = (string)$record->coursenum;
            $number = intval(substr($coursenum, 0, -2));
            if ($number <= 100 || $number >= 70000) {
                continue;
            }
            $finalcourse = substr($coursenum, 0, -2);
            $section = self::section((string)$record->groupname);
            $grade = $record->finalgrade === null ? 0.0 : (float)$record->finalgrade;
            $finalgrade = $singleround ? round($grade) : round(round($grade, 2));
            $finalgrade = min(100, (int)$finalgrade);
            $lines[] = $finalcourse . '           ' . $section . '  000' . $record->studentidnumber . '         ' .
                $finalgrade;
        }
        return $lines;
    }

    /**
     * Section code from a group name.
     *
     * @param string $groupname
     * @return string
     */
    public static function section(string $groupname): string {
        $section = str_replace('-', '', substr($groupname, -2, 2));
        $number = (int)$section;
        if ($number > 45 || $number < 1) {
            return '0 ';
        }
        if ($number > 9) {
            return $section;
        }
        return $section . ' ';
    }
}
