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
 * Capability definitions for the Interims report plugin.
 *
 * The capabilities are defined here to control access to the report.
 * The 'view' capability allows users to access and run the various
 * grade reports provided by this plugin.
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2024 Brian Pool (updated for Moodle 4.x)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    // View the interims reports.
    'report/interims:view' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
        'clonepermissionsfrom' => 'gradereport/grader:view',
    ],

    // See the SSID reports and the administrative (LOA export) reports.
    // Site administrators always have this; managers get it at course, category or site level.
    'report/interims:viewadminreports' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // See the reports for every school level, regardless of the course category.
    // Site administrators always have this. Not given to any role by default;
    // grant it to counsellors or principals who need it.
    'report/interims:viewallschools' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [],
    ],
];
