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
 * Upgrade steps for the Interims report plugin.
 *
 * @package    report_interims
 * @copyright  2026 Brian Pool, National Trail Local Schools
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrades the plugin.
 *
 * @param int $oldversion The version being upgraded from
 * @return bool
 */
function xmldb_report_interims_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2026100701) {
        // Version 3.0.0 gave managers "view every school level" by default.
        // From 3.0.1 managers see only their course's level (plus SSID and admin reports),
        // so remove the default manager permission that 3.0.0 added.
        $managers = $DB->get_records('role', ['archetype' => 'manager'], '', 'id');
        foreach ($managers as $role) {
            unassign_capability('report/interims:viewallschools', $role->id);
        }
        upgrade_plugin_savepoint(true, 2026100701, 'report', 'interims');
    }

    return true;
}
