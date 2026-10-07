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
function popup_form($common, $options, $formid, $selected='', $nothing='choose', $help='', $helptext='', $return=false,
$targetwindow='self', $selectlabel='', $optionsextra=NULL, $gobutton=NULL) {

    global $CFG, $DB;
    static $go, $choose;   /// Locally cached, in case there's lots on a page

    if (empty($options)) {
        return '';
    }

    if (!isset($go)) {
        $go = get_string('go');
    }

    if ($nothing == 'choose') {
        if (!isset($choose)) {
            $choose = get_string('choose');
        }
        $nothing = $choose.'...';
    }

    // changed reference to document.getElementById('id_abc') instead of document.abc
    // MDL-7861
    $output = '<form action="'.$CFG->wwwroot.'/course/jumpto.php"'.
                        ' method="get" '.
                         $CFG->frametarget.
                        ' id="'.$formid.'"'.
                        ' class="popupform">';

    if ($help) {
        $button = helpbutton($help, $helptext, 'moodle', true, false, '', true);
    } else {
        $button = '';
    }

    if ($selectlabel) {
        $selectlabel = '<label for="'.$formid.'_jump">'.$selectlabel.'</label>';
    }

    if ($gobutton) {
        // Using the no-JavaScript version
        $javascript = '';
    } else {
        //Other browser
        $javascript = ' onchange="'.$targetwindow.
          '.location=document.getElementById(\''.$formid.
          '\').jump.options[document.getElementById(\''.
          $formid.'\').jump.selectedIndex].value;"';
    }

    $output .= '<div>'.$selectlabel.$button.'<select id="'.$formid.'_jump" name="jump"'.$javascript.'>'."\n";

    if ($nothing != '') {
        $output .= "   <option value=\"javascript:void(0)\">$nothing</option>\n";
    }

    $inoptgroup = false;

    foreach ($options as $value => $label) {

        if ($label == '--') { /// we are ending previous optgroup
            /// Check to see if we already have a valid open optgroup
            /// XHTML demands that there be at least 1 option within an optgroup
            if ($inoptgroup and (count($optgr) > 1) ) {
                $output .= implode('', $optgr);
                $output .= '   </optgroup>';
            }
            $optgr = array();
            $inoptgroup = false;
            continue;
        } else if (substr($label,0,2) == '--') { /// we are starting a new optgroup

            /// Check to see if we already have a valid open optgroup
            /// XHTML demands that there be at least 1 option within an optgroup
            if ($inoptgroup and (count($optgr) > 1) ) {
                $output .= implode('', $optgr);
                $output .= '   </optgroup>';
            }

            unset($optgr);
            $optgr = array();

            $optgr[]  = '   <optgroup label="'. s(format_string(substr($label,2))) .'">';   // Plain labels

            $inoptgroup = true; /// everything following will be in an optgroup
            continue;

        } else {
           if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()]))
            {
                $url=sid_process_url( $common . $value );
            } else
            {
                $url=$common . $value;
            }
            $optstr = '   <option value="' . $url . '"';

            if ($value == $selected) {
                $optstr .= ' selected="selected"';
            }

            if (!empty($optionsextra[$value])) {
                $optstr .= ' '.$optionsextra[$value];
            }

            if ($label) {
                $optstr .= '>'. $label .'</option>' . "\n";
            } else {
                $optstr .= '>'. $value .'</option>' . "\n";
            }

            if ($inoptgroup) {
                $optgr[] = $optstr;
            } else {
                $output .= $optstr;
            }
        }

    }

    /// catch the final group if not closed
    if ($inoptgroup and count($optgr) > 1) {
        $output .= implode('', $optgr);
        $output .= '    </optgroup>';
    }

    $output .= '</select>';
    $output .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

    if ($gobutton) {
        $output .= '<input type="submit" value="'.
            ($gobutton===true ? $go : $gobutton).'" />';
    } else {
        $output .= '<div id="noscript'.$formid.'" style="display: inline;">';
        $output .= '<input type="submit" value="'.$go.'" /></div>';
        $output .= '<script type="text/javascript">'.
                   "\n//<![CDATA[\n".
                   'document.getElementById("noscript'.$formid.'").style.display = "none";'.
                   "\n//]]>\n".'</script>';
    }
    $output .= '</div></form>';
	//echo "THIS IS ".$output;

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

?>
