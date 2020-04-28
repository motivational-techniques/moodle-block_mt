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
 * This is to add/update or delete the goal awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../config.php');

require_once($CFG->dirroot . '/blocks/mt/mt_goals/ranking_award/award_settings_base.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

$awardid = required_param ( 'awardid', PARAM_INT );
$courseid = required_param ( 'courseid', PARAM_INT );
$userid = $USER->id;

$course = get_course($courseid);
require_login ( $course );

send_to_dashboard_if_no_block_installed($courseid);

$coursename = $course->fullname;

$pagename = get_string ( 'mt_goals:award_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_goals/ranking_award/award_settings.php';
$PAGE->set_url ( $pageurl, array (
    'courseid' => $courseid,
    'awardid'  => $awardid
) );
$logmtpage = true;

global $DB, $OUTPUT, $PAGE;

$PAGE->set_pagelayout('standard');
$PAGE->set_heading ( get_string ( 'mt_goals:award_page_name', 'block_mt', $coursename ) );
$PAGE->requires->jquery ();
$PAGE->requires->jquery_plugin ( 'ui' );
$PAGE->requires->jquery_plugin ( 'ui-css' );
$PAGE->requires->js ( new moodle_url ( '/blocks/mt/mt_goals/includes/range.js' ) );

$mtgoals = new mt_goals_award_form ();

$toform ['courseid'] = $courseid;
$toform ['userid'] = $userid;
$toform ['awardid'] = $awardid;

$mtgoals->set_data ( $toform );

if ($mtgoals->is_cancelled ()) {
    // Cancelled forms redirect to the course main page.
    $courseurl = new moodle_url ( '/blocks/mt/mt_goals/ranking_award/award.php', array (
            'courseid' => $courseid
    ) );
    redirect ( $courseurl );
} else if ($fromform = $mtgoals->get_data ()) {
    // If submit was clicked insert or update the selection and redirect to course main page.
    if (isset($fromform->goal)) {
        $goal = $fromform->goal;

        // Insert or update.
        $params = array (
            'courseid' => $courseid,
            'awardid' => $awardid,
            'userid' => $userid
        );
        if ($DB->record_exists ( 'block_mt_goals_awards', $params, false )) {
            $recordid = $DB->get_record ( 'block_mt_goals_awards', $params )->id;
            $params ["id"] = $recordid;
            $params ['goal'] = $goal;
            $DB->update_record ( 'block_mt_goals_awards', $params, false );
        } else {
            $params ['goal'] = $goal;
            $DB->insert_record ( 'block_mt_goals_awards', $params, false );
        }
    }
    $courseurl = new moodle_url ( '/blocks/mt/mt_goals/ranking_award/award.php', array (
            'courseid' => $courseid
    ) );

    redirect ( $courseurl );
} else {
    // Form didn't validate or this is the first display.
    $site = get_site ();
    echo $OUTPUT->header ();
    $mtgoals->display ();
    echo $OUTPUT->footer ();
}

$settingsnode = $PAGE->settingsnav->add ( get_string ( 'mt_goals:admin_settings', 'block_mt' ) );
$editurl = new moodle_url ( '/blocks/mt/mt_goals/ranking_award/award.php', array (
        'courseid' => $courseid
) );
$editnode = $settingsnode->add ( get_string ( 'mt_goals:admin_editpage', 'block_mt' ), $editurl );
$editnode->make_active ();
