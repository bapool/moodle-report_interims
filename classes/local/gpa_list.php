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
 * GPA lists: GPA report, superintendent list, principal list and eligibility report
 * for the high school and middle school.
 *
 * The GPA on these lists counts every visible course the student has a student role in.
 *
 * | List                   | High school           | Middle school                        |
 * |------------------------|-----------------------|--------------------------------------|
 * | GPA report             | GPA above 0           | everyone with grades                 |
 * | Superintendent list    | GPA 3.75 or higher    | GPA above 3.90                       |
 * | Principal list         | GPA 3.33 to 3.74      | only A/B grades and GPA below 4.00   |
 * | Eligibility report     | GPA below 2.00        | GPA below 1.60                       |
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2026 Brian Pool (version 3.0)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gpa_list {
    /** @var string High school GPA report. */
    public const HS_GPA = 'hsgpa';

    /** @var string High school superintendent list. */
    public const HS_SUPER = 'hssuper';

    /** @var string High school principal list. */
    public const HS_PRINCIPAL = 'hsprinc';

    /** @var string High school eligibility report. */
    public const HS_ELIGIBILITY = 'hssports';

    /** @var string Middle school GPA report. */
    public const MS_GPA = 'msgpa';

    /** @var string Middle school superintendent list. */
    public const MS_SUPER = 'mssuper';

    /** @var string Middle school principal list. */
    public const MS_PRINCIPAL = 'msprinc';

    /** @var string Middle school eligibility report. */
    public const MS_ELIGIBILITY = 'mssports';

    /** @var array List type => [school level, grading scale, title string id or null]. */
    public const LISTS = [
        self::HS_GPA => [helper::LEVEL_HS, grades::SCALE_HS, null],
        self::HS_SUPER => [helper::LEVEL_HS, grades::SCALE_HS, 'superintendentlist'],
        self::HS_PRINCIPAL => [helper::LEVEL_HS, grades::SCALE_HS, 'principallist'],
        self::HS_ELIGIBILITY => [helper::LEVEL_HS, grades::SCALE_HS, 'eligibilityreport'],
        self::MS_GPA => [helper::LEVEL_MS, grades::SCALE_MS, null],
        self::MS_SUPER => [helper::LEVEL_MS, grades::SCALE_MS, 'superintendentlist'],
        self::MS_PRINCIPAL => [helper::LEVEL_MS, grades::SCALE_MS, 'principallist'],
        self::MS_ELIGIBILITY => [helper::LEVEL_MS, grades::SCALE_MS, 'eligibilityreport'],
    ];

    /**
     * Prints a GPA list for the students of a course. The caller must already have called require_login($course).
     *
     * @param stdClass $course
     * @param string $script File name of the calling script in /report/interims/reports/
     * @param string $type One of the class constants
     */
    public static function run(stdClass $course, string $script, string $type): void {
        global $PAGE;

        [$level, $scale, $titleid] = self::LISTS[$type];
        $context = context_course::instance($course->id);
        require_capability('report/interims:view', $context);
        helper::require_level($course, $context, [$level]);

        $PAGE->set_url(new moodle_url('/report/interims/reports/' . $script, ['id' => $course->id]));
        $PAGE->set_pagelayout('print');

        echo helper::print_page_start($course->id, get_string('reportintro', 'report_interims'));
        echo '<div style="text-align: center; font-size: large;">';
        if ($titleid) {
            echo s(helper::school_name()) . ' ' . get_string($titleid, 'report_interims');
        }
        echo self::row_html(
            get_string('studentname', 'report_interims'),
            get_string('studentid', 'report_interims'),
            get_string('gpa', 'report_interims')
        );

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
            $rows = grades::student_course_grades($student->id);
            if (!$rows) {
                continue;
            }
            $gpa = grades::gpa($rows, $scale, grades::GPA_ALL);
            if (self::qualifies($type, $gpa, $rows)) {
                $first = reset($rows);
                echo self::row_html(
                    s($first->lastname . ', ' . $first->firstname),
                    s($first->studentidnumber),
                    grades::format_gpa($gpa)
                );
            }
        }
        echo '</div>';
    }

    /**
     * Does a student belong on this list?
     *
     * @param string $type One of the class constants
     * @param float $gpa The student's GPA (two decimals)
     * @param array $rows The student's course grades
     * @return bool
     */
    public static function qualifies(string $type, float $gpa, array $rows): bool {
        switch ($type) {
            case self::HS_GPA:
                return $gpa > 0;
            case self::HS_SUPER:
                return $gpa > 3.74;
            case self::HS_PRINCIPAL:
                return $gpa > 3.32 && $gpa < 3.75;
            case self::HS_ELIGIBILITY:
                return $gpa < 2.00;
            case self::MS_SUPER:
                return $gpa > 3.9;
            case self::MS_PRINCIPAL:
                return self::all_a_or_b($rows) && $gpa > 0 && $gpa < 4.0;
            case self::MS_ELIGIBILITY:
                return $gpa < 1.6;
            default:
                return true;
        }
    }

    /**
     * Are all of the student's graded courses an A or B (80% or above)?
     *
     * @param array $rows The student's course grades
     * @return bool
     */
    protected static function all_a_or_b(array $rows): bool {
        foreach ($rows as $row) {
            $rounded = grades::round_grade($row->finalgrade);
            if ($rounded !== null && $rounded < 80) {
                return false;
            }
        }
        return true;
    }

    /**
     * One line of the list (its own bordered table, like the original report).
     *
     * @param string $name Escaped student name
     * @param string $idnumber Escaped student ID
     * @param string $gpa GPA text
     * @return string HTML
     */
    protected static function row_html(string $name, string $idnumber, string $gpa): string {
        return '<table border="1" cellpadding="0" width="660px" style="margin-left: auto; margin-right: auto;"><tr>' .
            '<td width="300px" style="font-size: medium;">' . $name . '</td>' .
            '<td width="150px" align="center">' . $idnumber . '</td>' .
            '<td width="150px" align="center">' . $gpa . '</td>' .
            '</tr></table>';
    }
}
