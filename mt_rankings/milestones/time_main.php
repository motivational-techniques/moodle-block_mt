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
 * This displays the list of milestones.
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

defined('MOODLE_INTERNAL') || die();

$pagename = get_string ( 'mt_rankings:milestone_time_main', 'block_mt' );
$pageurl = '/blocks/mt/mt_rankings/milestones/time_main.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/includes/get_milestones.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/display_menu_items.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');

global $DB, $OUTPUT, $PAGE;

$menuheader = array (
    'class' => 'menu-header'
);
echo html_writer::start_tag ('div', $menuheader);
echo html_writer::tag ('p', $pagename, $menuheader);

$menuitem = new stdClass();
$menuitem->url = '';
$menuitem->text = '';
$menuitem->rank = '';

$quizid = block_mt_get_quiz_id();
$assignmentid = block_mt_get_assign_id();

// Get Milestones.
$parameters = array(
    'course' => $courseid
);
$results = $DB->get_records('block_mt_p_timeline', $parameters, 'id');

if (count ( ( array ) $results ) > 0) {
    foreach ($results as $id => $results) {
        $milestoneurl = new stdClass ();
        $milestoneurl->name = block_mt_get_milestone_name ($results->id, $results->instance, $courseid);
        $milestoneurl->number = $results->id;
        $milestoneurl->id = $results->id;

        $urlparams = array (
            'courseid' => $courseid,
            'milestoneid' => $results->id
        );
        $urltext = get_string ( 'mt_rankings:milestone_url_text', 'block_mt', $milestoneurl );

        $parameters = array (
            'userid' => $userid,
            'courseid' => $courseid,
            'rankname' => get_string('mt_rankings:generate_rank_milestone_rank_name', 'block_mt', $milestoneurl)
        );
        if ($DB->record_exists ( 'block_mt_ranks_user', $parameters )) {
            $rank = $DB->get_record( 'block_mt_ranks_user', $parameters )->rank;
        } else {
            $rank = get_string ( 'mt_rankings:no_current_ranking', 'block_mt' );
        }

        $ranktext = get_string ( 'mt_rankings:milestone_time_current_rank', 'block_mt', $rank );

        $menuitem->url = new moodle_url ( 'milestone_time.php', $urlparams );
        $menuitem->text = $urltext;
        $menuitem->rank = get_string ( 'mt_rankings:milestone_time_current_rank', 'block_mt', $rank );

        display_menu_item($menuitem);
    }
} else {
    $menuitem->url = "";
    $menuitem->text = get_string ( 'mt_rankings:no_milestones', 'block_mt' );
    $menuitem->rank = "";

    display_menu_item($menuitem);
}
echo html_writer::end_tag ( 'div' );
echo $OUTPUT->footer();