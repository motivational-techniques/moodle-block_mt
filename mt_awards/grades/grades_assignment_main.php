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
 * This displays a list of assignments
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$pagename = get_string ( 'mt_awards:grade_assign_main_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_awards/grades/grades_assignment_main.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/display_menu_items.php');
require($CFG->dirroot . '/blocks/mt/includes/get_assignment_list.php');
require($CFG->dirroot . "/blocks/mt/mt_awards/includes/get_grade_award_name.php");
require_once($CFG->dirroot . '/blocks/mt/includes/configuration_settings.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_mainmenu.php');

global $OUTPUT;

$counts = new stdClass ();
$counts->gold = block_mt_get_awards_settings ( 'mt_awards:grades_gold_count_value', $courseid );
$counts->silver = block_mt_get_awards_settings ( 'mt_awards:grades_silver_count_value', $courseid );
$counts->bronze = block_mt_get_awards_settings ( 'mt_awards:grades_bronze_count_value', $courseid );

echo html_writer::tag ( 'h2', get_string ( 'mt_awards:grade_assign_calc', 'block_mt', $counts ) );

$assignmentlist = block_mt_get_assignment_list($courseid);

$menuheader = array (
    'class' => 'menu-header'
);
echo html_writer::start_tag ('div', $menuheader );
echo html_writer::tag ('p', $pagename, $menuheader );

$menuitem = new stdClass();
$menuitem->url = '';
$menuitem->text = '';
$menuitem->rank = '';

if (count ( ( array ) $assignmentlist ) > 0) {
    foreach ($assignmentlist as $assignment) {
        $urlparams = array (
                'courseid' => $courseid,
                'assignmentid' => $assignment->id
        );
        $urltext = get_string ( 'mt_awards:grade_assign_main_current_award', 'block_mt', $assignment->itemname );
        $awardtext = get_current_award ( $userid, $courseid, get_grade_award_name($assignment->itemname));
        $urltext = $urltext . "<br/>" . $awardtext;

        $menuitem->url = new moodle_url ( 'grades_assignment.php', $urlparams );
        $menuitem->text = get_string ( 'mt_awards:grade_assign_main_current_award', 'block_mt', $assignment->itemname );
        $menuitem->award = $awardtext;

        display_menu_item($menuitem);
    }
} else {
    $menuitem->url = "";
    $menuitem->text = get_string ( 'mt_awards:no_assignments', 'block_mt' );
    $menuitem->award = "";

    display_menu_item($menuitem);
}

if (isset ( $urlparams ['assignmentid'] )) {
    unset ( $urlparams ['assignmentid'] );
}
echo html_writer::end_tag ( 'div' );
echo $OUTPUT->footer();