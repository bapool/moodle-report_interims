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

use context;
use context_system;
use core_course_category;
use html_writer;
use moodle_exception;
use moodle_url;
use stdClass;

/**
 * Shared helper functions for the Interims report plugin.
 *
 * Everything that used to be hard coded for one school district (student role id,
 * test account name, D/F cut-off, school logo, school level by category, SSID field)
 * is read from the plugin settings through this class.
 *
 * @package    report_interims
 * @copyright  2026 Brian Pool, National Trail Local Schools
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /** @var string High school level key. */
    public const LEVEL_HS = 'hs';

    /** @var string Middle school level key. */
    public const LEVEL_MS = 'ms';

    /** @var string Elementary school level key. */
    public const LEVEL_ELEM = 'elem';

    /** @var bool Whether the SSID row should be printed under the report heading. */
    protected static $showssid = false;

    /** @var array Per-request cache of SSID values keyed by user id. */
    protected static $ssidcache = [];

    /**
     * All school levels with the settings that control them.
     *
     * @return array level => [enable setting, category setting]
     */
    public static function levels(): array {
        return [
            self::LEVEL_HS => ['enablehsreports', 'hscategories'],
            self::LEVEL_MS => ['enablemsreports', 'mscategories'],
            self::LEVEL_ELEM => ['enableelemreports', 'elemcategories'],
        ];
    }

    /**
     * Is a school level switched on in the plugin settings?
     *
     * @param string $level One of the LEVEL_ constants
     * @return bool
     */
    public static function level_enabled(string $level): bool {
        $levels = self::levels();
        if (!isset($levels[$level])) {
            return false;
        }
        $value = get_config('report_interims', $levels[$level][0]);
        // Not configured yet means enabled (pre-3.0 behaviour).
        return ($value === false) ? true : !empty($value);
    }

    /**
     * Category ids assigned to a school level.
     *
     * @param string $level One of the LEVEL_ constants
     * @return int[]
     */
    public static function level_categories(string $level): array {
        $levels = self::levels();
        if (!isset($levels[$level])) {
            return [];
        }
        $value = get_config('report_interims', $levels[$level][1]);
        if (empty($value)) {
            return [];
        }
        return array_filter(array_map('intval', explode(',', $value)));
    }

    /**
     * Have any categories been assigned to any school level?
     *
     * @return bool
     */
    public static function categories_configured(): bool {
        foreach (array_keys(self::levels()) as $level) {
            if (self::level_categories($level)) {
                return true;
            }
        }
        return false;
    }

    /**
     * The category id of the course and all of its parent categories.
     *
     * @param stdClass $course
     * @return int[]
     */
    public static function course_category_path(stdClass $course): array {
        global $DB;
        if (empty($course->category)) {
            return [];
        }
        $path = $DB->get_field('course_categories', 'path', ['id' => $course->category]);
        if (!$path) {
            return [(int)$course->category];
        }
        return array_filter(array_map('intval', explode('/', $path)));
    }

    /**
     * Can this user see every school level regardless of course category?
     *
     * @param context $context Course context
     * @return bool
     */
    public static function can_view_all_levels(context $context): bool {
        return is_siteadmin() || has_capability('report/interims:viewallschools', $context);
    }

    /**
     * Can this user see the SSID reports and the administrative (LOA) reports?
     *
     * True for site administrators and anyone with report/interims:viewadminreports
     * in this course (managers by default, whether assigned in the course, category or site).
     *
     * @param context $context Course context
     * @return bool
     */
    public static function can_view_admin_reports(context $context): bool {
        return is_siteadmin() || has_capability('report/interims:viewadminreports', $context);
    }

    /**
     * The school levels that should be offered in this course.
     *
     * Rules:
     *  - Disabled levels are never shown.
     *  - Site admins, and anyone with report/interims:viewallschools, see every enabled level.
     *  - If no categories have been mapped yet, every enabled level is shown (pre-3.0 behaviour).
     *  - Otherwise only the level(s) whose mapped category contains this course are shown.
     *
     * @param stdClass $course
     * @param context $context Course context
     * @return string[] Level keys
     */
    public static function visible_levels(stdClass $course, context $context): array {
        $enabled = array_values(array_filter(array_keys(self::levels()), [self::class, 'level_enabled']));
        if (self::can_view_all_levels($context) || !self::categories_configured()) {
            return $enabled;
        }
        $path = self::course_category_path($course);
        $visible = [];
        foreach ($enabled as $level) {
            if (array_intersect($path, self::level_categories($level))) {
                $visible[] = $level;
            }
        }
        return $visible;
    }

    /**
     * Stop the script unless one of the given levels is available in this course.
     *
     * @param stdClass $course
     * @param context $context
     * @param string[] $levels Any one of these levels is enough
     * @throws moodle_exception
     */
    public static function require_level(stdClass $course, context $context, array $levels): void {
        if (!array_intersect($levels, self::visible_levels($course, $context))) {
            throw new moodle_exception(
                'reportnotavailable',
                'report_interims',
                new moodle_url('/report/interims/index.php', ['id' => $course->id])
            );
        }
    }

    /**
     * Comma separated list of student role ids, safe to put in SQL (integers only).
     *
     * @return string
     */
    public static function student_role_ids_csv(): string {
        global $DB;
        $value = get_config('report_interims', 'studentroles');
        $ids = [];
        if (!empty($value)) {
            $ids = array_filter(array_map('intval', explode(',', $value)));
        }
        if (!$ids) {
            $ids = array_keys($DB->get_records('role', ['archetype' => 'student'], '', 'id'));
        }
        if (!$ids) {
            $ids = [0];
        }
        return implode(',', array_map('intval', $ids));
    }

    /**
     * Is this a test/placeholder account that must not get a report?
     *
     * @param string|null $lastname
     * @return bool
     */
    public static function is_excluded_lastname(?string $lastname): bool {
        $exclude = trim((string)get_config('report_interims', 'excludelastname'));
        if ($exclude === '' || $lastname === null) {
            return false;
        }
        return \core_text::strtolower(trim($lastname)) === \core_text::strtolower($exclude);
    }

    /**
     * Course percentage below which a grade is reported as a D/F.
     *
     * @return float
     */
    public static function df_threshold(): float {
        $value = get_config('report_interims', 'dfthreshold');
        if ($value === false || $value === '' || !is_numeric($value)) {
            return 68.5;
        }
        return (float)$value;
    }

    /**
     * Does the course ID number start with a number greater than 1?
     *
     * Only courses with a numeric ID number are treated as graded classes
     * (homerooms, clubs and other non-graded courses are skipped).
     *
     * @param string|null $idnumber
     * @return bool
     */
    public static function is_graded_course_idnumber(?string $idnumber): bool {
        return intval(substr((string)$idnumber, 0, 4)) > 1;
    }

    /**
     * Is this course included in the D/F and no-grades lists?
     *
     * Uses the course ID number pattern from the settings (a regular expression,
     * default ^[0-9]{3,4}[A-Za-z]{2}$ e.g. 1234AB). An empty pattern includes every course.
     *
     * @param string|null $idnumber
     * @return bool
     */
    public static function is_df_course_idnumber(?string $idnumber): bool {
        $pattern = get_config('report_interims', 'dfcoursepattern');
        if ($pattern === false) {
            $pattern = '^[0-9]{3,4}[A-Za-z]{2}$';
        }
        $pattern = trim($pattern);
        if ($pattern === '') {
            return true;
        }
        $result = @preg_match('~' . str_replace('~', '\\~', $pattern) . '~', (string)$idnumber);
        if ($result === false) {
            debugging('report_interims: invalid course ID number pattern ' . s($pattern), DEBUG_DEVELOPER);
            return true;
        }
        return $result === 1;
    }

    /**
     * Switch the SSID row on or off for the current request.
     *
     * @param bool $show
     */
    public static function set_show_ssid(bool $show): void {
        self::$showssid = $show;
    }

    /**
     * Is the SSID row switched on for the current request?
     *
     * @return bool
     */
    public static function show_ssid(): bool {
        return self::$showssid;
    }

    /**
     * Options for the SSID source setting.
     *
     * @return array
     */
    public static function ssid_field_options(): array {
        global $DB;
        $options = [
            'idnumber' => get_string('idnumber'),
            'username' => get_string('username'),
        ];
        try {
            $fields = $DB->get_records('user_info_field', null, 'sortorder', 'id, shortname, name');
            foreach ($fields as $field) {
                $options['profile_field_' . $field->shortname] = get_string(
                    'ssidprofilefield',
                    'report_interims',
                    format_string($field->name)
                );
            }
        } catch (\Throwable $e) {
            // During install the table may not be ready; core fields are enough.
            debugging($e->getMessage(), DEBUG_DEVELOPER);
        }
        return $options;
    }

    /**
     * The SSID for a student, read from the field chosen in the settings.
     *
     * @param int $userid
     * @return string Empty string when not on file
     */
    public static function get_ssid(int $userid): string {
        global $DB;
        if (array_key_exists($userid, self::$ssidcache)) {
            return self::$ssidcache[$userid];
        }
        $field = get_config('report_interims', 'ssidfield');
        if (empty($field)) {
            $field = 'idnumber';
        }
        $value = '';
        if ($field === 'idnumber' || $field === 'username') {
            $value = (string)$DB->get_field('user', $field, ['id' => $userid]);
        } else if (strpos($field, 'profile_field_') === 0) {
            $shortname = substr($field, strlen('profile_field_'));
            $sql = "SELECT d.data
                      FROM {user_info_data} d
                      JOIN {user_info_field} f ON f.id = d.fieldid
                     WHERE d.userid = :userid AND f.shortname = :shortname";
            $value = (string)$DB->get_field_sql($sql, ['userid' => $userid, 'shortname' => $shortname]);
        }
        self::$ssidcache[$userid] = trim($value);
        return self::$ssidcache[$userid];
    }

    /**
     * HTML for the SSID line printed under "Current grade report for ...".
     *
     * @param int $userid
     * @return string
     */
    public static function ssid_html(int $userid): string {
        $ssid = self::get_ssid($userid);
        if ($ssid === '') {
            $value = html_writer::span(
                get_string('ssidnotonfile', 'report_interims'),
                'report-interims-ssid-missing',
                ['style' => 'color: #b00020;']
            );
        } else {
            $value = s($ssid);
        }
        return html_writer::div(
            $value,
            'report-interims-ssid',
            ['style' => 'font-size: medium; margin-top: 4px;']
        );
    }

    /**
     * URL of the uploaded school logo, or null.
     *
     * @return moodle_url|null
     */
    public static function logo_url(): ?moodle_url {
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            context_system::instance()->id,
            'report_interims',
            'schoollogo',
            0,
            'filename',
            false
        );
        if (!$files) {
            return null;
        }
        $file = reset($files);
        return moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        );
    }

    /**
     * HTML img tag for the school logo (empty string when none uploaded).
     *
     * @return string
     */
    public static function logo_html(): string {
        $url = self::logo_url();
        if (!$url) {
            return '';
        }
        $alt = get_config('report_interims', 'schoolname');
        return html_writer::empty_tag('img', [
            'src' => $url->out(false),
            'alt' => $alt ? format_string($alt) : get_string('schoollogo', 'report_interims'),
            'style' => 'height: 75px; width: auto;',
        ]);
    }

    /**
     * The school name from the settings.
     *
     * @return string
     */
    public static function school_name(): string {
        return format_string((string)get_config('report_interims', 'schoolname'));
    }

    /**
     * "Grades printed on ..." text for the footer of each report.
     *
     * @return string
     */
    public static function printed_on_string(): string {
        return get_string(
            'gradesprinted',
            'report_interims',
            userdate(time(), get_string('strftimeprinted', 'report_interims'))
        );
    }

    /**
     * Start of a printable report page: minimal styles plus a screen-only toolbar.
     *
     * @param int $courseid
     * @param string $intro Text shown above the reports on screen only
     * @return string
     */
    public static function print_page_start(int $courseid, string $intro): string {
        $css = '@media print { .noprint { display: none; } } '
            . '@media screen { .noscreen { display: none; } } '
            . '@media print { .page-break { page-break-before: always; } }';
        $out = html_writer::tag('style', $css);
        $out .= html_writer::start_div('noprint', ['style' => 'text-align: center; font-size: large;']);
        $back = new moodle_url('/report/interims/index.php', ['id' => $courseid]);
        $out .= html_writer::start_tag('form', ['method' => 'get', 'action' => $back->out_omit_querystring()]);
        $out .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $courseid]);
        $out .= html_writer::empty_tag('input', ['type' => 'submit',
            'value' => get_string('returntoreports', 'report_interims')]);
        $out .= html_writer::end_tag('form');
        $out .= html_writer::tag('p', $intro);
        $out .= html_writer::end_div();
        return $out;
    }
}
