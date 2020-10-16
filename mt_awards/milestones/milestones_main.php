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
 * This displays the list of milestones
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

defined('MOODLE_INTERNAL') || die();

$pagename = get_string('mt_awards:milestones_main_page_name', 'block_mt');
$pageurl = '/blocks/mt/mt_awards/milestones/milestones_main.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/../includes/get_milestones.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/display_menu_items.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_mainmenu.php');

global $DB, $OUTPUT;

$menuheader = array (
    'class' => 'menu-header'
);
echo html_writer::start_tag ('div', $menuheader );
echo html_writer::tag ('p', $pagename, $menuheader );

$menuitem = new stdClass();
$menuitem->url = '';
$menuitem->text = '';
$menuitem->rank = '';

$parameters = array(
    'course' => $courseid
);
$results = $DB->get_records('block_mt_p_timeline', $parameters);
if (count((array) $results) > 0) {
    foreach ($results as $id => $results) {
        $milestonename = block_mt_get_milestone_name_by_id($results->id);

        $urlparams = array(
            'courseid' => $courseid,
            'instanceid' => $results->id
        );
        $results->milestone_name = $milestonename;
        $urltext = get_string('mt_awards:milestones_main_current_award', 'block_mt', $results);
        $awardtext = get_current_award($userid, $courseid, $urltext);
        $urltext = $urltext . "<br/>" . $awardtext;

        $menuitem->url = new moodle_url ( 'milestones.php', $urlparams );
        $menuitem->text = get_string ( 'mt_awards:milestones_main_current_award', 'block_mt', $results);
        $menuitem->award = $awardtext;

        display_menu_item($menuitem);
    }
} else {
    $menuitem->url = "";
    $menuitem->text = get_string ( 'mt_awards:no_milestones', 'block_mt' );
    $menuitem->award = "";

    display_menu_item($menuitem);
}
echo html_writer::end_tag ( 'div' );
echo $OUTPUT->footer();