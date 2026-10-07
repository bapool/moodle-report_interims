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
 * Admin settings for the Interims report plugin.
 *
 * Site administration > Plugins > Reports > Interims report.
 *
 * @package    report_interims
 * @copyright  2026 Brian Pool, National Trail Local Schools
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // School information.
    $settings->add(new admin_setting_heading(
        'report_interims/schoolinfo',
        get_string('schoolinfo', 'report_interims'),
        get_string('schoolinfo_desc', 'report_interims')
    ));

    $settings->add(new admin_setting_configtext(
        'report_interims/schoolname',
        get_string('schoolname', 'report_interims'),
        get_string('schoolname_desc', 'report_interims'),
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'report_interims/elemschoolname',
        get_string('elemschoolname', 'report_interims'),
        get_string('elemschoolname_desc', 'report_interims'),
        '',
        PARAM_TEXT
    ));


    $settings->add(new admin_setting_configstoredfile(
        'report_interims/schoollogo',
        get_string('schoollogo', 'report_interims'),
        get_string('schoollogo_desc', 'report_interims'),
        'schoollogo',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.gif', '.svg']]
    ));

    $settings->add(new admin_setting_configtextarea(
        'report_interims/schoolcontact',
        get_string('schoolcontact', 'report_interims'),
        get_string('schoolcontact_desc', 'report_interims'),
        '',
        PARAM_TEXT
    ));

    // School levels and the course categories that belong to them.
    $settings->add(new admin_setting_heading(
        'report_interims/reporttypes',
        get_string('schoollevels', 'report_interims'),
        get_string('schoollevels_desc', 'report_interims')
    ));

    $categories = core_course_category::make_categories_list();

    $levels = [
        'hs' => ['enablehsreports', 'hscategories'],
        'ms' => ['enablemsreports', 'mscategories'],
        'elem' => ['enableelemreports', 'elemcategories'],
    ];
    foreach ($levels as $level => $names) {
        [$enable, $cats] = $names;
        $settings->add(new admin_setting_configcheckbox(
            'report_interims/' . $enable,
            get_string($enable, 'report_interims'),
            get_string($enable . '_desc', 'report_interims'),
            1
        ));
        $settings->add(new admin_setting_configmultiselect(
            'report_interims/' . $cats,
            get_string($cats, 'report_interims'),
            get_string('levelcategories_desc', 'report_interims'),
            [],
            $categories
        ));
    }

    // Students.
    $settings->add(new admin_setting_heading(
        'report_interims/students',
        get_string('studentsettings', 'report_interims'),
        ''
    ));

    $settings->add(new admin_setting_pickroles(
        'report_interims/studentroles',
        get_string('studentroles', 'report_interims'),
        get_string('studentroles_desc', 'report_interims'),
        ['student']
    ));

    $settings->add(new admin_setting_configtext(
        'report_interims/excludelastname',
        get_string('excludelastname', 'report_interims'),
        get_string('excludelastname_desc', 'report_interims'),
        'A-Student',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'report_interims/dfthreshold',
        get_string('dfthreshold', 'report_interims'),
        get_string('dfthreshold_desc', 'report_interims'),
        '68.5',
        PARAM_FLOAT
    ));

    $settings->add(new admin_setting_configtext(
        'report_interims/dfcoursepattern',
        get_string('dfcoursepattern', 'report_interims'),
        get_string('dfcoursepattern_desc', 'report_interims'),
        '^[0-9]{3,4}[A-Za-z]{2}$',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configcheckbox(
        'report_interims/enableloaexport',
        get_string('enableloaexport', 'report_interims'),
        get_string('enableloaexport_desc', 'report_interims'),
        1
    ));

    // SSID for secure document delivery.
    $settings->add(new admin_setting_heading(
        'report_interims/ssid',
        get_string('ssidsettings', 'report_interims'),
        get_string('ssidsettings_desc', 'report_interims')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'report_interims/enablessidreports',
        get_string('enablessidreports', 'report_interims'),
        get_string('enablessidreports_desc', 'report_interims'),
        1
    ));

    $settings->add(new admin_setting_configselect(
        'report_interims/ssidfield',
        get_string('ssidfield', 'report_interims'),
        get_string('ssidfield_desc', 'report_interims'),
        'idnumber',
        \report_interims\local\helper::ssid_field_options()
    ));
}
