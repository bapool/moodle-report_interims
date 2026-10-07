<?php  
// This file is part of report_interims for Moodle by Brian Pool 
// http://www.nationaltrail.k12.oh.us/course/view.php?id=2
// K-4 Outcome based Report Cards and 5-12 Interim Reports
//
require_once(__DIR__ . "/../../../config.php");
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot."/grade/lib.php");
require_once($CFG->dirroot."/lib/dmllib.php");
require_once($CFG->dirroot."/lib/grade/grade_item.php");
require_once($CFG->dirroot."/lib/grouplib.php");
require_once($CFG->dirroot . "/report/interims/lib/oclib.php");
//
GLOBAL $DB , $OUTPUT, $userid, $group;
//
$id = required_param('id', PARAM_INT); //course id
//$page     = optional_param('page', 0, PARAM_INT);   // active page
//$userid   = optional_param('userid', $USERID, PARAM_INT);
//$cgroup  = optional_param('groupid', $group, PARAM_INT); // group id 
$oid1  = optional_param('oid1',0,PARAM_INT); 
$oid2 = optional_param('oid2',0,PARAM_INT);
//$thisquart= $oid1+1;
$countcat=0;
$skip=true;
//
if (!$course = $DB->get_record('course', array('id' => $id))) {
	print_error('nocourseid');
}
// requirements
require_login($course);
$context = context_course::instance($id);
require_capability('gradereport/laegrader:view', $context);
//
$url = new moodle_url('report/interims/classindcomp.php', array('id' => $id));                  
$PAGE->set_url($url);
// Get current day, month and year for printout.
$date = usergetdate(time());
list($d, $m, $y, $min, $hour) = array($date['mday'], $date['mon'], $date['year'], $date['minutes'], $date['hours']);
$mmin = sprintf("%02d", $min);
$printdate="Grades printed on:".$m."/".$d."/".$y." at:".$hour.":".$mmin;
//
//$thiscat="LA";
//$thisquart=1;
//
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
//
$quartlist=array(1,2,3,4);
$sublist=array("LA","MA","SC","SO");
$top_s = "Subject";
$top_q = "Quarter";
//
//$urlroot = "classind.php?id=".$id."&oid1=".(substr($oid1,-1))."&oid2=".(substr($oid2,-1));
$urlroot = "classind.php?id=".$id."&oid1=".(substr($oid1,-1));
$urlroot2 = "classind.php?id=".$id."&oid1=".(substr($oid1,-1))."&oid2=".(substr($oid2,-1));
//
print popup_form($urlroot, $quartlist, 'selectoutcome', $oid1, $top_q, '', '', false, 'self', "Choose the Quarter for this report: ");
//
//print popup_form($urlroot2, $sublist, 'selectoutcome', $oid2, $top_s, '', '', false, 'self', "Choose the Subject for this report: ",false);
//
$oid1=substr($oid1,-1);
$oid2=substr($oid2,-1);
$thisquart=$quartlist[$oid1];
//echo "<br/>SELECTED Q IS".$oid2."<br/>";
//echo "<br/>SELECTED S IS".$oid1."<br/>";
echo "This report is best done in Firefox, use print Preview and select landscape.<br/>";
echo "All quarters indicators are included in the report, subjects print on separate pages.<br/>";
print "</div>";
print "<style type='text/css'> @media print{@page {size: landscape}}</style>";
echo "<html moznomarginboxes mozdisallowselectionprint></html>";
$seluserid = $userid;
//echo "students check";
// Get all outcomes used in this course
//
$sql = "SELECT go.shortname as shortname, c.fullname as coursename
FROM {grade_outcomes} go
JOIN {grade_outcomes_courses} goc ON  go.id = goc.outcomeid
JOIN {course} c ON go.courseid = c.id
JOIN {grade_items} gi ON go.id = gi.outcomeid
JOIN {grade_grades} gg ON gi.id = gg.itemid
JOIN {user} u ON gg.userid = u.id
JOIN {scale} s ON gi.courseid = s.courseid
WHERE c.visible = 1 AND c.id = $id 
ORDER BY go.shortname ASC,go.timemodified DESC, lastname, firstname";
//
//$thiscat="LA";
//$oid=1;
//$thisquart=$oid2;
$skip=true;
if ($oid2==0) {$thiscat="LA";}
if ($oid2==1) {$thiscat="MA";}
if ($oid2==2) {$thiscat="SC";}
if ($oid2==3) {$thiscat="SO";}
//
while ($countcat < 4) {
if ($countcat==0) {$thiscat="LA";}
if ($countcat==1) {$thiscat="MA";}
if ($countcat==2) {$thiscat="SC";}
if ($countcat==3) {$thiscat="SO";}
	$results = $DB->get_record_sql($sql);
	//print_r($results);  //DEBUG Info
	$cfullname=($results->coursename);
	// print table title
	print "<br><br>";
	// *NT** change font size here
	print "<center><FONT SIZE='4'>";
	print "Quarter ".$thisquart." - ".$thiscat." Report for ".$cfullname;
	print "<br><br>";

	// print table header
	print "<FONT SIZE='2'>";
	print "<table border=1 cellpadding=0>";
	print "<tr>";
	print "<td width ='150' style='min-width:150px' bgcolor=#C0C0C0><b><font size='2'>Student</b></td>";

	//$subjectSubstring = substr($curName, 2, 2);
	$results = $DB->get_records_sql($sql);
		// Print the column headers with mouseover (fullname) descriptions of outcomes
		
		foreach($results as $result){
			$curname = substr($result->shortname, 2, 2);
			$curquart= substr($result->shortname, 0, 1);
			//echo $curquart;
			//if($result->shortname >= $sn1 && $result->shortname <= $sn2){
			if($curname == $thiscat && $curquart == $thisquart){
				print "<td width='80' style='min-width:80px' bgcolor=#C0C0C0><b><font size='2'><span title=".$result->fullname.
					">".$result->shortname."</span></b></td>";
			}
		}
		print "</tr>";
		print "<tr>"; 
		//print_r($students);

		// *NT** print grades for each student
		if ($students) {
			$classsize=count($students);
			foreach ($students as $student) {
				$currentuserid = $student->id;
				$studentsname=$student->lastname.", ".$student->firstname;
				print_data($student->id,$studentsname, $id,$thiscat,$thisquart);
			}
			//echo "Class Size is:".$classsize;
			//unset($students);
		}
	//
	print "</tr></table><table style='page-break-before:always' border=0 cellpadding=0 cellspacing=0></tr><tr><td><table border=0 cellpadding=4 cellspacing=0>";
	$countcat++;
}
////
exit;
function print_data($cstudentid, $studentname, $courseid,$thiscat,$thisquart) {
	//global $CFG, $DB, $course, $currentuserid, $cuser, $oid, $result_info, $scale, $oid1, $oid2, $sn1, $sn2;
	global $CFG, $DB;
	// Start printing the information fetched from the DB
	print "<table border=1 cellpadding=0>";
	print "<tr>";
	print "</tr>";
	print "<tr>"; 
	// *NT** Display the user's name in the table
	print "<tr>";
	print "<td width='150' style='min-width:150px;'><font size='2'>".$studentname."</td>";



	$sql = "SELECT go.id, go.courseid, go.fullname, go.scaleid, go.timemodified, go.shortname as shortname, go.description as description, s.scale, gg.finalgrade as ograde, u.firstname as firstname, u.lastname as lastname, c.fullname as fullname, c.idnumber as coursenum, u.idnumber as studentnum, u.id as studentid
	FROM {grade_outcomes} go
	JOIN {grade_items} gi ON go.id = gi.outcomeid
	JOIN {grade_grades} gg ON gi.id = gg.itemid
	JOIN {user} u ON gg.userid = u.id
	JOIN {course} c ON gi.courseid = c.id
	JOIN {scale} s ON gi.courseid = s.courseid
	WHERE gg.userid = $cstudentid AND c.visible = 1 AND c.id = $courseid
	ORDER BY go.shortname ASC,go.timemodified DESC";
	$results = $DB->get_records_sql($sql);
//	
	//print_r($results);
	foreach($results as $result){
               		$cname = substr($result->shortname, 2, 2);
			$cquart= substr($result->shortname, 0, 1);		
	if($cname ==$thiscat && $cquart==$thisquart){
//echo $result->shortname;
               		$cname = substr($result->shortname, 2, 2);
			$cquart= substr($result->shortname, 0, 1);
				$curName = $result->shortname;	
				//print $curName;
				$thisQuarter = substr($curName, 0, 1);
				$subjectSubstring = substr($curName, 2, 2);
				if ($subjectSubstring == $thiscat){
					$scaleid = $result->scaleid;
					$scale = new grade_scale(array('id' => $scaleid), false);
					$currentOutcome = $result->shortname;
					$newQuarter = substr($currentOutcome, 0, 1);
					$newSubject = substr($currentOutcome, 2, 2);
					
					// This loop gets the most recent grade for all of the entries for this particular indicator.
					foreach($result as $rr){

						if(empty($result->ograde)){
							Print "<tr style='border-bottom:dotted 1px'><font size='2'>"."_"."</td>";
							break;                      
						}
						else {
							$printgrade = $result->ograde;
							$scalegrade = $scale->get_nearest_item($printgrade);
					Print "<td width='80' style='min-width:80px'><font size='2'>".$scalegrade. "</td>";
							break;
						}
					}// end grade loop
				}  //End Subject Checks
			}// end grade loop
	}
}
?>
