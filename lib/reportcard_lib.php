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
 * Library file for the Interims report plugin.
 *
 * @package    report_interims
 * @copyright  2016 Brian Pool, National Trail Local Schools
 * @copyright  2024 Brian Pool (updated for Moodle 4.x)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/export/lib.php');

global $CFG;
//
//  KG ReportCard
//
function print_kcard ($courseid, $currentuserid, $printdate, $fontsize, $scount) {
	global $DB, $CFG;
	$roles = 5;
//First we need to get the list of students that we will be doing reports for.  
//  Additional information is for the student report header.	
	$sql = "SELECT u.firstname as firstname, u.lastname as lastname, c.fullname as fullname, c.idnumber as coursenum, u.idnumber as studentnum, u.id as studentid
	FROM {grade_outcomes} go
	JOIN {grade_items} gi ON go.id = gi.outcomeid
	JOIN {grade_grades} gg ON gi.id = gg.itemid
	JOIN {user} u ON gg.userid = u.id
	JOIN {course} c ON gi.courseid = c.id
	JOIN {scale} s ON gi.courseid = s.courseid
	WHERE gg.userid = $currentuserid AND c.id = $courseid AND c.visible = 1";
//
	$header = true;
	$results = $DB->get_records_sql($sql);
	//print_r($results);  //DEBUG Info
//
	if ($results) {

		foreach ($results as $result) {
			$cfirstname = $result->firstname;
			$clastname = $result->lastname;
			$cfullname = $result->fullname;
			$ccoursenum = $result->coursenum;
			$cstudentid = $result->studentid;
			if($header) {
				print_rcheader($cfirstname, $clastname, $cfullname);
				$header = false;
			}
			print_rcdata($courseid, $cstudentid, $cfirstname, $clastname, $cfullname,$fontsize);
		}
	print_kfooter ($clastname,$cfirstname,$printdate,$scount);
	}
	unset($result); 
}
//
//  1st Grade ReportCard
//
function print_1card ($courseid, $currentuserid, $printdate, $fontsize, $scount) {
	global $DB, $CFG;
	$roles = 5;
//First we need to get the list of students that we will be doing reports for.  
//  Additional information is for the student report header.	
	$sql = "SELECT u.firstname as firstname, u.lastname as lastname, c.fullname as fullname, c.idnumber as coursenum, u.idnumber as studentnum, u.id as studentid
	FROM {grade_outcomes} go
	JOIN {grade_items} gi ON go.id = gi.outcomeid
	JOIN {grade_grades} gg ON gi.id = gg.itemid
	JOIN {user} u ON gg.userid = u.id
	JOIN {course} c ON gi.courseid = c.id
	JOIN {scale} s ON gi.courseid = s.courseid
	WHERE gg.userid = $currentuserid AND c.id = $courseid AND c.visible = 1";
//
	$header = true;
	$results = $DB->get_records_sql($sql);

	//print_r($results);  //DEBUG Info
//
	if ($results) {

		foreach ($results as $result) {
			$cfirstname = $result->firstname;
			$clastname = $result->lastname;
			$cfullname = $result->fullname;
			$ccoursenum = $result->coursenum;
			$cstudentid = $result->studentid;
			if($header) {
				print_rcheader($cfirstname, $clastname, $cfullname);
				$header = false;
			}
			
			print_rcdata($courseid, $cstudentid, $cfirstname, $clastname, $cfullname,$fontsize);
					
		}
	print_1footer ($clastname,$cfirstname,$printdate,$scount);
	//exit;  //DEBUG ONLY FOR 1 Student.
	//print_kbreak ($count);
	}
	unset($result); 
}
//
//  2nd Grade ReportCard
//
function print_2card ($courseid, $currentuserid, $printdate, $fontsize, $scount) {
	global $DB, $CFG;
	$roles = 5;
//First we need to get the list of students that we will be doing reports for.  
//  Additional information is for the student report header.	
	$sql = "SELECT u.firstname as firstname, u.lastname as lastname, c.fullname as fullname, c.idnumber as coursenum, u.idnumber as studentnum, u.id as studentid
	FROM {grade_outcomes} go
	JOIN {grade_items} gi ON go.id = gi.outcomeid
	JOIN {grade_grades} gg ON gi.id = gg.itemid
	JOIN {user} u ON gg.userid = u.id
	JOIN {course} c ON gi.courseid = c.id
	JOIN {scale} s ON gi.courseid = s.courseid
	WHERE gg.userid = $currentuserid AND c.id = $courseid AND c.visible = 1";
//
	$header = true;
	$results = $DB->get_records_sql($sql);
	//print_r($results);  //DEBUG Info
//
	if ($results) {

		foreach ($results as $result) {
			$cfirstname = $result->firstname;
			$clastname = $result->lastname;
			$cfullname = $result->fullname;
			$ccoursenum = $result->coursenum;
			$cstudentid = $result->studentid;
			if($header) {
				print_rcheader($cfirstname, $clastname, $cfullname);
				$header = false;
			}
			
			print_rcdata($courseid, $cstudentid, $cfirstname, $clastname, $cfullname, $fontsize);
					
		}
	print_2footer ($clastname,$cfirstname,$printdate,$scount);
	//exit;  //DEBUG ONLY FOR 1 Student.
	//print_kbreak ($count);
	}
	unset($result); 
}
//
//  3rd Grade ReportCard
//
function print_3card ($courseid, $currentuserid, $printdate, $fontsize, $scount) {
	global $DB, $CFG;
	$roles = 5;
//First we need to get the list of students that we will be doing reports for.  
//  Additional information is for the student report header.	
	$sql = "SELECT u.firstname as firstname, u.lastname as lastname, c.fullname as fullname, c.idnumber as coursenum, u.idnumber as studentnum, u.id as studentid
	FROM {grade_outcomes} go
	JOIN {grade_items} gi ON go.id = gi.outcomeid
	JOIN {grade_grades} gg ON gi.id = gg.itemid
	JOIN {user} u ON gg.userid = u.id
	JOIN {course} c ON gi.courseid = c.id
	JOIN {scale} s ON gi.courseid = s.courseid
	WHERE gg.userid = $currentuserid AND c.id = $courseid AND c.visible = 1";
//
	$header = true;
	$results = $DB->get_records_sql($sql);
	//print_r($results);  //DEBUG Info
//
	if ($results) {

		foreach ($results as $result) {
			$cfirstname = $result->firstname;
			$clastname = $result->lastname;
			$cfullname = $result->fullname;
			$cfullname = "3rd Grade Report Card";
			$ccoursenum = $result->coursenum;
			$cstudentid = $result->studentid;
			if($header) {
				print_rcheader($cfirstname, $clastname, $cfullname);
				$header = false;
			}
			
			print_rcdata($courseid, $cstudentid, $cfirstname, $clastname, $cfullname,$fontsize);
					
		}
	print_3footer ($clastname,$cfirstname,$printdate,$scount);
	//exit;  //DEBUG ONLY FOR 1 Student.
	//print_kbreak ($count);
	}
	unset($result); 
}
//
//  4th Grade ReportCard
//
function print_4card ($courseid, $currentuserid, $printdate, $fontsize, $scount) {
	global $DB, $CFG;
	$roles = 5;
//First we need to get the list of students that we will be doing reports for.  
//  Additional information is for the student report header.	
	$sql = "SELECT u.firstname as firstname, u.lastname as lastname, c.fullname as fullname, c.idnumber as coursenum, u.idnumber as studentnum, u.id as studentid
	FROM {grade_outcomes} go
	JOIN {grade_items} gi ON go.id = gi.outcomeid
	JOIN {grade_grades} gg ON gi.id = gg.itemid
	JOIN {user} u ON gg.userid = u.id
	JOIN {course} c ON gi.courseid = c.id
	JOIN {scale} s ON gi.courseid = s.courseid
	WHERE gg.userid = $currentuserid AND c.id = $courseid AND c.visible = 1";
//
	$header = true;
	$results = $DB->get_records_sql($sql);
	//print_r($results);  //DEBUG Info
//
	if ($results) {

		foreach ($results as $result) {
			$cfirstname = $result->firstname;
			$clastname = $result->lastname;
			$cfullname = $result->fullname;
			$cfullname = "4th Grade Report Card";
			$ccoursenum = $result->coursenum;
			$cstudentid = $result->studentid;
			if($header) {
				print_rcheader($cfirstname, $clastname, $cfullname);
				$header = false;
			}
			
			print_rcdata($courseid, $cstudentid, $cfirstname, $clastname, $cfullname, $fontsize);
					
		}
	print_4footer ($clastname,$cfirstname,$printdate,$scount);
	//exit;  //DEBUG ONLY FOR 1 Student.
	//print_kbreak ($count);
	}
	unset($result); 
}
//
//  Specials Grade ReportCard
//
function print_spcard ($courseid, $currentuserid, $printdate, $fontsize, $scount) {
	global $DB, $CFG;
	$roles = 5;
//First we need to get the list of students that we will be doing reports for.  
//  Additional information is for the student report header.	
	$sql = "SELECT u.firstname as firstname, u.lastname as lastname, c.fullname as fullname, c.idnumber as coursenum, u.idnumber as studentnum, u.id as studentid
	FROM {grade_outcomes} go
	JOIN {grade_items} gi ON go.id = gi.outcomeid
	JOIN {grade_grades} gg ON gi.id = gg.itemid
	JOIN {user} u ON gg.userid = u.id
	JOIN {course} c ON gi.courseid = c.id
	JOIN {scale} s ON gi.courseid = s.courseid
	WHERE gg.userid = $currentuserid AND c.id = $courseid AND c.visible = 1";
//
	$header = true;
	$results = $DB->get_records_sql($sql);
	//print_r($results);  //DEBUG Info
//
	if ($results) {

		foreach ($results as $result) {
			$cfirstname = $result->firstname;
			$clastname = $result->lastname;
			$cfullname = "Specials Report Card";
			$ccoursenum = $result->coursenum;
			$cstudentid = $result->studentid;
			if($header) {
				print_rcheader($cfirstname, $clastname, $cfullname);
				$header = false;
			}
			print_rcdata($courseid, $cstudentid, $cfirstname, $clastname, $cfullname,$fontsize);
		}
	print_spfooter ($clastname,$cfirstname,$printdate,$scount);
	}
	unset($result); 
}

//  This function the report header for the current student.
//  Returns nothing.
//
function print_rcheader($cfirstname, $clastname, $cfullname) {
	 global $CFG;
//	removes the firefox border information for a cleaner reportcard!
	echo "<html moznomarginboxes mozdisallowselectionprint>";
	echo "</html>";
//
//Get the school Year
$date = usergetdate(time());
list($d, $m, $y, $min, $hour) = array($date['mday'], $date['mon'], $date['year'], $date['minutes'], $date['hours']);
$ThisYear=$date['year'];
$ThisMonth=$date['mon'];
If ($ThisMonth < 8) {
$Schoolyear = ($ThisYear-1)." - ".$ThisYear;
};
If ($ThisMonth > 7) {
$Schoolyear = ($ThisYear)." - ".($ThisYear+1);
};
//If $ThisMonth > 7 Then $Schoolyear = ($ThisYear)." - ".($ThisYear+1);
//
	print "<center><FONT SIZE='4'>";
	print "<tr>";
    print "<td>";
	print "<tr><td><table border=0 cellpadding=4 cellspacing=0>";
	$namefont = 5;
	$studname = $clastname. ", " .$cfirstname;
	$namelength = strlen($studname);
	if ($namelength > 20) 
		{
			$namefont = 4;
		}
//
    print "<tr><td><table border=0 cellpadding=4 cellspacing=0><tr>
    ";
    // Get and display school logo from file storage
    $fs = get_file_storage();
    $logocontext = context_system::instance();
    $logofiles = $fs->get_area_files($logocontext->id, 'report_interims', 'schoollogo', 0, 'filename', false);
    if (count($logofiles) > 0) {
        $logofile = reset($logofiles);
        $logourl = moodle_url::make_pluginfile_url(
            $logofile->get_contextid(),
            $logofile->get_component(),
            $logofile->get_filearea(),
            $logofile->get_itemid(),
            $logofile->get_filepath(),
            $logofile->get_filename()
        );
        echo "<img src='" . $logourl . "' alt='School Logo' style='height: 60px; width: auto;'>";
    } else {
        echo "<span style='font-size: large;'>School Logo</span>";
    }
    print "
    
    
    
    
    
    
    
    <font size=$namefont>".$studname."<font size='4'><br> $cfullname <font size='2'><br>National Trail Elementary School <br>             National Trail Local School District
        </td></tr></table></td>";
//
    print "<td style='text-align:center'><table align=center border=0 cellpadding=2 cellspacing=0>
        <tr><td style='font-weight:bold; border-left:dotted 1px; border-right:dotted 1px; border-top:dotted 1px;'> Progress Marks </td></tr><tr><td
        style='border-left:dotted 1px; border-right:dotted 1px; border-bottom:dotted 1px'><font size='2'>
        M = Meets Expectations<br>
        P = Progressing to benchmark<br>
        L = Limited, did not meet benchmark</td></tr></table><font size='6'>$Schoolyear<br></tr>";
//
	
	print "</td>";
	
}
//  This function prints all grades for the current student.
//  Returns nothing.
//
function print_rcdata($courseid, $cstudentid, $cfirstname, $clastname, $cfullname, $fontsize) {

    global $CFG, $DB;
       
    $curQuarter = 0; // This variable keeps track of which quarter is to be printed next.  The variables runs 0-3, effectively quarters 1-4.
	$special= false;
//	
	If ($cfullname == "Specials Report Card")
	{
		$special= true;
	}
//	
	$osql = "SELECT go.id, go.courseid, go.fullname, go.scaleid, go.timemodified, go.shortname as shortname, go.description as description, s.scale, gg.finalgrade as ograde, u.firstname as firstname, u.lastname as lastname, c.fullname as fullname, c.idnumber as coursenum, u.idnumber as studentnum, u.id as studentid
	FROM {grade_outcomes} go
	JOIN {grade_items} gi ON go.id = gi.outcomeid
	JOIN {grade_grades} gg ON gi.id = gg.itemid
	JOIN {user} u ON gg.userid = u.id
	JOIN {course} c ON gi.courseid = c.id
	JOIN {scale} s ON gi.courseid = s.courseid
	WHERE gg.userid = $cstudentid AND c.visible = 1
	ORDER BY go.shortname ASC,go.timemodified DESC";
//
	$results = $DB->get_records_sql($osql); 

    for($i=0; $i<2; $i++){
        print "<tr>";
        for($j=0; $j<2; $j++){
            print "<td style='border-top:solid 1px; border-left:solid 1px; border-bottom:solid 1px;";

            if((strcmp($curQuarter,"1")==0) || (strcmp($curQuarter, "3") ==0)){
                print "border-right:solid 1px;";              
            }
            print"'><table width = 320 border=0 cellpadding=2 cellspacing=0><tr>";
       
            print "</tr><tr><td>";
 
            $currentSubject = "";
            $currentQuarter = "";
 
            print "<table border=0 cellpadding=3 cellspacing=0>";
			
		// Always print the quarter header regardless of data
		print "<tr><td colspan='2' align='center' style='text-align:center; background-color:lightgray; white-space:nowrap; vertical-align:top;'><center><font size='3'>"; // *NT*
		
		if(strcmp($curQuarter, "0") == 0){
			print "FIRST QUARTER";              
		} else if(strcmp($curQuarter, "1") == 0){
			print "SECOND QUARTER";
		} else if(strcmp($curQuarter, "2") == 0){
			 print "THIRD QUARTER";
		} else if(strcmp($curQuarter, "3") == 0){
			print "FOURTH QUARTER";
		}
		print "</font></center></td></tr>"; // *NT*

            $hasData = false;

            //  begin printing outcome grades
            foreach($results as $result){                  
                $curName = $result->shortname;
                $thisQuarter = substr($curName, 0, 1);
		// The following if statement: discards indicators without the appropriate MA, SO, SC, or LA tag
		$subjectSubstring = substr($curName, 2, 2);
		if (!$special)
		{   // If this is a normal reportcard, use these...if not, use specials.
			if(strcmp($subjectSubstring, "LA") == 0 || strcmp($subjectSubstring, "MA") == 0 || strcmp($subjectSubstring, "SO") == 0 || strcmp($subjectSubstring, "SC") == 0 || strcmp($subjectSubstring, "XB") == 0){
				// If the outcome is from the appropriate quarter:
				if(strcmp($thisQuarter-1, $curQuarter) == 0){
		    $hasData = true;

					$scaleid = $result->scaleid;
					// used to collect the actual scale's name for a numerical grade -- i.e. Satisfactory, Not met, Exceeds..etc.
					$scale = new grade_scale(array('id' => $scaleid), false);
					$currentOutcome = $result->shortname;
					$newQuarter = substr($currentOutcome, 0, 1);
					$newSubject = substr($currentOutcome, 2, 2);

					if(strcmp($newQuarter, $currentQuarter) != 0){
		   
						$currentQuarter = $newQuarter;
		   
					}
					if(strcmp($newSubject, $currentSubject) != 0){
		   
						$currentSubject = $newSubject;

						if(strcmp($currentSubject, "LA") == 0){
							$subj = "Language/Literacy Development";
						}
						else if(strcmp($currentSubject, "MA") == 0){
							$subj = "Mathematics Development";
						}
						else if(strcmp($currentSubject, "SO") == 0){
							$subj = "Social Studies Development";
						}
						else if(strcmp($currentSubject, "SC") == 0){
							$subj = "Science Development";
						}
						else if(strcmp($currentSubject, "MU") == 0){
							$subj = "Music";
						}
						else if(strcmp($currentSubject, "AR") == 0){
							$subj = "Art";
						}
						else if(strcmp($currentSubject, "PE") == 0){
							$subj = "Physical Education";
						}
						else if(strcmp($currentSubject, "XB") == 0){
							$subj = "Behavior and Attendance";
						}
						else{
							$subj = "--";
						}
						if(strcmp($subj, "--") != 0){

							print "<td style='font-weight:bold; font-size:13; border-bottom:solid 1px;'> $subj </td><td
							style='border-left:dotted 1px; border-bottom:solid 1px;'>&nbsp</td></tr>";
						}
					}
					print "<tr><td width=420  style='min-width:80px; font-size:$fontsize; border-bottom:dotted 1px'>"
						.$result->description. "</td>";

		   
					// This loop gets the most recent grade for all of the entries for this particular indicator.

					foreach($result as $rr){

					    if(empty($result->ograde)){
						Print "<td style='border-bottom:dotted 1px'><font size='2'>"."_"."</td>";
						break;                      
					    }
					    else {
						$printgrade = $result->ograde;
						$scalegrade = $scale->get_nearest_item($printgrade);
						
						// Check if this is the attendance percentage field
						if(strcmp($currentSubject, "XB") == 0 && is_numeric(trim($scalegrade))) {
						    // For attendance percentage, the grade should already be stored as a percentage
						    // Just display the scale grade text which should be the number
						    $displayValue = trim($scalegrade);
						    print "<td style='border-bottom:dotted 1px; border-left:dotted 1px; text-align:center; min-width:35px;'><font size='2'><b>".$displayValue."</b></td>";
						} else {
						    // For other fields, show first letter only
						    $firstLetter = strtoupper(substr($scalegrade, 0, 1));
						    print "<td style='border-bottom:dotted 1px; border-left:dotted 1px; text-align:center'><font size='2'><b>".$firstLetter."</b></td>";
						}
						break;
					    }
					}// end grade loop				
					print "</tr>";
				}// end if(appropriate quarter)
			}//EA - end if(not an included subject)
		} //Not special check, otherwise special		
		ELSE 
		{	//  These are the headers for the specials report card indicators
			if(strcmp($subjectSubstring, "AR") == 0 || strcmp($subjectSubstring, "MU") == 0 || strcmp($subjectSubstring, "PE") == 0){
				//  If the outcome is from the appropriate quarter:
				if(strcmp($thisQuarter-1, $curQuarter) == 0){
                    $hasData = true;
  
					$scaleid = $result->scaleid;
					// *NT** used to collect the actual scale's name for a numerical grade -- i.e. Satisfactory, Not met, Exceeds..etc.
					$scale = new grade_scale(array('id' => $scaleid), false);
					$currentOutcome = $result->shortname;
					$newQuarter = substr($currentOutcome, 0, 1);
					$newSubject = substr($currentOutcome, 2, 2);
   
					if(strcmp($newQuarter, $currentQuarter) != 0){
		   
						$currentQuarter = $newQuarter;
		   
					}
					if(strcmp($newSubject, $currentSubject) != 0){
		   
						$currentSubject = $newSubject;
   
						if(strcmp($currentSubject, "LA") == 0){
							$subj = "Language/Literacy Development";
						}
						else if(strcmp($currentSubject, "MA") == 0){
							$subj = "Mathematics Development";
						}
						else if(strcmp($currentSubject, "SO") == 0){
							$subj = "Social Studies Development";
						}
						else if(strcmp($currentSubject, "SC") == 0){
							$subj = "Science Development";
						}
						else if(strcmp($currentSubject, "MU") == 0){
							$subj = "Music";
						}
						else if(strcmp($currentSubject, "AR") == 0){
							$subj = "Art";
						}
						else if(strcmp($currentSubject, "PE") == 0){
							$subj = "Physical Education";
						}
						else if(strcmp($currentSubject, "XB") == 0){
							$subj = "Behavior and Attendance";
						}
						else{
							$subj = "--";
						}
						if(strcmp($subj, "--") != 0){
   
							print "<td style='font-weight:bold; font-size:13; border-bottom:solid 1px;'> $subj </td><td
							style='border-left:dotted 1px; border-bottom:solid 1px;'>&nbsp</td></tr>";
						}
					}
					print "<tr><td width=420  style='min-width:80px; font-size:$fontsize; border-bottom:dotted 1px'>"
						.$result->description. "</td>";
					// This loop gets the most recent grade for all of the entries for this particular indicator.
					foreach($result as $rr){

					    if(empty($result->ograde)){
						Print "<td style='border-bottom:dotted 1px'><font size='2'>"."_"."</td>";
						break;                      
					    }
					    else {
						$printgrade = $result->ograde;
						$scalegrade = $scale->get_nearest_item($printgrade);
						
						// Check if this is the attendance percentage field
						if(strcmp($currentSubject, "XB") == 0 && strpos($result->description, "Percentage") !== false) {
						    // For attendance percentage, display the full number
						    $displayValue = trim($scalegrade);
						    print "<td style='border-bottom:dotted 1px; border-left:dotted 1px; text-align:center; min-width:35px;'><font size='2'><b>".$displayValue."</b></td>";
						} else {
						    // For other fields, show first letter only
						    print "<td style='border-bottom:dotted 1px; border-left:dotted 1px;'><font size='2'>".substr($scalegrade, 0, 1). "</td>";
						}
						break;
					    }

					}// end grade loop
		   
					print "</tr>";
				}// end if(appropriate quarter)

		   }//End if(not an included subject)	
		} // Special Check
			
             }// end foreach(indicator)
             
             // If no data was found for this quarter, add a placeholder row but keep same table structure
             if (!$hasData) {
                 print "<tr><td colspan='2' style='text-align:center; font-style:italic; padding:20px; border-bottom:dotted 1px;'>No grades recorded for this quarter</td></tr>";
             }
             
            print "</table></td></tr></table></td>";
            $curQuarter ++;
        }// end for loop 2
		If (!$special) 
		{  //Special reportcards all fit on one page and do not wrap
        print "</tr></table>";
	If ($curQuarter == 2) {
	//print "Behavior and Work Habits are on the reverse side.";
	}
	If ($curQuarter == 2) {
        print "<table style='page-break-before:always'border=0 cellpadding=0 cellspacing=0></tr><tr><td>";}
	//Print "Q3";
        //print "<table style='page-break-before:always' border=0 cellpadding=0 cellspacing=0></tr><tr><td><table border=0 cellpadding=4 cellspacing=0>";}
		}
    }// end for loop 1
        print "</table>";
}
//
//Print informational footer at bottom of ReportCard
//
function print_kfooter ($clastname,$cfirstname,$printdate,$scount) {
//
//	
    print "</table></td></tr></table><br>";

    print "<tr><td style='text-align:center;font-size:2'>
 
        <table border=0 width=642 style='font-size:14; border-top:solid 1px' cellspacing=0>
            <tr>
                
             </tr><tr>
                <td width=300 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
				font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Next Grade Level Assignment
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Teacher
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Principal
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr>
         </table>

	</td></tr>";	
	print "</table>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}

}
//
//
//
function print_kfooter_old ($clastname,$cfirstname,$printdate,$scount) {
//
	
    print "</table></td></tr></table>";

    print "<tr><td style='text-align:center;font-size:2'>	

	<table border=0 width=659 style='font-size:14' cellspacing=0>
		<tr>
			<td width=60 style='font-weight:bold; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;
				border-left:solid 1px; border-top:solid 1px'>
				Behavior & Work Habits
			</td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
				1st
			</td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
				2nd
			</td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
				3rd
			</td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
				4th
			</td><td style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
				&nbsp
			</td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
				1st
			</td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
				2nd
			</td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
				3rd
			</td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
				4th
			</td>
		</tr><tr>
			<td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
				I can show respect.
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td style='border-right:solid 1px'>
				Overall
			</td><td width=60 style='border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 1px'>
				&nbsp
			</td>
		</tr><tr>
			<td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
				I can listen and follow rules.
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				Effort
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp


			</td>
		</tr><tr>
			<td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
				I can be kind and considerate of others.
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
				Attendance
			</td><td  style='font-weight:bold;border-bottom:solid 1px; background-color:lightgray;'>
				%
			</td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
				&nbsp
			</td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
				&nbsp
			</td><td  style='border-bottom:solid 1px; background-color:lightgray;; border-right:solid 1px;'>
				&nbsp
			</td>
		</tr><tr>
			<td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
				I can take turns and share.
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td style='border-bottom:solid 1px; border-right:solid 1px'>
				Absent
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
				&nbsp
			</td>
		</tr><tr>
			<td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
				I can work and write neatly.
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td style='border-bottom:solid 1px; border-right:solid 1px'>
				Present
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
				&nbsp
			</td>
		</tr><tr>
			<td width=350 height=20 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;'>
				I can show self confidence.
			</td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
				&nbsp
			</td><td style='border-right:solid 1px; border-bottom:solid 1px'>
				Tardy
			</td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
				&nbsp
			</td>

		</tr>
		 </tr><tr>
			<td width=350 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
			font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
				Next Grade Level Assignment
			</td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
				Teacher
			</td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
				&nbsp
			</td><td style='border-right:solid 1px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
				Principal
			</td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
				&nbsp
			</td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
				&nbsp
			</td>
		</tr>			
	</table>

	</td></tr>";	
	print "</table>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}
}
//
//Print informational footer at bottom of ReportCard
//
function print_1footer ($clastname,$cfirstname,$printdate, $scount) {
//	
    print "</table></td></tr></table><br>";

    print "<tr><td style='text-align:center;font-size:2'>
 
        <table border=0 width=642 style='font-size:14; border-top:solid 1px' cellspacing=0>
            <tr>
                
             </tr><tr>
                <td width=300 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
				font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Next Grade Level Assignment
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Teacher
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Principal
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr>
         </table>

	</td></tr>";	
	print "</table>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}

}
//
//
//
function print_1footer_old ($clastname,$cfirstname,$printdate, $scount) {
//	
    print "</table></td></tr></table>";

    print "<tr><td style='text-align:center;font-size:2'>
 
        <table border=0 width=659 style='font-size:14' cellspacing=0>
            <tr>
                <td width=60 style='font-weight:bold; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;
                    border-left:solid 1px; border-top:solid 1px'>
                    Behavior & Work Habits
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    1st
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    2nd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    3rd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    4th
                </td><td style='font-weight:bold;border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                   
                    Attendance
                </td><td  style='border-right:solid 1px;border-top:solid 1px; border-bottom:solid 1px; background-color:lightgray;'>
                    1st
                </td><td  style='border-right:solid 1px;border-top:solid 1px; border-bottom:solid 1px; background-color:lightgray;'>
                    2nd
                </td><td  style='border-right:solid 1px;border-top:solid 1px;border-bottom:solid 1px; background-color:lightgray;'>
                    3rd
                </td><td  style='border-right:solid 1px;border-top:solid 1px;border-bottom:solid 1px; background-color:lightgray;; border-right:solid 1px;'>
                    4th
                </td>
            </tr><tr>
                <td width=60 height=20 style='border-right:solid 1px; border-left:solid 1px;'>
                    Overall Effort
                </td><td width=80 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=80 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=80 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=80 style='border-right:solid 1px'>
                    &nbsp
                </td><td style='border-bottom:solid 1px; border-right:solid 1px'>
                    Absent
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=100 height=20 style='border-right:solid 1px; border-left:solid 1px;'>
                    &nbsp
                </td><td width=60 style=' border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style=' border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style=' border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style=' border-right:solid 1px'>
                    &nbsp
                </td><td style='border-bottom:solid 1px; border-right:solid 1px'>
                    Present
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=350 height=20 style='border-right:solid 1px; border-bottom:solid 2px; border-left:solid 1px;'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 2px'>
                    Tardy
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 2px; border-right:solid 1px;'>
                    &nbsp
                </td>
             </tr><tr>
                <td width=300 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
				font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Next Grade Level Assignment
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Teacher
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Principal
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr>
         </table>

	</td></tr>";	
	print "</table>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}

}
//
//Print informational footer at bottom of ReportCard
//
function print_2footer ($clastname,$cfirstname,$printdate,$scount) {
//	
    print "</table></td></tr></table><br>";

    print "<tr><td style='text-align:center;font-size:2'>
 
        <table border=0 width=642 style='font-size:14; border-top:solid 1px' cellspacing=0>
            <tr>
             </tr><tr>
                <td width=350 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
				font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Next Grade Level Assignment
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Teacher
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Principal
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr>
         </table>
 
        </td></tr>";
	print "</table>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";

	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}
}
//
//
//
function print_2footer_old ($clastname,$cfirstname,$printdate,$scount) {
//	
    print "</table></td></tr></table>";

    print "<tr><td style='text-align:center;font-size:2'>
 
        <table border=0 width=659 style='font-size:14' cellspacing=0>
            <tr>
                <td width=60 style='font-weight:bold; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;
                    border-left:solid 1px; border-top:solid 1px'>
                    Behavior & Work Habits
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    1st
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    2nd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    3rd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    4th
                </td><td style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    &nbsp
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    1st
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    2nd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    3rd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    4th
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Makes good use of time and materials
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px'>
                    Overall
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Works independently
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    Effort
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
 
 
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Listens and follows directions
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Attendance
                </td><td  style='font-weight:bold;border-bottom:solid 1px; background-color:lightgray;'>
                    %
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Works cooperatively with others
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td style='border-bottom:solid 1px; border-right:solid 1px'>
                    Absent
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Respects others
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td style='border-bottom:solid 1px; border-right:solid 1px'>
                    Present
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=350 height=20 style='border-right:solid 1px; border-bottom:solid 2px; border-left:solid 1px;'>
                    Completes work neatly and legibly
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 2px'>
                    Tardy
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 2px; border-right:solid 1px;'>
                    &nbsp
                </td>
             </tr><tr>
                <td width=350 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
				font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Next Grade Level Assignment
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Teacher
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Principal
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr>
         </table>
 
        </td></tr>";
	print "</table>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}

}
//
//
//
function print_3footer ($clastname,$cfirstname,$printdate,$scount) {
//	
	
    print "</table></td></tr></table><br>";

	
    print "<tr><td style='text-align:center;font-size:2'>
 
        <table border=0 width=642 style='font-size:14; border-top:solid 1px' cellspacing=0>
            <tr>
             </tr><tr>
                <td width=350 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
				font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Next Grade Level Assignment
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Teacher
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Principal
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr>
         </table>
 
        </td></tr>";
	print "</table>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}
}
//
//
//
function print_3footer_old ($clastname,$cfirstname,$printdate,$scount) {
//	
    print "</table></td></tr></table>";

    print "<tr><td style='text-align:center;font-size:2'>
        <table border=0 width=659 style='font-size:12' cellspacing=0>
            <tr>
                <td width=60 style='font-weight:bold; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;
                    border-left:solid 1px; border-top:solid 1px'>
                    Behavior & Work Habits
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    1st
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    2nd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    3rd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    4th
                </td><td width=15 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    &nbsp
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    1st
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    2nd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    3rd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    4th
                </td>
            </tr><tr>
                <td width=330 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Makes good use of time and materials
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=15 style='border-right:solid 1px'>
                    Overall
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Works independently
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=15 style='border-bottom:solid 1px; border-right:solid 1px'>
                    Effort
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
 
 
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Listens and follows directions
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=15 style='font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='font-weight:bold;border-bottom:solid 1px; text-align:right;background-color:lightgray;'>
                    Atten
                </td><td  style='font-weight:bold;border-bottom:solid 1px; background-color:lightgray;'>
                    dance
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Works cooperatively with others
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=15 style='border-bottom:solid 1px; border-right:solid 1px'>
                    Absent
                </td><td width=75 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=75 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=75 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=75 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Respects others
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td style='border-bottom:solid 1px; border-right:solid 1px'>
                    Present
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=350 height=20 style='border-right:solid 1px; border-bottom:solid 2px; border-left:solid 1px;'>
                    Completes work neatly and legibly
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 2px'>
                    Tardy
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 2px; border-right:solid 1px;'>
                    &nbsp
                </td>				
            </tr><tr>
                <td width=350 height=20 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;'>
                    Homework Grade - LA
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=79 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>	
            </tr><tr>
                <td width=350 height=20 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;'>
                    Homework Grade - MA
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
               </td><td width=15 style='font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='font-weight:bold;border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;; border-right:solid 1px;'>
                    &nbsp
                </td>	
	
            </tr><tr>
                <td width=370 height=20 style='border-right:solid 1px; border-bottom:solid 2px; border-left:solid 1px;'>
                    Homework Grade - SC/SO
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
               </td><td width=15 style='font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='font-weight:bold;border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;; border-right:solid 1px;'>
                    &nbsp
                </td>	
             </tr><tr>
                <td width=370 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
				font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Next Grade Level Assignment
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Teacher
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=15 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Principal
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr>
         </table>
 
        </td></tr>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}
}
//
//
//
function print_4footer ($clastname,$cfirstname,$printdate,$scount) {
//	
   print "</table></td></tr></table><br>";

	
    print "<tr><td style='text-align:center;font-size:2'>
 
        <table border=0 width=642 style='font-size:14; border-top:solid 1px' cellspacing=0>
            <tr>
             </tr><tr>
                <td width=350 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
				font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Next Grade Level Assignment
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Teacher
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Principal
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr>
         </table>
 
        </td></tr>";
	print "</table>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}
}
//
//
//
function print_4footer_old ($clastname,$cfirstname,$printdate,$scount) {
//	
    print "</table></td></tr></table>";

    print "<tr><td style='text-align:center;font-size:2'>
 
        <table border=0 width=659 style='font-size:14' cellspacing=0>
            <tr>
                <td width=60 style='font-weight:bold; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;
                    border-left:solid 1px; border-top:solid 1px'>
                    Behavior & Work Habits
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    1st
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    2nd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    3rd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    4th
                </td><td style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    &nbsp
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    1st
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    2nd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    3rd
                </td><td width=60 style='border-top:solid 1px; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;text-align:center'>
                    4th
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Makes good use of time and materials
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Works independently
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    Vocabulary
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
 
 
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Listens and follows directions
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Attendance
                </td><td  style='font-weight:bold;border-bottom:solid 1px; background-color:lightgray;'>
                    %
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;'>
                    &nbsp
                </td><td  style='border-bottom:solid 1px; background-color:lightgray;; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Works cooperatively with others
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td style='border-bottom:solid 1px; border-right:solid 1px'>
                    Absent
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=120 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    Respects others
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td style='border-bottom:solid 1px; border-right:solid 1px'>
                    Present
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr><tr>
                <td width=350 height=20 style='border-right:solid 1px; border-bottom:solid 2px; border-left:solid 1px;'>
                    Completes work neatly and legibly
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 2px'>
                    Tardy
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 2px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 2px; border-right:solid 1px;'>
                    &nbsp
                </td>
             </tr><tr>
                <td width=350 height=30 style='border-right:solid 1px; border-bottom:solid 1px; border-left:solid 1px;			
				font-weight:bold; border-bottom:solid 1px; background-color:lightgray;'>
                    Next Grade Level Assignment
                </td><td width=60 style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Teacher
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td style='border-right:solid 1px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px;font-size:6;text-align:center;vertical-align: bottom'>
                    Principal
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-right:solid 0px; border-bottom:solid 1px'>
                    &nbsp
                </td><td width=60 style='border-bottom:solid 1px; border-right:solid 1px;'>
                    &nbsp
                </td>
            </tr>
         </table>
 
        </td></tr>";
	print "</table>";
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='2'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}

}
//
//
//
function print_spfooter ($clastname,$cfirstname,$printdate,$scount) {
//	
    print "</table></td></tr></table>";

    print "<tr><td style='text-align:center;font-size:2'>
        <table border=0 width=659 style='font-size:14' cellspacing=0>
            <tr>
                <td width=60 style='font-weight:bold; border-bottom:solid 1px; border-right:solid 1px; background-color:lightgray;
                    border-left:solid 1px; border-top:solid 1px;;text-align:center'>
                    Behavior and Work Habits - Comment Codes
       </table>
 
        </td></tr>";		
		
		
	    print "<tr><td style='text-align:center'>
 
        <table border=0 width=659 style='font-size:14' cellspacing=0>
            <tr>
            </tr><tr>
                <td width=330 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    1. Exceptional behavior & performance		
                </td><td width=330 style='border-bottom:solid 1px; border-right:solid 1px'>
                    2. Positive and respectful attitude		
                </td>
</tr><tr>
                <td width=330 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    3. Participation/behavior is improving				
                </td><td width=330 style='border-bottom:solid 1px; border-right:solid 1px'>
                    4. Frequently disrupts instruction				
                </td>
</tr><tr>
                <td width=330 height=20 style='border-bottom:solid 1px; border-right:solid 1px; border-left:solid 1px;'>
                    5. Needs to improve respect for others			
                </td><td width=330 style='border-bottom:solid 1px; border-right:solid 1px'>
                    6. Needs to bring gym shoes to PE				
                </td>
</tr><tr>

 
            </tr>
             </tr><tr>
            </tr>			
        </table>
 
        </td></tr>";	
	print "<table border=1 cellpadding=1 width=660px>";
	print "<center><FONT SIZE='1'>";
	print $printdate;
	print " for ".$clastname.", ".$cfirstname;
	print "<center><FONT SIZE='3'>";
	print "</table></table></table>";
	If ($scount > 1) 
	{
		//echo "<br style=\"page-break-before:always\">";
		print "<div class='page-break'></div>";
	}

}

?>
