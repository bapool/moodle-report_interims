# Changelog

## 3.1.3 (2026-10-07) – version 2026100706

### Changed (ES gradecards with SSID)
- Larger top margin on every page (0.6in; other margins stay 0.4in).
- On the second class page the student's name and class are printed larger.
- "Grades exported on ..." is now at the bottom of every page, including the first class page.

## 3.1.2 (2026-10-07) – version 2026100705

### Changed (ES gradecards with SSID)
- Every quarter that has grades is now printed (not just the latest two). Quarters with no
  grades yet are left out completely - no table and no heading.
- The class side prints two quarters per page: quarters 1-2 on the first page (with the full
  header) and quarters 3-4 on the next page (with the student's name and class at the top).
  The specials side prints all of its graded quarters on one page.
- A student can now be two or three pages depending on how many quarters are graded.
- Grade items are one size larger (indicator text 12px, subject headings and marks 13px).

## 3.1.1 (2026-10-07) – version 2026100704

### Changed (ES gradecards with SSID)
- Header: the elementary school name is now under the class name and larger; the SSID is
  below it at the old school-name size; the district name is no longer shown.
- Footer now reads "Grades exported on ..." instead of "Grades printed on ...".
- Every side now fits on one page: tighter fonts and spacing, 0.4in page margins, and any
  page that is still too tall is automatically scaled down to fit Letter or A4. Each student
  is always exactly two pages.

## 3.1.0 (2026-10-07) – version 2026100703

### Added
- New report **ES gradecards with SSID** (Elementary school reports with SSID section).
  Run it from an elementary course such as the Elementary School Guidance News course: it
  prints a combined gradecard for every student enrolled in that course, ready to save as a
  PDF and send through ParentSquare Secure Documents.
  - Front: logo, student name, the student's primary class (for example "1st Grade with
    Mrs. Knepshield"), elementary school name, SSID, progress marks key and
    school year; then the class indicators (Language/Literacy, Mathematics, Science,
    Social Studies, Behavior and Attendance).
  - Back: "Specials Report Card" with Art, Music and Physical Education indicators and the
    Behavior and Work Habits comment codes.
  - Each side shows the latest quarter that has grades and the quarter before it; quarters
    with no grades are not printed.
  - No signature blocks. Every student is exactly two pages (front and back) so 2-sided
    printing and PDF splitting stay aligned. Students with no elementary grades are skipped
    and listed on screen only.
  - Only for site admins and users with `report/interims:viewadminreports` (managers).
- Setting "Elementary school name" printed on the elementary gradecards.

### Unchanged
- The existing Kindergarten-4th grade and Specials report cards.

## 3.0.2 (2026-10-07) – version 2026100702

Code clean-up of the high school and middle school reports so they pass the Moodle
coding standard (moodle and moodle-extra). The printed reports look the same as 3.0.1.

### Changed
- All HS/MS report logic rewritten as autoloaded classes in `classes/local/`:
  `grades` (scales, GPA, gradebook queries), `interim_report` (interims, interims with
  signature, D/F mailouts, SSID versions), `gpa_list` (GPA, superintendent, principal and
  eligibility lists), `df_list` (my class D/F, all my students D/F, no grades) and
  `loa_export` (Ohio DASL LOA exports).
- The HS/MS report scripts in `reports/` are now a few lines each.
- `lib/interims_lib.php` is now only a loader kept for the elementary report cards.
- Grade scales and list thresholds are documented in one place (`classes/local/grades.php`,
  `classes/local/gpa_list.php`).
- Added `.moodle-plugin-ci.yml`, which excludes only the elementary report card files from
  the GitHub code checks until they are rewritten in 3.1.

### Fixed
- "No grades list" showed no students in 3.0.0 and 3.0.1.
- "My class D/F report" showed a 0% grade as an A; it now shows F.
- Interims showed a GPA of "0" (instead of "0.00") for a student with no graded courses.

### Removed
- `lib/csvlib.class.php` (only used by the Abre export removed in 3.0.0).
- Unused language strings (old MS comment codes, signature slip text).

### Verified
- Every HS/MS report was printed before and after the rewrite; the text is identical
  apart from the fixes above, and the LOA export lines match the original code exactly.

## 3.0.1 (2026-10-07) – version 2026100701

### Changed
- SSID reports and administrative (LOA) reports are only shown to site admins and
  users with the new capability `report/interims:viewadminreports` (managers by
  default, whether assigned in the course, category or site). Teachers no longer
  see them, and opening their URLs directly is refused.
- `report/interims:viewallschools` is no longer given to managers by default.
  Managers now see only their course's school level, like teachers. An upgrade step
  removes the default manager permission that 3.0.0 added. Grant it by hand to anyone
  who must see every level.
- The SSID line now prints just the number, not bold and without the "SSID:" label.
- Removed the "MS interims with signature and SSID" button (ParentSquare collects the
  signature). The paper "MS interims with signature" report is unchanged.

## 3.0.0 (2026-10-07) – version 2026100700

Generalised for sharing with the Moodle community.

### Added
- "With SSID" versions of the HS interims, HS D/F mailout, MS interims,
  MS interims with signature and MS D/F mailout. The SSID prints on its own line
  under "Current Grade Report for ..." for ParentSquare Secure Documents and
  similar services. Students with no SSID show "not on file" in red.
- Settings: SSID source field (ID number, username or custom profile field),
  SSID label, enable SSID reports.
- School levels mapped to course categories. A course only shows the reports for
  its own level; site admins and `report/interims:viewallschools` see all.
- New capability `report/interims:viewallschools` (managers by default).
- Report scripts refuse to run in a course of the wrong school level.
- Settings: student roles, test account last name, D/F cut-off, course ID number
  pattern for D/F lists, enable LOA exports.
- New plugin icon; README rewritten.

### Changed
- All HS/MS report text moved to the language file (school name, list titles,
  table headings, GPA line, signature block, MS comment codes).
- "Grades printed on" date format is a language string (`strftimeprinted`).
- Logo comes only from the uploaded setting (no "School Logo" placeholder text).
- Student role id 5, "A-Student", 68.5 and NT-specific titles are no longer hard coded.
- MySQL-only SQL (REGEXP, CAST AS UNSIGNED, varchar > integer) replaced with PHP
  filtering, so the plugin also runs on PostgreSQL.
- Queries now use a unique first column, fixing D/F lists that could drop a course
  when a student had two identical grades.
- Single-student reports check the student is enrolled in the course.
- Student selector lists students only (not teachers).
- The five parent-facing reports share one runner function.

### Fixed
- `index.php` config path, undefined `$USERID`, `$GROUP`, `$seluserid`, `$j`,
  `$cumgpa`/`$cumclasses` warnings, deprecated `print_error()`,
  `{groups}_members` table name in LOA exports.
- Test account is now skipped on the D/F list and no-grades reports too.

### Removed
- District-only or unused scripts: NTteachers.php (unauthenticated table
  import), abregrade.php and abregrade2.php (unauthenticated Abre export that wrote
  student grades into the web folder), HSDFmail.php, HSALLDFContact.php,
  MSALLDFContact.php, Classcontact.php, MyHSgrades.php, MyMSgrades.php,
  nogradescourse.php, loaexport.php.
- config_school.php, duplicate pluginfile.php, unused grade selector functions,
  development notes (STATUS, NEXT_STEPS, INSTALLATION, TROUBLESHOOTING).

### Not changed
- Elementary report cards and their libraries are untouched (planned for 3.1:
  combined ES gradecard printed from the Elementary School Guidance News course).

## 2.0.0 (2024-11-19)
- Moodle 4.4/4.5 compatibility, privacy provider, settings page, logo upload.

## 1.x (2016-2023)
- Original National Trail Local Schools version.
