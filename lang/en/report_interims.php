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
 * English language strings for the Interims report plugin.
 *
 * @package    report_interims
 * @category   string
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2026 Brian Pool (version 3.0)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminreports'] = 'Administrative reports';
$string['allenrolledstudents'] = 'All enrolled students';
$string['allstudents'] = 'All students';
$string['course'] = 'Course';
$string['coursenumber'] = 'Course number';
$string['currentgpa'] = 'The current unweighted GPA for this quarter is: {$a}';
$string['currentgradereportfor'] = 'Current Grade Report for {$a}';
$string['currentletter'] = 'Current letter grade';
$string['currentpercentage'] = 'Current percentage grade';
$string['dfcoursepattern'] = 'Course ID number pattern for D/F lists';
$string['dfcoursepattern_desc'] = 'Regular expression matched against the course ID number. Only matching courses are listed on the "All my students D/F" and "No grades" reports. The default <code>^[0-9]{3,4}[A-Za-z]{2}$</code> matches ID numbers such as 1234AB (course number followed by teacher initials). Leave empty to include every course.';
$string['dfreport'] = 'Current D-F student report';
$string['dfreportall'] = 'Current D-F student report (all courses)';
$string['dfthreshold'] = 'D/F cut-off percentage';
$string['dfthreshold_desc'] = 'A course grade below this percentage is reported as a D or F on the D/F reports and mailouts.';
$string['elemcategories'] = 'Elementary school categories';
$string['elemreports'] = 'Elementary school reports';
$string['elemschoolname'] = 'Elementary school name';
$string['elemschoolname_desc'] = 'Printed under the class name on the elementary gradecards, for example "Springfield Elementary School". Leave empty to use the school name.';
$string['elemssidreports'] = 'Elementary school reports with SSID';
$string['eligibilityreport'] = 'Eligibility report';
$string['enableelemreports'] = 'Enable elementary reports';
$string['enableelemreports_desc'] = 'Offer the elementary report cards.';
$string['enablehsreports'] = 'Enable high school reports';
$string['enablehsreports_desc'] = 'Offer the high school reports.';
$string['enableloaexport'] = 'Enable LOA grade exports';
$string['enableloaexport_desc'] = 'Show the "Administrative reports" section with the LOA grade exports (fixed-width Excel export used by Ohio DASL student information systems).';
$string['enablemsreports'] = 'Enable middle school reports';
$string['enablemsreports_desc'] = 'Offer the middle school reports.';
$string['enablessidreports'] = 'Enable SSID reports';
$string['enablessidreports_desc'] = 'Add "with SSID" versions of the high school and middle school parent reports (interims and D/F mailouts). These print the student\'s SSID on its own line under the student\'s name so a secure document service such as ParentSquare can match each page to the right family.';
$string['esgradecardintro'] = 'Elementary gradecards: the class report card is on the front and the specials report card on the back. Print 2-sided; every student is exactly two pages.';
$string['esgradecardssid'] = 'ES gradecards with SSID';
$string['esnogradesfor'] = 'No elementary indicator grades were found for: {$a}';
$string['excludelastname'] = 'Test account last name';
$string['excludelastname_desc'] = 'Students with this last name (for example a shared test student) are skipped on every report. Not case sensitive. Leave empty to include everyone.';
$string['gpa'] = 'GPA';
$string['gradesexported'] = 'Grades exported on: {$a}';
$string['gradesprinted'] = 'Grades printed on: {$a}';
$string['hsalldf'] = 'All my students D/F report';
$string['hscategories'] = 'High school categories';
$string['hsdfmailout'] = 'HS D/F mailout';
$string['hsdfmailoutssid'] = 'HS D/F mailout with SSID';
$string['hsgpa'] = 'GPA report';
$string['hsgpaprinc'] = 'Principal list';
$string['hsgpasports'] = 'Eligibility report';
$string['hsgpasuper'] = 'Superintendent list';
$string['hsinterims'] = 'HS interims';
$string['hsinterimsssid'] = 'HS interims with SSID';
$string['hsmydf'] = 'My class D/F report';
$string['hsnogrades'] = 'No grades list';
$string['hsreports'] = 'High school reports';
$string['hsssidreports'] = 'High school reports with SSID';
$string['interimintro'] = 'Interim reports - these reports reflect all current gradebook grades as of today.';
$string['interims:view'] = 'View interims reports';
$string['interims:viewadminreports'] = 'View SSID and administrative interims reports';
$string['interims:viewallschools'] = 'View interims reports for every school level';
$string['invaliduser'] = 'That student is not enrolled in this course.';
$string['lettergrade'] = 'Letter grade';
$string['levelcategories_desc'] = 'Courses in these categories (and their sub-categories) show only this school level\'s reports. Site administrators, and users given the "View interims reports for every school level" capability, always see every enabled level. If no categories are chosen for any level, every enabled level is shown in every course.';
$string['loagradeexport'] = 'LOA grade export';
$string['msalldf'] = 'All my students D/F report';
$string['mscategories'] = 'Middle school categories';
$string['msdfmailout'] = 'MS D/F mailout';
$string['msdfmailoutssid'] = 'MS D/F mailout with SSID';
$string['msgpa'] = 'GPA report';
$string['msgpaprinc'] = 'Principal list';
$string['msgpasports'] = 'Eligibility report';
$string['msgpasuper'] = 'Superintendent list';
$string['msinterims'] = 'MS interims';
$string['msinterimsssid'] = 'MS interims with SSID';
$string['msinterimswithsig'] = 'MS interims with signature';
$string['msmydf'] = 'My class D/F report';
$string['msreports'] = 'Middle school reports';
$string['msssidreports'] = 'Middle school reports with SSID';
$string['nogradesrecorded'] = 'No grades recorded yet.';
$string['nogradesreportall'] = 'No grades report (all courses)';
$string['nogradesyet'] = 'No grades';
$string['nolevelforcourse'] = 'This course is not in a category that has been assigned to a school level, so no school reports are offered here. Ask your site administrator to check the Interims report settings.';
$string['nostudents'] = 'No students found';
$string['parentguardian'] = 'Parent/Guardian';
$string['parentstudentsignature'] = 'Parent/Student signature';
$string['percentgrade'] = '%Grade';
$string['pluginname'] = 'Interims report';
$string['principallist'] = 'Principal list';
$string['printedfor'] = 'for {$a}';
$string['privacy:metadata'] = 'The Interims report plugin does not store any personal data. It only displays information from the gradebook and user profiles.';
$string['progressmarkl'] = 'L = Limited, did not meet benchmark';
$string['progressmarkm'] = 'M = Meets Expectations';
$string['progressmarkp'] = 'P = Progressing to benchmark';
$string['progressmarks'] = 'Progress Marks';
$string['quarter1'] = 'FIRST QUARTER';
$string['quarter2'] = 'SECOND QUARTER';
$string['quarter3'] = 'THIRD QUARTER';
$string['quarter4'] = 'FOURTH QUARTER';
$string['reportcard1'] = '1st grade';
$string['reportcard2'] = '2nd grade';
$string['reportcard3'] = '3rd grade';
$string['reportcard4'] = '4th grade';
$string['reportcardk'] = 'Kindergarten grade';
$string['reportcardspecials'] = 'Specials';
$string['reportintro'] = 'This report reflects all current gradebook grades as of today.';
$string['reportnotavailable'] = 'This report is not available in this course.';
$string['reportselection'] = 'Report selection';
$string['returntoreports'] = 'Return to reports';
$string['schoolcontact'] = 'Contact information';
$string['schoolcontact_desc'] = 'School phone number, email or website (for your reference; reserved for future report footers).';
$string['schoolinfo'] = 'School information';
$string['schoolinfo_desc'] = 'Shown at the top of every printed report.';
$string['schoollevels'] = 'School levels';
$string['schoollevels_desc'] = 'Choose which school levels are offered and which course categories belong to each one.';
$string['schoollogo'] = 'School logo';
$string['schoollogo_desc'] = 'Printed at the top of every report. Recommended height 75 pixels or more; PNG with a transparent background works best.';
$string['schoolname'] = 'School name';
$string['schoolname_desc'] = 'Printed under the logo on every report, for example "Springfield Local Schools".';
$string['selectgroup'] = 'Select a group';
$string['selectstudent'] = 'Select a student';
$string['singleclassloaexport'] = 'Single class LOA grade export';
$string['specialscode1'] = '1. Exceptional behavior & performance';
$string['specialscode2'] = '2. Positive and respectful attitude';
$string['specialscode3'] = '3. Participation/behavior is improving';
$string['specialscode4'] = '4. Frequently disrupts instruction';
$string['specialscode5'] = '5. Needs to improve respect for others';
$string['specialscode6'] = '6. Needs to bring gym shoes to PE';
$string['specialscodes'] = 'Behavior and Work Habits - Comment Codes';
$string['specialsreportcard'] = 'Specials Report Card';
$string['ssid'] = 'SSID';
$string['ssidfield'] = 'SSID source field';
$string['ssidfield_desc'] = 'Where the student\'s SSID is stored in Moodle: the user ID number, the username, or a custom user profile field.';
$string['ssidintro'] = 'SSID version: each student\'s SSID is printed under the student\'s name for secure document delivery.';
$string['ssidnotonfile'] = 'not on file';
$string['ssidprofilefield'] = 'Profile field: {$a}';
$string['ssidsettings'] = 'SSID reports';
$string['ssidsettings_desc'] = 'Secure document services such as ParentSquare match each printed page to a family by the student\'s SSID.';
$string['strftimeprinted'] = '%m/%d/%Y at %I:%M %p';
$string['student'] = 'Student';
$string['studentid'] = 'Student ID';
$string['studentname'] = 'Student name';
$string['studentroles'] = 'Student roles';
$string['studentroles_desc'] = 'Users with these roles in a course are treated as students when grades are collected.';
$string['studentsettings'] = 'Students and grades';
$string['subjectar'] = 'Art';
$string['subjectla'] = 'Language/Literacy Development';
$string['subjectma'] = 'Mathematics Development';
$string['subjectmu'] = 'Music';
$string['subjectpe'] = 'Physical Education';
$string['subjectsc'] = 'Science Development';
$string['subjectso'] = 'Social Studies Development';
$string['subjectxb'] = 'Behavior and Attendance';
$string['superintendentlist'] = 'Superintendent list';
