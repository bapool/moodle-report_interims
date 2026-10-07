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
use context_system;
use grade_scale;
use moodle_exception;
use moodle_url;
use required_capability_exception;
use stdClass;

/**
 * Combined elementary gradecard: the class report card on the front and the specials
 * report card on the back, two printed pages per student, for secure document delivery.
 *
 * Elementary grades are Moodle outcomes ("indicators"). Each outcome short name starts with
 * the quarter number and a two letter subject code, for example "2-LA-03" or "3.PE.01":
 *  - Front (class): LA Language/Literacy, MA Mathematics, SC Science, SO Social Studies,
 *    XB Behavior and Attendance.
 *  - Back (specials): AR Art, MU Music, PE Physical Education.
 * The outcome description is the "I can ..." statement that is printed.
 *
 * Every quarter that has grades is printed; quarters with no grades are left out completely.
 * The class side prints two quarters per page; the specials side prints all quarters on one page.
 * The front is titled with the student's primary class: the course where most of their class
 * (LA/MA/SC/SO) indicators are graded.
 *
 * @package    report_interims
 * @copyright  2026 Brian Pool, National Trail Local Schools
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class es_gradecard {
    /** @var string[] Subject codes on the front, in printing order. */
    public const FRONT_SUBJECTS = ['LA', 'MA', 'SC', 'SO', 'XB'];

    /** @var string[] Subject codes used to find the primary class. */
    public const CLASS_SUBJECTS = ['LA', 'MA', 'SC', 'SO'];

    /** @var string[] Subject codes on the back (specials), in printing order. */
    public const BACK_SUBJECTS = ['AR', 'MU', 'PE'];

    /** @var string[] Language string for each subject code. */
    public const SUBJECT_STRINGS = [
        'LA' => 'subjectla',
        'MA' => 'subjectma',
        'SC' => 'subjectsc',
        'SO' => 'subjectso',
        'XB' => 'subjectxb',
        'AR' => 'subjectar',
        'MU' => 'subjectmu',
        'PE' => 'subjectpe',
    ];

    /** @var array Cache of loaded grade scales. */
    protected $scales = [];

    /** @var string "Grades printed on ..." text. */
    protected $printdate;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->printdate = get_string(
            'gradesexported',
            'report_interims',
            userdate(time(), get_string('strftimeprinted', 'report_interims'))
        );
    }

    /**
     * Prints the gradecards for the students of a course. The caller must already have called require_login($course).
     *
     * @param stdClass $course
     */
    public static function run(stdClass $course): void {
        global $PAGE;

        $userid = optional_param('userid', 0, PARAM_INT);
        $showssid = optional_param('ssid', 0, PARAM_BOOL);

        $context = context_course::instance($course->id);
        require_capability('report/interims:view', $context);
        helper::require_level($course, $context, [helper::LEVEL_ELEM]);
        if ($showssid && !helper::can_view_admin_reports($context)) {
            throw new required_capability_exception($context, 'report/interims:viewadminreports', 'nopermissions', '');
        }
        if ($userid && !is_enrolled($context, $userid)) {
            throw new moodle_exception('invaliduser', 'report_interims');
        }

        $urlparams = ['id' => $course->id];
        if ($showssid) {
            $urlparams['ssid'] = 1;
        }
        $PAGE->set_url(new moodle_url('/report/interims/reports/esgradecard.php', $urlparams));
        $PAGE->set_pagelayout('print');
        helper::set_show_ssid((bool)$showssid);

        $intro = get_string('esgradecardintro', 'report_interims');
        if ($showssid) {
            $intro .= '<br>' . get_string('ssidintro', 'report_interims');
        }
        echo helper::print_page_start($course->id, $intro);
        echo self::styles();

        if ($userid) {
            $students = [(object)['id' => $userid]];
        } else {
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
            $students = array_filter($students, function ($student) {
                return !helper::is_excluded_lastname($student->lastname);
            });
        }

        $card = new self();
        $printed = 0;
        $skipped = [];
        foreach ($students as $student) {
            $html = $card->student_html((int)$student->id, $printed > 0);
            if ($html === '') {
                $skipped[] = fullname(\core_user::get_user($student->id));
                continue;
            }
            echo $html;
            $printed++;
        }
        echo self::fit_script();
        if ($skipped) {
            echo '<div class="noprint" style="margin-top: 20px;"><p>' .
                get_string('esnogradesfor', 'report_interims', s(implode(', ', $skipped))) . '</p></div>';
        }
        if (!$printed && !$skipped) {
            echo '<p>' . get_string('nostudents', 'report_interims') . '</p>';
        }
    }

    /**
     * Both pages for one student, or an empty string when the student has no elementary grades.
     *
     * @param int $userid
     * @param bool $pagebreak Start on a new page (every student after the first)
     * @return string HTML
     */
    public function student_html(int $userid, bool $pagebreak): string {
        $rows = $this->outcome_rows($userid);
        $front = $this->select_indicators($rows, self::FRONT_SUBJECTS);
        $back = $this->select_indicators($rows, self::BACK_SUBJECTS);
        if (!$front && !$back) {
            return '';
        }
        $user = \core_user::get_user($userid, 'id, firstname, lastname');
        $class = $this->primary_class($rows);

        // Front: two quarters side by side per page; the full header on the first page only.
        $frontpages = $front ? array_chunk($front, 2, true) : [[]];
        $html = '';
        foreach ($frontpages as $index => $pagequarters) {
            $html .= '<div class="esgc-page' . (($pagebreak || $index > 0) ? ' esgc-break' : '') . '">';
            if ($index === 0) {
                $html .= $this->front_header_html($user, $class);
            } else {
                $html .= '<div class="esgc-contname">' . s($user->lastname . ', ' . $user->firstname) .
                    ($class !== '' ? ' - ' . $class : '') . '</div>';
            }
            $html .= $this->quarters_html($pagequarters, true);
            $html .= $this->printed_html($user);
            $html .= '</div>';
        }

        $html .= '<div class="esgc-page esgc-break">';
        $html .= '<div class="esgc-backtitle">' . get_string('specialsreportcard', 'report_interims') . '</div>';
        $html .= '<div class="esgc-backname">' . s($user->lastname . ', ' . $user->firstname) . '</div>';
        $html .= $this->quarters_html($back, false);
        $html .= $this->comment_codes_html();
        $html .= $this->printed_html($user);
        $html .= '</div>';
        return $html;
    }

    /**
     * Every outcome grade of the student in visible courses, newest first per outcome.
     *
     * @param int $userid
     * @return stdClass[]
     */
    protected function outcome_rows(int $userid): array {
        global $DB;
        $sql = "SELECT gg.id AS ggid, go.id AS outcomeid, go.shortname, go.description, go.descriptionformat,
                       go.scaleid, gg.finalgrade, gg.timemodified, c.id AS courseid, c.fullname AS coursename
                  FROM {grade_grades} gg
                  JOIN {grade_items} gi ON gi.id = gg.itemid
                  JOIN {grade_outcomes} go ON go.id = gi.outcomeid
                  JOIN {course} c ON c.id = gi.courseid
                 WHERE gg.userid = :userid
                   AND c.visible = 1
              ORDER BY go.shortname ASC, gg.timemodified DESC";
        return array_values($DB->get_records_sql($sql, ['userid' => $userid]));
    }

    /**
     * Quarter number and subject code from an outcome short name such as "2-LA-03".
     *
     * @param string $shortname
     * @return array|null [quarter, subject] or null when the name does not follow the pattern
     */
    public static function parse_shortname(string $shortname): ?array {
        $quarter = (int)substr($shortname, 0, 1);
        $subject = strtoupper(substr($shortname, 2, 2));
        if ($quarter < 1 || $quarter > 4 || !isset(self::SUBJECT_STRINGS[$subject])) {
            return null;
        }
        return [$quarter, $subject];
    }

    /**
     * The indicators to print for one side, grouped by quarter.
     *
     * Keeps one row per outcome (a graded row before an ungraded one, then the newest), and only the
     * quarters that have at least one grade.
     *
     * @param stdClass[] $rows From outcome_rows()
     * @param string[] $subjects Subject codes for this side
     * @return array quarter => list of rows (subject, description, grade text), in printing order
     */
    protected function select_indicators(array $rows, array $subjects): array {
        $byoutcome = [];
        foreach ($rows as $row) {
            $parsed = self::parse_shortname((string)$row->shortname);
            if (!$parsed || !in_array($parsed[1], $subjects)) {
                continue;
            }
            $current = $byoutcome[$row->outcomeid] ?? null;
            if ($current === null || ($current->finalgrade === null && $row->finalgrade !== null)) {
                $row->quarter = $parsed[0];
                $row->subject = $parsed[1];
                $byoutcome[$row->outcomeid] = $row;
            }
        }

        $graded = [];
        foreach ($byoutcome as $row) {
            if ($row->finalgrade !== null) {
                $graded[$row->quarter] = true;
            }
        }
        if (!$graded) {
            return [];
        }
        $quarters = array_keys($graded);
        sort($quarters);

        $result = [];
        foreach ($quarters as $quarter) {
            $list = array_filter($byoutcome, function ($row) use ($quarter) {
                return $row->quarter === $quarter;
            });
            usort($list, function ($a, $b) use ($subjects) {
                $order = array_search($a->subject, $subjects) <=> array_search($b->subject, $subjects);
                return $order ?: strcmp($a->shortname, $b->shortname);
            });
            $result[$quarter] = $list;
        }
        return $result;
    }

    /**
     * The student's primary class: the course with the most graded class (LA/MA/SC/SO) indicators.
     *
     * @param stdClass[] $rows From outcome_rows()
     * @return string Course full name, or empty when there are no class indicators
     */
    protected function primary_class(array $rows): string {
        $counts = [];
        $names = [];
        foreach ($rows as $row) {
            $parsed = self::parse_shortname((string)$row->shortname);
            if (!$parsed || !in_array($parsed[1], self::CLASS_SUBJECTS)) {
                continue;
            }
            $counts[$row->courseid] = ($counts[$row->courseid] ?? 0) + ($row->finalgrade !== null ? 1 : 0);
            $names[$row->courseid] = $row->coursename;
        }
        if (!$counts) {
            return '';
        }
        arsort($counts);
        $courseid = array_key_first($counts);
        return format_string($names[$courseid], true, ['context' => context_course::instance($courseid)]);
    }

    /**
     * Text printed in the grade column: the whole value for numbers (attendance), else the first letter.
     *
     * @param stdClass $row
     * @return string
     */
    protected function grade_text(stdClass $row): string {
        if ($row->finalgrade === null || $row->finalgrade === '') {
            return '_';
        }
        if (!isset($this->scales[$row->scaleid])) {
            $this->scales[$row->scaleid] = grade_scale::fetch(['id' => $row->scaleid]);
        }
        $scale = $this->scales[$row->scaleid];
        if (!$scale) {
            return '_';
        }
        $item = trim((string)$scale->get_nearest_item($row->finalgrade));
        if (is_numeric($item)) {
            return $item;
        }
        return \core_text::strtoupper(\core_text::substr($item, 0, 1));
    }

    /**
     * School year text, for example "2026 - 2027" (a new school year starts in August).
     *
     * @return string
     */
    public static function school_year(): string {
        $date = usergetdate(time());
        $start = ($date['mon'] >= 8) ? $date['year'] : $date['year'] - 1;
        return $start . ' - ' . ($start + 1);
    }

    /**
     * Front page header: logo, name, class, SSID, school, progress marks and school year.
     *
     * @param stdClass $user
     * @param string $class Primary class name
     * @return string HTML
     */
    protected function front_header_html(stdClass $user, string $class): string {
        $name = s($user->lastname . ', ' . $user->firstname);
        $namesize = (\core_text::strlen($user->lastname . ', ' . $user->firstname) > 20) ? '20px' : '24px';
        $html = '<table class="esgc-header"><tr><td class="esgc-headleft">';
        $html .= '<div>' . helper::logo_html() . ' <span style="font-size: ' . $namesize . ';">' . $name . '</span></div>';
        if ($class !== '') {
            $html .= '<div class="esgc-class">' . $class . '</div>';
        }
        $elemschool = trim((string)get_config('report_interims', 'elemschoolname'));
        $school = ($elemschool !== '') ? format_string($elemschool) : s(helper::school_name());
        if ($school !== '') {
            $html .= '<div class="esgc-school">' . $school . '</div>';
        }
        if (helper::show_ssid()) {
            $ssid = helper::get_ssid((int)$user->id);
            $label = get_string('studentid', 'report_interims') . ': ';
            if ($ssid === '') {
                $html .= '<div class="esgc-ssid">' . $label . '<span class="esgc-missing">' .
                    get_string('ssidnotonfile', 'report_interims') . '</span></div>';
            } else {
                $html .= '<div class="esgc-ssid">' . $label . s($ssid) . '</div>';
            }
        }
        $html .= '</td><td class="esgc-headright">';
        $html .= '<div class="esgc-key"><b>' . get_string('progressmarks', 'report_interims') . '</b><br>' .
            get_string('progressmarkm', 'report_interims') . '<br>' .
            get_string('progressmarkp', 'report_interims') . '<br>' .
            get_string('progressmarkl', 'report_interims') . '</div>';
        $html .= '<div class="esgc-year">' . self::school_year() . '</div>';
        $html .= '</td></tr></table>';
        return $html;
    }

    /**
     * The quarters side by side.
     *
     * @param array $quarters From select_indicators()
     * @param bool $front True for the class side (bold marks), false for specials
     * @return string HTML
     */
    protected function quarters_html(array $quarters, bool $front): string {
        if (!$quarters) {
            return '<p class="esgc-none">' . get_string('nogradesrecorded', 'report_interims') . '</p>';
        }
        $html = '';
        foreach (array_chunk($quarters, 2, true) as $pair) {
            $html .= '<table class="esgc-quarters"><tr>';
            foreach ($pair as $quarter => $rows) {
                $html .= $this->quarter_html($quarter, $rows, $front);
            }
            if (count($pair) === 1) {
                $html .= '<td class="esgc-quarter esgc-empty"></td>';
            }
            $html .= '</tr></table>';
        }
        return $html;
    }

    /**
     * One quarter column.
     *
     * @param int $quarter Quarter number
     * @param stdClass[] $rows Indicators in printing order
     * @param bool $front True for the class side (bold marks), false for specials
     * @return string HTML
     */
    protected function quarter_html(int $quarter, array $rows, bool $front): string {
        $html = '<td class="esgc-quarter"><div class="esgc-qtitle">' .
            get_string('quarter' . $quarter, 'report_interims') . '</div><table class="esgc-list">';
        $subject = '';
        foreach ($rows as $row) {
            if ($row->subject !== $subject) {
                $subject = $row->subject;
                $html .= '<tr><td class="esgc-subject">' .
                    get_string(self::SUBJECT_STRINGS[$subject], 'report_interims') .
                    '</td><td class="esgc-subjectmark"></td></tr>';
            }
            $description = format_text(
                (string)$row->description,
                (int)$row->descriptionformat,
                ['context' => context_system::instance(), 'para' => false]
            );
            $html .= '<tr><td class="esgc-item">' . $description . '</td>';
            $html .= '<td class="esgc-mark' . ($front ? ' esgc-bold' : '') . '">' . s($this->grade_text($row)) .
                '</td></tr>';
        }
        return $html . '</table></td>';
    }

    /**
     * Behavior and work habits comment codes printed on the specials side.
     *
     * @return string HTML
     */
    protected function comment_codes_html(): string {
        $html = '<table class="esgc-codes"><tr><th colspan="2">' .
            get_string('specialscodes', 'report_interims') . '</th></tr>';
        for ($i = 1; $i <= 6; $i += 2) {
            $html .= '<tr><td>' . get_string('specialscode' . $i, 'report_interims') . '</td><td>' .
                get_string('specialscode' . ($i + 1), 'report_interims') . '</td></tr>';
        }
        return $html . '</table>';
    }

    /**
     * "Grades printed on ... for Last, First".
     *
     * @param stdClass $user
     * @return string HTML
     */
    protected function printed_html(stdClass $user): string {
        return '<div class="esgc-printed">' . s($this->printdate) . ' ' .
            get_string('printedfor', 'report_interims', s($user->lastname . ', ' . $user->firstname)) . '</div>';
    }

    /**
     * Shrinks any page that is too tall so every side fits on one printed page.
     *
     * Pages are laid out 690px wide; a page taller than the printable height of a Letter or A4
     * sheet (with the margins set in the styles: 0.6in top, 0.4in elsewhere) is scaled down with CSS zoom.
     *
     * @return string HTML
     */
    protected static function fit_script(): string {
        return '<script>
            (function() {
                var maxheight = 940;
                var pages = document.querySelectorAll(".esgc-page");
                for (var i = 0; i < pages.length; i++) {
                    var height = pages[i].offsetHeight;
                    if (height > maxheight) {
                        pages[i].style.zoom = (maxheight / height).toFixed(3);
                    }
                }
            })();
        </script>';
    }

    /**
     * Styles for the gradecards (screen and print).
     *
     * @return string HTML
     */
    protected static function styles(): string {
        return '<style>
            @page { margin: 0.5in; }
            .esgc-page { width: 690px; margin: 0 auto; font-family: serif; color: #000; line-height: 1.15;
                -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @media print { .esgc-break { page-break-before: always; } }
            @media screen { .esgc-break { margin-top: 40px; border-top: 2px dashed #999; padding-top: 20px; } }
            .esgc-header { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
            .esgc-header td { vertical-align: top; padding: 1px 2px; }
            .esgc-headleft { text-align: left; }
            .esgc-headleft img { height: 44px !important; vertical-align: bottom; }
            .esgc-class { font-size: 17px; }
            .esgc-school { font-size: 16px; }
            .esgc-ssid { font-size: 13px; }
            .esgc-missing { color: #b00020; }
            .esgc-headright { text-align: center; width: 260px; }
            .esgc-key { border: 1px dotted #000; text-align: left; font-size: 12px; padding: 1px 4px; display: inline-block; }
            .esgc-key b { font-size: 14px; }
            .esgc-year { font-size: 28px; margin-top: 2px; }
            .esgc-quarters { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 6px; }
            .esgc-quarter { border: 1px solid #000; vertical-align: top; padding: 4px; width: 50%; }
            .esgc-empty { border: none; }
            .esgc-qtitle { background: lightgray; text-align: center; font-size: 15px; padding: 1px; margin-bottom: 1px; }
            .esgc-list { width: 100%; border-collapse: collapse; }
            .esgc-subject { font-weight: bold; font-size: 13px; border-bottom: 1px solid #000; padding: 4px 2px 1px 2px; }
            .esgc-subjectmark { border-bottom: 1px solid #000; border-left: 1px dotted #000; }
            .esgc-item { font-size: 12px; border-bottom: 1px dotted #000; padding: 1px 2px; }
            .esgc-mark { border-bottom: 1px dotted #000; border-left: 1px dotted #000; text-align: center;
                width: 34px; font-size: 13px; }
            .esgc-bold { font-weight: bold; }
            .esgc-backtitle { font-size: 20px; text-align: center; margin-bottom: 1px; }
            .esgc-backname { font-size: 12px; text-align: center; margin-bottom: 6px; }
            .esgc-contname { font-size: 18px; text-align: center; margin-bottom: 6px; }
            .esgc-codes { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 12px; }
            .esgc-codes th { background: lightgray; border: 1px solid #000; padding: 1px; }
            .esgc-codes td { border: 1px solid #000; padding: 1px 4px; width: 50%; }
            .esgc-printed { font-size: 10px; text-align: center; margin-top: 4px; }
            .esgc-none { text-align: center; font-style: italic; padding: 20px; border: 1px solid #000; }
        </style>';
    }
}
