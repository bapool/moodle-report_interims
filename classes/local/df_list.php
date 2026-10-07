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
use moodle_url;
use stdClass;

/**
 * Teacher lists: "My class D/F", "All my students D/F" and "No grades".
 *
 *  - My class D/F: every student in this course whose course grade is below the D/F cut-off or missing.
 *  - All my students D/F: for every student in this course, each of their courses (matching the
 *    course ID number pattern) where the grade is below the cut-off or missing.
 *  - No grades: as above, but only the courses with no grade at all.
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2026 Brian Pool (version 3.0)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class df_list {
    /** @var string High school, this course only. */
    public const HS_MY = 'hsmydf';

    /** @var string Middle school, this course only. */
    public const MS_MY = 'msmydf';

    /** @var string High school, all courses of the students in this course. */
    public const HS_ALL = 'hsalldf';

    /** @var string Middle school, all courses of the students in this course. */
    public const MS_ALL = 'msalldf';

    /** @var string Courses with no grade, all courses of the students in this course (HS or MS). */
    public const NO_GRADES = 'nogrades';

    /**
     * Prints a list for the students of a course. The caller must already have called require_login($course).
     *
     * @param stdClass $course
     * @param string $script File name of the calling script in /report/interims/reports/
     * @param string $type One of the class constants
     */
    public static function run(stdClass $course, string $script, string $type): void {
        global $PAGE;

        $context = context_course::instance($course->id);
        require_capability('report/interims:view', $context);
        if ($type === self::NO_GRADES) {
            $levels = [helper::LEVEL_HS, helper::LEVEL_MS];
        } else if (in_array($type, [self::HS_MY, self::HS_ALL])) {
            $levels = [helper::LEVEL_HS];
        } else {
            $levels = [helper::LEVEL_MS];
        }
        helper::require_level($course, $context, $levels);
        $scale = in_array($type, [self::HS_MY, self::HS_ALL]) ? grades::SCALE_HS : grades::SCALE_MS;

        $PAGE->set_url(new moodle_url('/report/interims/reports/' . $script, ['id' => $course->id]));
        $PAGE->set_pagelayout('print');

        echo helper::print_page_start($course->id, '');

        if ($type === self::HS_MY || $type === self::MS_MY) {
            $heading = format_string($course->fullname, true, ['context' => $context]);
            $title = get_string('dfreport', 'report_interims');
        } else {
            $heading = get_string('allenrolledstudents', 'report_interims');
            $title = get_string($type === self::NO_GRADES ? 'nogradesreportall' : 'dfreportall', 'report_interims');
        }
        echo '<div style="text-align: center; font-weight: bold;">';
        echo '<div style="font-size: x-large; margin-top: 16px;">' . $heading . '</div>';
        echo '<div style="font-size: medium;">' . $title . '<br><br></div>';
        echo '<table border="1" cellpadding="1" width="660px" style="margin-left: auto; margin-right: auto;"><tr>';
        foreach (
            [['150px', 'student'], ['70px', 'coursenumber'], ['240px', 'course'], ['70px', 'percentgrade'],
                ['70px', 'lettergrade']] as [$width, $stringid]
        ) {
            echo '<td width="' . $width . '" bgcolor="#C0C0C0"><b>' . get_string($stringid, 'report_interims') . '</b></td>';
        }
        echo '</tr>';

        $group = groups_get_course_group($course, true);
        $students = get_enrolled_users(
            $context,
            'mod/assign:submit',
            $group,
            'u.id, u.lastname',
            'u.lastname ASC, u.firstname ASC',
            0,
            0,
            true
        );
        foreach ($students as $student) {
            if (helper::is_excluded_lastname($student->lastname)) {
                continue;
            }
            if ($type === self::HS_MY || $type === self::MS_MY) {
                $rows = self::course_rows($student->id, $course->id);
            } else {
                $rows = self::all_course_rows($student->id);
            }
            foreach ($rows as $row) {
                $rounded = grades::round_grade($row->finalgrade);
                if ($type === self::NO_GRADES && $rounded !== null) {
                    continue;
                }
                echo self::row_html($row, $rounded, $scale);
            }
        }
        echo '</table>';
        echo '<div style="font-size: small;">' . s(helper::printed_on_string()) . '</div></div>';
    }

    /**
     * The student's grade in this course, if it is below the cut-off or missing.
     *
     * @param int $userid
     * @param int $courseid
     * @return stdClass[]
     */
    protected static function course_rows(int $userid, int $courseid): array {
        global $DB;
        $sql = "SELECT gg.id AS ggid, gg.finalgrade, u.firstname, u.lastname, c.fullname, c.idnumber
                  FROM {grade_grades} gg
                  JOIN {grade_items} gi ON gg.itemid = gi.id
                  JOIN {user} u ON u.id = gg.userid
                  JOIN {course} c ON gi.courseid = c.id
                 WHERE gg.userid = :userid
                   AND c.id = :courseid
                   AND gi.itemtype = 'course'
                   AND c.visible = 1
                   AND (gg.finalgrade < :threshold OR gg.finalgrade IS NULL)";
        $params = ['userid' => $userid, 'courseid' => $courseid, 'threshold' => helper::df_threshold()];
        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * All of the student's active courses where the grade is below the cut-off or missing.
     *
     * @param int $userid
     * @return stdClass[]
     */
    protected static function all_course_rows(int $userid): array {
        global $DB;
        $sql = "SELECT DISTINCT gg.id AS ggid, gg.finalgrade, u.firstname, u.lastname, c.fullname, c.idnumber
                  FROM {grade_grades} gg
                  JOIN {grade_items} gi ON gg.itemid = gi.id
                  JOIN {user} u ON u.id = gg.userid
                  JOIN {course} c ON gi.courseid = c.id
                  JOIN {enrol} e ON e.courseid = c.id
                  JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.userid = u.id
                 WHERE gg.userid = :userid
                   AND gi.itemtype = 'course'
                   AND c.visible = 1
                   AND ue.status = :active
                   AND (gg.finalgrade < :threshold OR gg.finalgrade IS NULL)
              ORDER BY u.lastname, u.firstname, c.fullname";
        $params = ['userid' => $userid, 'active' => ENROL_USER_ACTIVE, 'threshold' => helper::df_threshold()];
        $rows = $DB->get_records_sql($sql, $params);
        return array_values(array_filter($rows, function ($row) {
            return helper::is_df_course_idnumber($row->idnumber);
        }));
    }

    /**
     * One row of the list.
     *
     * @param stdClass $row
     * @param int|null $rounded Rounded grade (null when missing)
     * @param string $scale grades::SCALE_ constant
     * @return string HTML
     */
    protected static function row_html(stdClass $row, ?int $rounded, string $scale): string {
        if ($rounded === null) {
            $percent = '';
            $letter = get_string('nogradesyet', 'report_interims');
        } else {
            $percent = (string)$rounded;
            $letter = grades::letter($rounded, $scale);
        }
        return '<tr><td width="150px">' . s($row->lastname . ', ' . $row->firstname) . '</td>' .
            '<td width="70px">' . s($row->idnumber) . '</td>' .
            '<td width="240px">' . s($row->fullname) . '</td>' .
            '<td width="70px">' . s($percent) . '% </td>' .
            '<td width="70px">' . s($letter) . '</td></tr>';
    }
}
