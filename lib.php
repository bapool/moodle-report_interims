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

/**
 * Moodle callbacks for the Interims report.
 *
 * The HS/MS report logic lives in classes/local/ (helper, grades, interim_report, gpa_list,
 * df_list, loa_export); the elementary report cards are in lib/reportcard_lib.php.
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2026 Brian Pool (version 3.0)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Adds the Interims report to the course Reports menu.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course object
 * @param context $context The course context
 */
function report_interims_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/interims:view', $context)) {
        $url = new moodle_url('/report/interims/index.php', ['id' => $course->id]);
        $navigation->add(
            get_string('pluginname', 'report_interims'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'report_interims',
            new pix_icon('i/report', '')
        );
    }
}

/**
 * Page types offered by this report (used when adding blocks).
 *
 * @param string $pagetype Current page type
 * @param stdClass|null $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 * @return array
 */
function report_interims_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return [
        '*' => get_string('page-x', 'pagetype'),
        'report-*' => get_string('page-report-x', 'pagetype'),
        'report-interims-*' => get_string('pluginname', 'report_interims'),
        'report-interims-index' => get_string('pluginname', 'report_interims'),
    ];
}

/**
 * Serves the school logo uploaded in the plugin settings.
 *
 * @param stdClass $course
 * @param stdClass|null $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool false if the file was not found
 */
function report_interims_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel != CONTEXT_SYSTEM || $filearea !== 'schoollogo') {
        return false;
    }
    require_login();

    $itemid = (int)array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'report_interims', $filearea, $itemid, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        return false;
    }
    send_stored_file($file, DAYSECS, 0, $forcedownload, $options);
}

/**
 * Student selector shown on the report selection page.
 *
 * @param stdClass $course The course object
 * @param moodle_url $url The page URL
 * @param int $userid The currently selected user ID (0 for all)
 * @param int $groupid The current group ID
 * @return string HTML
 */
function report_interims_print_user_selector($course, $url, $userid = 0, $groupid = 0) {
    $context = context_course::instance($course->id);
    $userfields = \core_user\fields::for_name()->get_sql('u', false, '', '', false)->selects;
    $users = get_enrolled_users($context, 'mod/assign:submit', $groupid, 'u.id, ' . $userfields, 'u.lastname ASC, u.firstname ASC');
    if (empty($users)) {
        return '';
    }

    $options = [0 => get_string('allstudents', 'report_interims')];
    foreach ($users as $user) {
        $options[$user->id] = fullname($user);
    }

    $output = html_writer::start_div('user-selector-container mb-3');
    $output .= html_writer::start_tag('form', ['method' => 'get', 'action' => $url->out_omit_querystring(),
        'class' => 'd-inline-block']);
    $output .= html_writer::label(
        get_string('selectstudent', 'report_interims') . ': ',
        'userselect',
        true,
        ['class' => 'mr-2 me-2']
    );
    $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $course->id]);
    if ($groupid > 0) {
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'group', 'value' => $groupid]);
    }
    $output .= html_writer::select(
        $options,
        'userid',
        $userid,
        false,
        ['id' => 'userselect', 'onchange' => 'this.form.submit()', 'class' => 'custom-select form-select',
        'style' => 'min-width: 200px; width: auto; display: inline-block;']
    );
    $output .= html_writer::end_tag('form');
    $output .= html_writer::end_div();
    return $output;
}
