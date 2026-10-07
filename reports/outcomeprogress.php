<?php  // $Id: exceptions.php,v 1.1.8.1 2007/07/23 02:48:57 arborrow Exp $
// branch from CVS 18STABLE at: exceptions.php,v 1.11.6.3 2007/06/04 22:57:13 
//*EA*  This report will show the most reecent indicator grades for each student for the specified indicators within the range.

	// required files
	require_once("../../../config.php");
	require_once("../../lib.php");
	require_once("../../../ntcode/lib.php");
	require_once("../../../lib/dmllib.php");
	require_once("../../../lib/grade/grade_item.php");
	require_once("../../../lib/gradelib.php");
	require_once("../../../grade/lib.php");
	require_once("../../../lib/weblib.php");
	require_once("../../../lib/grade/grade_object.php");
	require_once("../../../lib/grade/grade_outcome.php");
	require_once($CFG->dirroot . "/report/interims/lib/oclib.php");
    
	// course id
$id = required_param('id', PARAM_INT); //course id
global $CFG, $DB;
echo "Report Started";
	// outcome ids
//	$oid1  = optional_param('oid1'); 
//	$oid2 = optional_param('oid2');
if (!$course = $DB->get_record('course', array('id' => $id))) {
	print_error('nocourseid');
}


// requirements
require_login($course);
$context = context_course::instance($id);
require_capability('gradereport/laegrader:view', $context);


	// ------Page begin------ //
	// *NT** This style allows us to define certain parts of the page to be unseen when sent to the printer.  Labeling a <div class='noprint'></div>
	//       will exclude the content within the tags from printing.
	print "<style type='text/css'> @media print {.noprint {display: none;}} @media screen{.noscreen {display: none;}}</style> <div class = 'noprint'>";
	// Print the group selector
$groupmode = groups_get_course_groupmode($course);  
$group = groups_get_course_group($course, true);
$students = get_enrolled_users($context, $withcapability = 'mod/assign:submit', $group, $userfields = 'u.*', $orderby = 'lastname ASC', $limitfrom = 0, $limitnum = 0, $onlyactive = true);


	// *NT** get all outcomes used in this course
	$oresult = array();
	$osql = "SELECT go.*
		FROM {grade_outcomes} go, {grade_outcomes_courses} goc
		WHERE go.id = goc.outcomeid AND goc.courseid = {$id}
		ORDER BY go.shortname ASC;";

	if($datas = $DB->get_records_sql($osql)){
		foreach($datas as $data){
			$instance = new grade_outcome();
			grade_object::set_properties($instance, $data);
			$oresult[$instance->id] = $instance;
		}	
	}
	
	$alloutcomes = $oresult;
	$outcomeslist = array();


	// *NT** get grade_items that use each outcome and store them in $report_info
	foreach($alloutcomes as $outcomeid => $outcome){
		// *NT** store all the related grade_items in $report_info[$outcomeid]['items']
		$report_info[$outcomeid]['items'] = $DB->get_records_select('grade_items', "outcomeid = $outcomeid AND courseid = $course->id");
	
		// *NT** store the outcome grade_item
		$report_info[$outcomeid]['outcome'] = $outcome;
		$outcomeslist[$outcomeid] = "".$report_info[$outcomeid]['outcome']->shortname . " - " . $report_info[$outcomeid]['outcome']->fullname;
	
		// *NT** get average grades for each item
		if(is_array($report_info[$outcomeid]['items'])){
			foreach($report_info[$outcomeid]['items'] as $itemid => $item){
				// *NT** fetch the grades to be averaged from the DB
				$sql1 = "SELECT itemid, AVG(finalgrade) AS avg, COUNT(finalgrade) AS count
					FROM {grade_grades}
					WHERE itemid = $itemid
					GROUP BY itemid";
				$info = $DB->get_records_sql($sql1);
				
			/*	if(!$info){
					unset($report_info[$outcomeid]['items'][$itemid]);
					continue;
				}
				else{
					$info = reset($info);
					$avg = round($info->avg, 2);
					$count = $info->count;
				}*/

				// *NT** store the calculated avg and count into $report_info
				$report_info[$outcomeid]['items'][$itemid]->avg = $avg;
				$report_info[$outcomeid]['items'][$itemid]->count = $count;
			}		
		}
	}


	$sn1 = $report_info[$oid1]['outcome']->shortname;
	$sn2 = $report_info[$oid2]['outcome']->shortname;
echo "CHECK";


	// *NT** make the first outcome selector. This will be the first outcome of the range that is displayed.
	$urlroot = "outcomeprogress.php?id=".$course->id."&group=".$group."&oid1=";
	$var = get_string("outcome", "gradereport_indoutcomes");
	print popup_form($urlroot, $outcomeslist, 'selectoutcome', $oid1,'choose','','',false,'self',"First ".$var." in range: ");
	
	
	
	/*
	// *NT** make the last outcome selector. This will be the last outcome of the range that is displayed.
	$urlroot = "outcomeprogress.php?id=".$course->id."&group=".$group."&oid1=".$oid1."&oid2=";
	print popup_form($urlroot, $outcomeslist, 'selectoutcome2', $oid2,'choose','','',false,'self',"Last $var in range: ");

	print "</div>";

	// *NT** pull necessary outcome information to be used/displayed
	$printname = $report_info[$oid]['outcome']->fullname;
	$scaleid = $report_info[$oid]['outcome']->scaleid;
	// *NT** used to collect the actual scale's name for a numerical grade -- i.e. Satisfactory, Not met, Exceeds..etc.
	$scale = new grade_scale(array('id' => $scaleid), false);

	
	// *NT** get the course fullname to be displayed in the report title
	$cresult = get_record('course', 'id', $id);

	// Print table title
	Print "<br><br>";
	// *NT** change font size here
	Print "<center><FONT SIZE='4'>";
	Print $var." Report for ".$cresult->fullname;


	// Print table header
	Print "<FONT SIZE='2'>";
	Print "<table border=1 cellpadding=0>";
	Print "<tr>";
	Print "<td width ='150' style='min-width:150px' bgcolor=#C0C0C0><b><font size='2'>Student</b></td>";


	// *NT** print the column headers with cute little mouseover (fullname) descriptions of outcomes
	foreach($report_info as $outcomeid => $outcomedata){
		if($outcomedata['outcome']->shortname >= $sn1 && $outcomedata['outcome']->shortname <= $sn2){
			print "<td width='80' style='min-width:80px' bgcolor=#C0C0C0><b><font size='2'><span title=".$outcomedata['outcome']->fullname.
				">".$outcomedata['outcome']->shortname."</span></b></td>";
		}
	}
	Print "</tr>";
	Print "<tr>"; 
	

	// *NT** print grades for each student
	if ($students) {
		// For each student, call print_interims
		foreach ($students as $student) {
			$currentuserid = $student->id;
			print_data($student->id);
		}
		unset($students);
	}

	// else print the single user report
	else{
		$currentuserid = $seluserid;
		print_data($userid);
	}

	exit;
    
//
//  This function prints all grades for the current student.
//  Returns nothing.
//
function print_data($user, $currid) {
	global $CFG, $course, $currentuserid, $cuser, $oid, $report_info, $scale, $oid1, $oid2, $sn1, $sn2;
	
	// Start printing the information fetched from the DB
	Print "<table border=1 cellpadding=0>";
	Print "<tr>";
	Print "</tr>";
	Print "<tr>"; 

	
	// *NT** Grab the user's name from the DB to be displayed in the table
	$sqlname = "SELECT u.firstname, u.lastname
			FROM {$CFG->prefix}user u
			WHERE u.id = '$currentuserid';";
	$resultname = mysql_query($sqlname);
	$resultname1 = mysql_fetch_array($resultname);

	// *NT** Display the user's name in the table
	Print "<tr>";
	print "<td width='150' style='min-width:150px;'><font size='2'>".$resultname1['lastname']. ", " .$resultname1['firstname']. "</td>";

	// *NT** begin printing outcome grades
	foreach($report_info as $outcomeid => $outcomedata){
		// *NT** if the outcome is within the selected range:
		if($outcomedata['outcome']->shortname >= $sn1 && $outcomedata['outcome']->shortname <= $sn2){
			$sql = "SELECT gg.finalgrade as ograde, go.id as oid, go.shortname, u.id
				FROM {grade_outcomes} go
				JOIN {user} u on u.id = '$currentuserid'
				LEFT JOIN {grade_items} gi on go.id = gi.outcomeid AND gi.courseid = '$course->id'
				LEFT JOIN {grade_grades} gg on gi.id = gg.itemid AND gg.userid = '$currentuserid'
				WHERE go.id = ".$outcomedata['outcome']->id." 
				ORDER BY gg.timemodified DESC, go.shortname;";

			$result = $DB->get_records_sql($sql);

			$scaleid = $outcomedata['outcome']->scaleid;
			// *NT** used to collect the actual scale's name for a numerical grade -- i.e. Satisfactory, Not met, Exceeds..etc.
			$scale = new grade_scale(array('id' => $scaleid), false);

			foreach($result as $rr){
				if(empty($rr->ograde)){
					Print "<td width='80' style='min-width:80px'><font size='2'>"."_____"."</td>";
					break;					
				}
				else {
				
					$printgrade = $rr->ograde;
					$scalegrade = $scale->get_nearest_item($printgrade);
					Print "<td width='80' style='min-width:80px'><font size='2'>".$scalegrade. "</td>";
					break;
				}
			}
		}
	}

}
*/

?>
