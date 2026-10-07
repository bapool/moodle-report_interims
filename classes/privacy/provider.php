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
 * Privacy Subsystem implementation for report_interims.
 *
 * This plugin does not store any personal data itself. It only reads
 * and displays data from the Moodle gradebook and user tables.
 * Therefore, it implements the null_provider interface.
 *
 * @package    report_interims
 * @copyright  2024 Brian Pool
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_interims\privacy;

use core_privacy\local\metadata\null_provider;

/**
 * Privacy provider for the Interims report plugin.
 *
 * This report plugin does not store any personal data. It only reads
 * and displays existing data from:
 * - Moodle's gradebook (grades and grade items)
 * - User enrollment information
 * - Course information
 *
 * All data displayed comes from core Moodle tables and is covered by
 * the privacy implementations of those core components.
 *
 * @copyright  2024 Brian Pool
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements null_provider {
    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return string The string identifier
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
