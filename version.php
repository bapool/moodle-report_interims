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
 * Version information for the Interims report plugin.
 *
 * This report provides comprehensive grade reporting including:
 * - High School interim reports
 * - Middle School interim reports
 * - Elementary report cards (K-4)
 * - GPA reports and eligibility tracking
 * - D/F grade reports
 * - SSID versions of parent reports for secure document delivery (e.g. ParentSquare)
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2026 Brian Pool (version 3.0 generalised for community release)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'report_interims';
$plugin->version   = 2026100706;        // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2024042200;        // Requires Moodle 4.4+ (2024042200).
$plugin->supported = [404, 405];        // Moodle 4.4 and 4.5.
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '3.1.3';
