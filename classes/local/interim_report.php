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
use moodle_url;
use required_capability_exception;
use stdClass;

/**
 * The one-page-per-student parent reports: HS/MS interims, MS interims with signature
 * and the HS/MS D/F mailouts, each optionally with the student's SSID.
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2026 Brian Pool (version 3.0)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class interim_report {
    /** @var string High school interims. */
    public const HS = 'hs';

    /** @var string Middle school interims. */
    public const MS = 'ms';

    /** @var string Middle school interims with signature block. */
    public const MS_SIGNATURE = 'mssig';

    /** @var string High school D/F mailout. */
    public const HS_DF = 'hsdf';

    /** @var string Middle school D/F mailout. */
    public const MS_DF = 'msdf';

    /** @var string Width of every grade table. */
    public const TABLEWIDTH = '660px';

    /** @var string Report variant (one of the constants above). */
    protected $variant;

    /** @var string School name printed under the logo. */
    protected $title;

    /** @var string "Grades printed on ..." text. */
    protected $printdate;

    /**
     * Constructor.
     *
     * @param string $variant One of the class constants
     */
    public function __construct(string $variant) {
        $this->variant = $variant;
        $this->title = helper::school_name();
        $this->printdate = helper::printed_on_string();
    }

    /**
     * The school level a variant belongs to.
     *
     * @param string $variant
     * @return string helper::LEVEL_ constant
     */
    public static function level_for(string $variant): string {
        return in_array($variant, [self::HS, self::HS_DF]) ? helper::LEVEL_HS : helper::LEVEL_MS;
    }

    /**
     * Grading scale for this variant.
     *
     * @return string grades::SCALE_ constant
     */
    protected function scale(): string {
        return in_array($this->variant, [self::HS, self::HS_DF]) ? grades::SCALE_HS : grades::SCALE_MS;
    }

    /**
     * Which courses count towards the GPA for this variant (as in the original reports).
     *
     * @return string grades::GPA_ constant
     */
    protected function gpa_filter(): string {
        switch ($this->variant) {
            case self::HS:
            case self::MS:
                return grades::GPA_GRADED;
            case self::MS_SIGNATURE:
                return grades::GPA_HASIDNUMBER;
            default:
                return grades::GPA_ALL;
        }
    }

    /**
     * Is this a D/F mailout (only students with a D or F get a page)?
     *
     * @return bool
     */
    protected function is_df(): bool {
        return in_array($this->variant, [self::HS_DF, self::MS_DF]);
    }

    /**
     * Prints the whole report for a course: checks, toolbar and one page per student.
     *
     * The caller must already have called require_login($course).
     *
     * @param stdClass $course
     * @param string $script File name of the calling script in /report/interims/reports/
     * @param string $variant One of the class constants
     */
    public static function run(stdClass $course, string $script, string $variant): void {
        global $PAGE;

        $isdf = in_array($variant, [self::HS_DF, self::MS_DF]);
        $userid = $isdf ? 0 : optional_param('userid', 0, PARAM_INT);
        $showssid = optional_param('ssid', 0, PARAM_BOOL);

        $context = context_course::instance($course->id);
        require_capability('report/interims:view', $context);
        helper::require_level($course, $context, [self::level_for($variant)]);
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
        $PAGE->set_url(new moodle_url('/report/interims/reports/' . $script, $urlparams));
        $PAGE->set_pagelayout('print');
        helper::set_show_ssid((bool)$showssid);

        $intro = get_string('interimintro', 'report_interims');
        if ($showssid) {
            $intro .= '<br>' . get_string('ssidintro', 'report_interims');
        }
        echo helper::print_page_start($course->id, $intro);

        $report = new self($variant);
        if ($userid) {
            $report->print_student($userid, 1);
            return;
        }

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
        if (!$students) {
            echo '<p>' . get_string('nostudents', 'report_interims') . '</p>';
            return;
        }
        $remaining = count($students);
        foreach ($students as $student) {
            $report->print_student($student->id, $remaining);
            $remaining--;
        }
    }

    /**
     * Prints one student's page (nothing for D/F mailouts when the student has no D or F).
     *
     * @param int $userid
     * @param int $remaining Students still to print, including this one (no page break after the last)
     */
    public function print_student(int $userid, int $remaining): void {
        if ($this->is_df() && !grades::has_df_grade($userid)) {
            return;
        }
        $rows = grades::student_course_grades($userid);
        if (!$rows) {
            return;
        }
        $first = reset($rows);
        if (helper::is_excluded_lastname($first->lastname)) {
            return;
        }

        $gpa = grades::format_gpa(grades::gpa($rows, $this->scale(), $this->gpa_filter()));
        $gpaline = get_string('currentgpa', 'report_interims', $gpa);

        echo $this->header_html($first);
        foreach ($rows as $row) {
            if (helper::is_graded_course_idnumber($row->idnumber)) {
                echo $this->row_html($row);
            }
        }

        if ($this->is_df()) {
            // D/F mailouts print the GPA before the printed-on line.
            echo '<div style="text-align: center; font-size: medium;">' . $gpaline . '</div>';
            echo $this->printed_html($first);
        } else if ($this->variant === self::MS_SIGNATURE) {
            echo $this->printed_html($first);
            echo '<div style="text-align: center; font-size: medium;"><br>' . $gpaline . '<br><br></div>';
            echo $this->signature_html();
        } else {
            echo $this->printed_html($first);
            echo '<div style="text-align: center; font-size: medium;">' . $gpaline . '</div>';
        }
        echo '<hr>';
        if ($remaining > 1) {
            echo '<div class="page-break" style="page-break-before: always;"></div>';
        }
    }

    /**
     * Logo, school name, student name, optional SSID and the table heading.
     *
     * @param stdClass $student Row from grades::student_course_grades()
     * @return string HTML
     */
    protected function header_html(stdClass $student): string {
        $name = s($student->firstname . ' ' . $student->lastname);
        $html = '<div style="text-align: center;">';
        $html .= '<div style="font-size: x-large;">' . helper::logo_html() . '<br>' . s($this->title) . '</div>';
        $html .= '<div style="font-size: medium;">' . get_string('currentgradereportfor', 'report_interims', $name);
        if (helper::show_ssid()) {
            $html .= helper::ssid_html((int)$student->userid);
        }
        $html .= '<br><br></div></div>';
        $html .= $this->table_start();
        $html .= '<tr>';
        $html .= '<td width="70px" bgcolor="#C0C0C0"><b>' . get_string('coursenumber', 'report_interims') . '</b></td>';
        $html .= '<td width="400px" bgcolor="#C0C0C0"><b>' . get_string('course') . '</b></td>';
        $html .= '<td width="100px" bgcolor="#C0C0C0"><b>' . get_string('currentpercentage', 'report_interims') . '</b></td>';
        $html .= '<td width="100px" bgcolor="#C0C0C0"><b>' . get_string('currentletter', 'report_interims') . '</b></td>';
        $html .= '</tr></table>';
        return $html;
    }

    /**
     * Opening tag of a centred grade table.
     *
     * @return string HTML
     */
    protected function table_start(): string {
        return '<table border="1" cellpadding="1" width="' . self::TABLEWIDTH . '" ' .
            'style="margin-left: auto; margin-right: auto;">';
    }

    /**
     * One course row (its own bordered table, so the rows stack like the original reports).
     *
     * @param stdClass $row Row from grades::student_course_grades()
     * @return string HTML
     */
    protected function row_html(stdClass $row): string {
        $rounded = grades::round_grade($row->finalgrade);
        if (grades::is_no_grade($rounded)) {
            $percent = '';
            $letter = get_string('nogradesyet', 'report_interims');
        } else {
            $percent = (string)$rounded;
            $letter = grades::letter($rounded, $this->scale());
        }
        $html = $this->table_start() . '<tr>';
        $html .= '<td width="70px" height="35px">' . s($row->idnumber) . '</td>';
        $html .= '<td width="400px">' . s($row->fullname) . '</td>';
        $html .= '<td width="100px">' . s($percent) . ' % </td>';
        $html .= '<td width="100px">' . s($letter) . '</td>';
        $html .= '</tr></table>';
        return $html;
    }

    /**
     * "Grades printed on ... for Last, First".
     *
     * @param stdClass $student
     * @return string HTML
     */
    protected function printed_html(stdClass $student): string {
        return '<div style="text-align: center; font-size: medium;">' . s($this->printdate) . ' ' .
            get_string('printedfor', 'report_interims', s($student->lastname . ', ' . $student->firstname)) . '</div>';
    }

    /**
     * Parent/student signature box used by the MS interims with signature.
     *
     * @return string HTML
     */
    protected function signature_html(): string {
        $line = 'border-bottom: solid 1px;';
        $small = 'font-size: 6px; text-align: center; vertical-align: bottom;';
        $html = '<table border="1" cellpadding="1" width="' . self::TABLEWIDTH . '" ' .
            'style="margin-left: auto; margin-right: auto;"><tr><td style="text-align: center;">';
        $html .= '<table border="0" width="650" cellspacing="0" style="font-size: 14px; border-top: solid 1px;"><tr>';
        $html .= '<td width="200" height="30" style="border-right: solid 1px; border-left: solid 1px; ' . $line .
            ' font-weight: bold; background-color: lightgray;">' .
            get_string('parentstudentsignature', 'report_interims') . '</td>';
        $html .= '<td width="110" style="' . $line . ' ' . $small . '">' .
            get_string('parentguardian', 'report_interims') . '</td>';
        $html .= '<td width="110" style="' . $line . '">&nbsp;</td>';
        $html .= '<td width="60" style="' . $line . '">&nbsp;</td>';
        $html .= '<td style="border-right: solid 1px; ' . $line . '">&nbsp;</td>';
        $html .= '<td width="60" style="' . $line . ' ' . $small . '">' .
            get_string('student', 'report_interims') . '</td>';
        $html .= '<td width="60" style="' . $line . '">&nbsp;</td>';
        $html .= '<td width="60" style="' . $line . '">&nbsp;</td>';
        $html .= '<td width="60" style="border-right: solid 1px; ' . $line . '">&nbsp;</td>';
        $html .= '</tr></table></td></tr></table>';
        return $html;
    }
}
