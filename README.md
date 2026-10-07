# Interims report (report_interims)

A Moodle course report that prints parent-ready grade reports for K-12 schools:
high school and middle school interim reports, D/F mailouts, GPA honour lists,
athletic eligibility lists, and elementary standards-based report cards.

Originally written in 2016 for National Trail Local Schools (Ohio) by Brian Pool.
Version 3.0 removes the district-specific code so other schools can use it.

## Why this plugin exists

Moodle's gradebook shows one course at a time. Schools need one printed page per
student that lists **every** course the student is enrolled in, with the current
percentage, letter grade and quarter GPA, so homeroom teachers and the counselling
office can send interim reports home halfway through each grading period.

Version 3.0 adds **SSID versions** of the parent reports. Secure document services
such as ParentSquare match each page of an uploaded PDF to a family by the student's
State Student ID (SSID), so these versions print the SSID on its own line directly
under "Current Grade Report for ..." (the number only, no label). Print the SSID version to PDF and upload it,
instead of printing and mailing paper copies.

## Requirements

- Moodle 4.4 or 4.5
- MySQL/MariaDB or PostgreSQL

## Installation

1. Site administration > Plugins > Install plugins, upload the zip, **or** unzip into
   `/report/interims` on the server.
2. Visit Site administration > Notifications to complete the install/upgrade.
3. Configure Site administration > Plugins > Reports > **Interims report**.

The report appears in each course under **Reports > Interims report**
(capability `report/interims:view`, given to teachers and managers by default).

## Settings

| Setting | What it does |
| --- | --- |
| School name / logo | Printed at the top of every report. Upload the logo here. |
| Elementary school name | Printed under the class name on the ES gradecards (the district name is not shown). |
| Enable high / middle / elementary reports | Turns a whole school level on or off. |
| High / middle / elementary school categories | Courses in these categories (and sub-categories) show **only** that level's reports. |
| Student roles | Roles counted as students when grades are collected (default: Student). |
| Test account last name | Students with this last name are skipped (default `A-Student`). |
| D/F cut-off percentage | Grades below this are a D/F (default 68.5). |
| Course ID number pattern for D/F lists | Regular expression of graded course ID numbers (default `^[0-9]{3,4}[A-Za-z]{2}$`, e.g. `1234AB`). |
| Enable LOA grade exports | Shows the Ohio DASL fixed-width LOA exports. |
| Enable SSID reports | Adds the "with SSID" report buttons. |
| SSID source field | ID number (default), username, or any custom profile field. |

### Which reports show in which course

- Site administrators, and anyone given `report/interims:viewallschools`
  (no role has it by default), see every enabled level in every course. Give this
  capability to counsellors or principals who run reports from a district course.
- The **SSID reports** and **Administrative reports** are shown only to site
  administrators and users with `report/interims:viewadminreports` (managers by
  default, at course, category or site level). Teachers do not see them.
- Everyone else only sees the level whose category contains the course.
- If **no** categories are mapped yet, every enabled level shows everywhere
  (same as version 2), so upgrading changes nothing until you map categories.
- Opening a report URL for a level that does not belong to the course is refused.

## Reports

**High school** – HS interims, My class D/F, All my students D/F, HS D/F mailout,
No grades list, Superintendent list, Principal list, Eligibility report, GPA report.
**With SSID:** HS interims, HS D/F mailout.

**Middle school** – MS interims, MS interims with signature, My class D/F,
All my students D/F, MS D/F mailout, No grades list, Superintendent/Principal lists,
Eligibility report, GPA report.
**With SSID:** MS interims, MS D/F mailout.

**Elementary** – Kindergarten to 4th grade and Specials standards-based report cards
(uses Moodle outcomes).
**With SSID:** ES gradecards – a combined two-page gradecard per student (class report card
on the front with the SSID, specials on the back) for every student enrolled in the course it
is run from. Run it from the elementary guidance course and save as PDF for ParentSquare.

### How the elementary gradecards read the indicators

Elementary grades are Moodle outcomes (called indicators). The outcome short name must start
with the quarter number and a two-letter subject code, for example `2-LA-03`:

| Code | Subject | Side |
| --- | --- | --- |
| LA | Language/Literacy Development | front |
| MA | Mathematics Development | front |
| SC | Science Development | front |
| SO | Social Studies Development | front |
| XB | Behavior and Attendance | front |
| AR | Art | back (specials) |
| MU | Music | back (specials) |
| PE | Physical Education | back (specials) |

The outcome description is the "I can ..." text that prints. Marks print as the first letter
of the scale item (M, P, L), or the whole value when the item is a number (attendance).
The student's primary class is the course where most of their LA/MA/SC/SO indicators are graded.
Every quarter that has grades is printed and quarters with no grades are left out. The class
side prints two quarters per page; the specials side prints all of its quarters on one page.

**Administrative** – LOA grade exports (Ohio DASL format).

Every printed report opens in a print-friendly page: use the browser's
Print > Save as PDF. Screen-only text (the Return button) is hidden when printing,
and each student starts on a new page.

## Grading conventions (current code)

- Course grades are the gradebook course totals.
- Only courses whose ID number starts with a number greater than 1 are included
  in interims and GPA (homerooms and clubs without numeric ID numbers are skipped).
- A missing course total, or one that rounds to 0%, prints as "No grades" on interims and
  does not count towards the GPA.
- HS letter scale: A 95+, A- 90, B+ 87, B 83, B- 80, C+ 77, C 73, C- 70, D+ 67, D 60.
  HS GPA points: 4.0, 3.8, 3.4, 3.0, 2.8, 2.4, 2.0, 1.8, 1.4, 1.0, 0.
  MS scale and points: A 90 (4), B 80 (3), C 70 (2), D 60 (1), F (0).
  All scales are in `classes/local/grades.php`.
- Interims count only graded classes in the GPA. The GPA lists (GPA report, superintendent,
  principal, eligibility) count every visible course the student is enrolled in as a student.
- List thresholds – HS: superintendent 3.75+, principal 3.33–3.74, eligibility below 2.00.
  MS: superintendent above 3.90, principal all A/B and below 4.00, eligibility below 1.60.

## Files

- `index.php` – report selection page
- `classes/local/helper.php` – settings-driven helpers (levels, SSID, logo, roles, permissions)
- `classes/local/grades.php` – HS/MS grade scales, GPA and gradebook queries
- `classes/local/interim_report.php` – interims, interims with signature, D/F mailouts (and SSID versions)
- `classes/local/gpa_list.php` – GPA, superintendent, principal and eligibility lists
- `classes/local/df_list.php` – my class D/F, all my students D/F, no grades lists
- `classes/local/loa_export.php` – Ohio DASL LOA grade exports
- `classes/local/es_gradecard.php` – combined elementary gradecards with SSID
- `lib/reportcard_lib.php`, `lib/oclib.php` – elementary report cards (to be rewritten in 3.1)
- `lib/interims_lib.php` – shared includes for the elementary report card scripts
- `reports/` – one small script per report
- `.moodle-plugin-ci.yml` – excludes the elementary files from the code checks until 3.1

## Privacy

The plugin stores no personal data; it only reads the gradebook and user profiles.

## License

GNU GPL v3 or later. © 2016-2026 Brian Pool, National Trail Local Schools.
