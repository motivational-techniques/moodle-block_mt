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
 * This displays the options page for the awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');

require_once($CFG->dirroot . '/blocks/mt/mt_awards/mt_awards_options.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

global $DB, $OUTPUT, $PAGE;

// Check for all required variables.
$courseid = required_param ( 'courseid', PARAM_INT );
$userid = $USER->id;

$course = get_course($courseid);
require_login ( $course );

block_mt_send_to_dashboard_if_no_block_installed($courseid);

$PAGE->set_url ( '/blocks/mt/mt_awards/options.php', array (
        'courseid' => $courseid
) );
$PAGE->set_pagelayout ( 'standard' );
$PAGE->set_heading ( get_string ( 'mt:options_awards_heading', 'block_mt' ) );

$mtawards = new mt_awards_options ();

$toform ['courseid'] = $courseid;
$toform ['userid'] = $userid;

// Check if value in db.
$recordcount = $DB->count_records ( 'block_mt_awards_pref', array (
        'courseid' => $courseid,
        'userid' => $userid
) );
if ($recordcount != 0) {
    // Get value.
    $anonymous = $DB->get_record ( 'block_mt_awards_pref', array (
            'courseid' => $courseid,
            'userid' => $userid
    ) )->anonymous;
    $toform ['displayanonymous'] = $anonymous;
} else {
    // Default to No.
    $toform ['displayanonymous'] = '0';
}

$mtawards->set_data ( $toform );

if ($mtawards->is_cancelled ()) {
    // Cancelled forms redirect to the options main page.
    $courseurl = new moodle_url ( '/blocks/mt/options.php', array (
            'courseid' => $courseid
    ) );
    redirect ( $courseurl );
} else if ($fromform = $mtawards->get_data ()) {
    // If submit was clicked insert or update the selection and redirect to options main page.
    $courseurl = new moodle_url ( '/blocks/mt/options.php', array (
            'courseid' => $courseid
    ) );

    $recordcount = $DB->count_records ( 'block_mt_awards_pref', array (
            'courseid' => $courseid,
            'userid' => $userid
    ) );
    if ($recordcount != 0) {
        // Update.
        $recordid = $DB->get_field ( 'block_mt_awards_pref', 'id', array (
                'courseid' => $courseid,
                'userid' => $userid
        ) );
        $DB->update_record ( 'block_mt_awards_pref', array (
                'courseid' => $courseid,
                'userid' => $userid,
                'id' => $recordid,
                'anonymous' => $fromform->displayanonymous
        ) );
    } else {
        $DB->insert_record ( 'block_mt_awards_pref', array (
                'courseid' => $courseid,
                'userid' => $userid,
                'anonymous' => $fromform->displayanonymous
        ) );
    }

    redirect ( $courseurl );
} else {
    // Form didn't validate or this is the first display.
    $site = get_site ();
    echo $OUTPUT->header ();
    $mtawards->display ();
    echo $OUTPUT->footer ();
}

$settingsnode = $PAGE->settingsnav->add ( get_string ( 'mt:options_awards_settings', 'block_mt' ) );
$editurl = new moodle_url ( '/blocks/mt/options.php', array (
        'courseid' => $courseid
) );
$editnode = $settingsnode->add ( get_string ( 'mt:options_awards_editpage', 'block_mt' ), $editurl );
$editnode->make_active ();
