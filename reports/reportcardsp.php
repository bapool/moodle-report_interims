<?php  
// This file is part of report_interims for Moodle by Brian Pool 
// http://www.nationaltrail.k12.oh.us/course/view.php?id=2
// K-4 Outcome based Report Cards and 5-12 Interim Reports
//
//This report will go through the students in this course and do a combined interim report for those students.  
//This is a "mini report card" that will be sent home
// with students halfway through the current grading period.  
//Usually home room teachers will run this report and pass it out for their students.
//Lines 73 and 75 should be edited for the name and graphic for your school!

require_once(__DIR__ . "/../../../config.php");
require_once($CFG->dirroot."/grade/lib.php");
require_once($CFG->dirroot."/lib/dmllib.php");
require_once($CFG->dirroot."/lib/grade/grade_item.php");
require_once($CFG->dirroot."/lib/grouplib.php");
require_once($CFG->dirroot . "/report/interims/lib/interims_lib.php");
require_once($CFG->dirroot . "/report/interims/lib/reportcard_lib.php");

GLOBAL $DB , $OUTPUT, $output, $userid, $group;

$id = required_param('id', PARAM_INT); //course id
$page     = optional_param('page', 0, PARAM_INT);   // active page
$userid   = optional_param('userid', $USERID, PARAM_INT);
$cgroup  = optional_param('groupid', $group, PARAM_INT); // group id 
//  IF a student is sent from the index.php then only it will be printed.

$url = new moodle_url('/report/interims/reports/reportcardsp.php', array('id' => $id));                  
$PAGE->set_url($url);

// Get current day, month and year for printout.
$date = usergetdate(time());
list($d, $m, $y, $min, $hour) = array($date['mday'], $date['mon'], $date['year'], $date['minutes'], $date['hours']);
$mmin = sprintf("%02d", $min);
$printdate="Grades printed on:".$m."/".$d."/".$y." at:".$hour.":".$mmin;

if (!$course = $DB->get_record('course', array('id' => $id))) {
	print_error('nocourseid');
}

// requirements
require_login($course);
$context = context_course::instance($id);
require_capability('report/interims:view', $context);

$groupmode = groups_get_course_groupmode($course);  
$group = groups_get_course_group($course, true);
$students = get_enrolled_users($context, $withcapability = 'mod/assign:submit', $group, $userfields = 'u.*', $orderby = 'lastname ASC', $limitfrom = 0, $limitnum = 0, $onlyactive = true);
//
print "<style type='text/css'> @media print {.noprint {display: none;}} @media screen{.noscreen {display: none;}}</style> <div class = 'noprint'>";
Print "<center><FONT SIZE=4>";
//
$interim = new \moodle_url('/report/interims/index.php', array('id'=>$id));
$retbutton = new single_button($interim, "Return to Reports", 'get');
echo $OUTPUT->render($retbutton);
$seluserid = $userid;
//
Print "Grade Card Reports - All students will print on separate sheets.";
Print "<br><br>";
Print "Please remember to NOT print 2-sided as these are single sided reports.";	
Print "<br><br>";
Print "To print only one student select the student from the drop down box on the previous page";
//
Print "<br><br>";
$count=1;
$rcount=1;
$title = "" . get_config("report_interims", "schoolname") . "";
print "</div>";
$seluserid = $userid;
$fontsize=11;
Print "<head><style>@media print {.page-break { page-break-inside: avoid; page-break-before: always};}</style></head>";
// If there is no single user selected for report, print all student reports
if($seluserid == 0){

	if ($students) {
		// for each student, call the print_interims method
		$scount = count($students);
		//echo "STUDENTS TO PROCESS IS:".$count;
		echo "<html moznomarginboxes mozdisallowselectionprint>";
		echo "</html>";
		//echo "Total Number of Students to be Printed is ".$scount;
		foreach ($students as $student) {
			$currentuserid = $student->id;
			print_spcard($id,$student->id,$printdate,$fontsize, $scount);
			$scount--;
			$count--;
			$rcount++;
		}
		unset($students);
	}
}
// else print the single user report
else{
	echo "<html moznomarginboxes mozdisallowselectionprint>";
	echo "</html>";
	$scount = 1;
	$currentuserid = $seluserid;
	print_spcard($id,$seluserid,$printdate,$fontsize, $scount);
}
exit;
 	
?>
