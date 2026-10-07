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

/**
 * Grade scales, GPA calculation and the gradebook queries used by the HS/MS reports.
 *
 * Grading rules (unchanged from the original National Trail reports):
 *  - The course grade is the gradebook course total, rounded to a whole percent.
 *  - On interims a missing grade, or a grade that rounds to 0, prints as "No grades"
 *    and does not count towards the GPA.
 *  - High school letters: A 95+, A- 90, B+ 87, B 83, B- 80, C+ 77, C 73, C- 70, D+ 67, D 60, F.
 *  - High school GPA points: 4.0, 3.8, 3.4, 3.0, 2.8, 2.4, 2.0, 1.8, 1.4, 1.0, 0.
 *  - Middle school letters and points: A 90+ (4), B 80 (3), C 70 (2), D 60 (1), F (0).
 *
 * @package    report_interims
 * @copyright  2026 Brian Pool, National Trail Local Schools
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grades {
    /** @var string High school grading scale. */
    public const SCALE_HS = 'hs';

    /** @var string Middle school grading scale. */
    public const SCALE_MS = 'ms';

    /** @var string Count only graded classes (numeric course ID number) in the GPA. */
    public const GPA_GRADED = 'graded';

    /** @var string Count every course in the GPA. */
    public const GPA_ALL = 'all';

    /** @var string Count every course that has a course ID number in the GPA. */
    public const GPA_HASIDNUMBER = 'hasidnumber';

    /** @var array High school scale: lowest rounded percent => [letter, points]. */
    public const HS = [
        95 => ['A', 4.0],
        90 => ['A-', 3.8],
        87 => ['B+', 3.4],
        83 => ['B', 3.0],
        80 => ['B-', 2.8],
        77 => ['C+', 2.4],
        73 => ['C', 2.0],
        70 => ['C-', 1.8],
        67 => ['D+', 1.4],
        60 => ['D', 1.0],
        1 => ['F', 0.0],
    ];

    /** @var array Middle school scale: lowest rounded percent => [letter, points]. */
    public const MS = [
        90 => ['A', 4.0],
        80 => ['B', 3.0],
        70 => ['C', 2.0],
        60 => ['D', 1.0],
        1 => ['F', 0.0],
    ];

    /**
     * Rounds a course total to a whole percent.
     *
     * @param mixed $finalgrade Course total from grade_grades (null when there is no grade)
     * @return int|null Rounded percent, or null when there is no grade
     */
    public static function round_grade($finalgrade): ?int {
        if ($finalgrade === null || $finalgrade === '') {
            return null;
        }
        return (int)round((float)$finalgrade);
    }

    /**
     * Does this rounded grade count as "no grade" on the interims (missing, or 0%)?
     *
     * @param int|null $rounded
     * @return bool
     */
    public static function is_no_grade(?int $rounded): bool {
        return $rounded === null || $rounded <= 0;
    }

    /**
     * Letter grade and GPA points for a rounded percent.
     *
     * @param int $rounded Rounded percent (1 or more)
     * @param string $scale One of the SCALE_ constants
     * @return array [letter, points]
     */
    protected static function lookup(int $rounded, string $scale): array {
        $table = ($scale === self::SCALE_HS) ? self::HS : self::MS;
        foreach ($table as $minimum => $result) {
            if ($rounded >= $minimum) {
                return $result;
            }
        }
        return ['F', 0.0];
    }

    /**
     * Letter grade for a rounded percent ("F" for 0 or below).
     *
     * @param int $rounded
     * @param string $scale One of the SCALE_ constants
     * @return string
     */
    public static function letter(int $rounded, string $scale): string {
        return self::lookup($rounded, $scale)[0];
    }

    /**
     * GPA points for a rounded percent.
     *
     * @param int $rounded
     * @param string $scale One of the SCALE_ constants
     * @return float
     */
    public static function points(int $rounded, string $scale): float {
        return self::lookup($rounded, $scale)[1];
    }

    /**
     * Should this course be counted in the GPA?
     *
     * @param \stdClass $row Row from student_course_grades()
     * @param string $filter One of the GPA_ constants
     * @return bool
     */
    protected static function counts_for_gpa(\stdClass $row, string $filter): bool {
        if ($filter === self::GPA_GRADED) {
            return helper::is_graded_course_idnumber($row->idnumber);
        }
        if ($filter === self::GPA_HASIDNUMBER) {
            return trim((string)$row->idnumber) !== '';
        }
        return true;
    }

    /**
     * Unweighted GPA for a list of course grades.
     *
     * @param array $rows Rows from student_course_grades()
     * @param string $scale One of the SCALE_ constants
     * @param string $filter One of the GPA_ constants
     * @return float Rounded to two decimals (0 when no course counts)
     */
    public static function gpa(array $rows, string $scale, string $filter): float {
        $total = 0.0;
        $classes = 0;
        foreach ($rows as $row) {
            if (!self::counts_for_gpa($row, $filter)) {
                continue;
            }
            $rounded = self::round_grade($row->finalgrade);
            if (self::is_no_grade($rounded)) {
                continue;
            }
            $total += self::points($rounded, $scale);
            $classes++;
        }
        return $classes ? round($total / $classes, 2) : 0.0;
    }

    /**
     * Formats a GPA for printing (two decimals).
     *
     * @param float $gpa
     * @return string
     */
    public static function format_gpa(float $gpa): string {
        return number_format($gpa, 2);
    }

    /**
     * Every visible course total for one student in courses where they have a student role.
     *
     * @param int $userid
     * @return \stdClass[] Rows with ggid, fullname, finalgrade, firstname, lastname, idnumber,
     *                     userid and studentidnumber, ordered by course name
     */
    public static function student_course_grades(int $userid): array {
        global $DB;
        $sql = "SELECT gg.id AS ggid, c.fullname, gg.finalgrade, u.firstname, u.lastname, c.idnumber,
                       u.id AS userid, u.idnumber AS studentidnumber
                  FROM {grade_grades} gg
                  JOIN {grade_items} gi ON gg.itemid = gi.id
                  JOIN {context} ci ON gi.courseid = ci.instanceid AND ci.contextlevel = :contextlevel
                  JOIN {user} u ON u.id = gg.userid
                  JOIN {course} c ON gi.courseid = c.id
                  JOIN {role_assignments} ra ON gg.userid = ra.userid AND ra.contextid = ci.id
                 WHERE gg.userid = :userid
                   AND gi.itemtype = 'course'
                   AND ra.roleid IN (" . helper::student_role_ids_csv() . ")
                   AND c.visible = 1
              ORDER BY c.fullname";
        return array_values($DB->get_records_sql($sql, ['contextlevel' => CONTEXT_COURSE, 'userid' => $userid]));
    }

    /**
     * Does the student have a visible D or F (below the cut-off) in a graded class?
     *
     * @param int $userid
     * @return bool
     */
    public static function has_df_grade(int $userid): bool {
        global $DB;
        $sql = "SELECT gg.id AS ggid, c.idnumber
                  FROM {grade_grades} gg
                  JOIN {grade_items} gi ON gg.itemid = gi.id
                  JOIN {context} ci ON gi.courseid = ci.instanceid AND ci.contextlevel = :contextlevel
                  JOIN {course} c ON gi.courseid = c.id
                  JOIN {role_assignments} ra ON gg.userid = ra.userid AND ra.contextid = ci.id
                 WHERE gg.userid = :userid
                   AND gi.itemtype = 'course'
                   AND ra.roleid IN (" . helper::student_role_ids_csv() . ")
                   AND c.visible = 1
                   AND gg.hidden = 0
                   AND gg.finalgrade < :threshold";
        $params = ['contextlevel' => CONTEXT_COURSE, 'userid' => $userid, 'threshold' => helper::df_threshold()];
        foreach ($DB->get_records_sql($sql, $params) as $row) {
            if (helper::is_graded_course_idnumber($row->idnumber)) {
                return true;
            }
        }
        return false;
    }
}
