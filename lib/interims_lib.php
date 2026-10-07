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
 * Shared includes for the report scripts.
 *
 * Since version 3.0.2 the high school and middle school report logic lives in the
 * autoloaded classes in classes/local/ (grades, interim_report, gpa_list, df_list,
 * loa_export). This file is kept because the elementary report card scripts include it
 * for the course and gradebook libraries.
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2026 Brian Pool (version 3.0)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/export/lib.php');
