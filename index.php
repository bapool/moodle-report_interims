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
 * Report selection page for the Interims report.
 *
 * Only the school level(s) that match the course category are shown, unless the
 * user is a site administrator or has report/interims:viewallschools.
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2026 Brian Pool (version 3.0)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/gradelib.php');

use report_interims\local\helper;

$courseid = required_param('id', PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$groupid = optional_param('group', 0, PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($courseid);
require_capability('report/interims:view', $context);

$PAGE->set_url('/report/interims/index.php', ['id' => $courseid]);
$PAGE->set_pagelayout('report');
$PAGE->set_context($context);
$PAGE->set_title($course->shortname . ': ' . get_string('pluginname', 'report_interims'));
$PAGE->set_heading(format_string($course->fullname, true, ['context' => $context]));
$PAGE->navbar->add(get_string('pluginname', 'report_interims'));

// The selectors on this page decide the group; forget any group remembered in the session
// so the reports start from "All participants" unless a group is chosen here.
if (isset($SESSION->activegroup[$course->id])) {
    unset($SESSION->activegroup[$course->id][VISIBLEGROUPS]);
    unset($SESSION->activegroup[$course->id][SEPARATEGROUPS]);
}

// Make sure course totals are up to date before anyone prints.
grade_regrade_final_grades_if_required($course);

$levels = helper::visible_levels($course, $context);
// SSID and administrative reports are only for site admins and managers
// (capability report/interims:viewadminreports).
$canviewadmin = helper::can_view_admin_reports($context);
$ssidenabled = $canviewadmin && ((get_config('report_interims', 'enablessidreports') === false)
    || !empty(get_config('report_interims', 'enablessidreports')));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('reportselection', 'report_interims'), 2);

// Group and student selectors.
echo html_writer::start_div('report-interims-selectors my-3');
if (groups_get_course_groupmode($course) != NOGROUPS) {
    $groupoptions = [0 => get_string('allparticipants')];
    foreach (groups_get_all_groups($courseid) as $group) {
        $groupoptions[$group->id] = format_string($group->name);
    }
    echo html_writer::start_div('group-selector mb-3');
    echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url->out_omit_querystring(),
        'class' => 'd-inline-block']);
    echo html_writer::label(
        get_string('selectgroup', 'report_interims') . ': ',
        'groupselect',
        true,
        ['class' => 'mr-2 me-2']
    );
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $courseid]);
    echo html_writer::select(
        $groupoptions,
        'group',
        $groupid,
        false,
        ['id' => 'groupselect', 'onchange' => 'this.form.submit()', 'class' => 'custom-select form-select',
        'style' => 'min-width: 200px; width: auto; display: inline-block;']
    );
    echo html_writer::end_tag('form');
    echo html_writer::end_div();
}
echo report_interims_print_user_selector($course, $PAGE->url, $userid, $groupid);
echo html_writer::end_div();

/**
 * Prints one section of report buttons.
 *
 * @param string $heading Heading text
 * @param array $reports List of [file, string id, params]
 */
function report_interims_index_section(string $heading, array $reports): void {
    global $OUTPUT;
    echo $OUTPUT->heading($heading, 3);
    echo html_writer::start_div('report-buttons');
    foreach ($reports as [$file, $label, $params]) {
        $url = new moodle_url('/report/interims/reports/' . $file, $params);
        echo $OUTPUT->single_button($url, get_string($label, 'report_interims'), 'get');
    }
    echo html_writer::end_div();
}

$base = ['id' => $courseid];
$student = ['id' => $courseid, 'userid' => $userid, 'group' => $groupid];
$ssid = ['ssid' => 1];

if (!$levels) {
    echo $OUTPUT->notification(get_string('nolevelforcourse', 'report_interims'), 'info');
}

echo html_writer::start_div('report-interims-reports');

if (in_array(helper::LEVEL_HS, $levels)) {
    report_interims_index_section(get_string('hsreports', 'report_interims'), [
        ['HSinterims.php', 'hsinterims', $student],
        ['HSMYDF.php', 'hsmydf', $base],
        ['HSALLDF.php', 'hsalldf', $base],
        ['HSDFmailout.php', 'hsdfmailout', $base],
        ['nogrades.php', 'hsnogrades', $base],
        ['HSGPAsuper.php', 'hsgpasuper', $base],
        ['HSGPAprinc.php', 'hsgpaprinc', $base],
        ['HSGPAsports.php', 'hsgpasports', $base],
        ['HSGPA.php', 'hsgpa', $base],
    ]);
    if ($ssidenabled) {
        report_interims_index_section(get_string('hsssidreports', 'report_interims'), [
            ['HSinterims.php', 'hsinterimsssid', $student + $ssid],
            ['HSDFmailout.php', 'hsdfmailoutssid', $base + $ssid],
        ]);
    }
}

if (in_array(helper::LEVEL_MS, $levels)) {
    report_interims_index_section(get_string('msreports', 'report_interims'), [
        ['MSinterims.php', 'msinterims', $student],
        ['MSinterimsB.php', 'msinterimswithsig', $student],
        ['MSMYDF.php', 'msmydf', $base],
        ['MSALLDF.php', 'msalldf', $base],
        ['MSDFmailout.php', 'msdfmailout', $base],
        ['nogrades.php', 'hsnogrades', $base],
        ['MSGPAsuper.php', 'msgpasuper', $base],
        ['MSGPAprinc.php', 'msgpaprinc', $base],
        ['MSGPAsports.php', 'msgpasports', $base],
        ['MSGPA.php', 'msgpa', $base],
    ]);
    if ($ssidenabled) {
        report_interims_index_section(get_string('msssidreports', 'report_interims'), [
            ['MSinterims.php', 'msinterimsssid', $student + $ssid],
            ['MSDFmailout.php', 'msdfmailoutssid', $base + $ssid],
        ]);
    }
}

if (in_array(helper::LEVEL_ELEM, $levels)) {
    report_interims_index_section(get_string('elemreports', 'report_interims'), [
        ['reportcardk.php', 'reportcardk', $student],
        ['reportcard1.php', 'reportcard1', $student],
        ['reportcard2.php', 'reportcard2', $student],
        ['reportcard3.php', 'reportcard3', $student],
        ['reportcard4.php', 'reportcard4', $student],
        ['reportcardsp.php', 'reportcardspecials', $student],
    ]);
    if ($ssidenabled) {
        report_interims_index_section(get_string('elemssidreports', 'report_interims'), [
            ['esgradecard.php', 'esgradecardssid', $student + $ssid],
        ]);
    }
}

$loaenabled = $canviewadmin && ((get_config('report_interims', 'enableloaexport') === false)
    || !empty(get_config('report_interims', 'enableloaexport')));
if ($loaenabled) {
    report_interims_index_section(get_string('adminreports', 'report_interims'), [
        ['myloagrade.php', 'singleclassloaexport', $base],
        ['loagrade.php', 'loagradeexport', $base],
    ]);
}

echo html_writer::end_div();

echo html_writer::tag('style', '.report-interims-reports .report-buttons { display: flex; flex-wrap: wrap; gap: 4px;
    margin: 10px 0 30px 0; } .report-interims-reports .report-buttons form { margin: 0; }');

echo $OUTPUT->footer();
