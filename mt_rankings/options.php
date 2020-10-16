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
 * This displays the options for the rankings.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/mt/mt_rankings/mt_rankings_options.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

global $DB, $OUTPUT, $PAGE, $USER;

// Check for all required variables.
$courseid = required_param ( 'courseid', PARAM_INT );
$userid = $USER->id;

$course = get_course($courseid);
require_login ( $course );

block_mt_send_to_dashboard_if_no_block_installed($courseid);

$urlparams = array (
    'courseid' => $courseid
);
$PAGE->set_url ( '/blocks/mt/mt_rankings/options.php', $urlparams );
$PAGE->set_pagelayout ( 'standard' );
$PAGE->set_heading ( get_string ( 'mt:options_rankings_heading', 'block_mt' ) );

$mtranks = new mt_rankings_options ();

$toform ['courseid'] = $courseid;
$toform ['userid'] = $userid;

$parameters = array (
    'courseid' => $courseid,
    'userid' => $userid
);

// Check if value in db.
$recordcount = $DB->count_records ( 'block_mt_ranks_prefs', $parameters);
if ($recordcount != 0) {
    $anonymous = $DB->get_record ( 'block_mt_ranks_prefs', $parameters)->anonymous;
    $toform ['displayanonymous'] = $anonymous;
} else {
    // Default to No.
    $toform ['displayanonymous'] = '0';
}

$mtranks->set_data ( $toform );

if ($mtranks->is_cancelled ()) {
    // Cancelled forms redirect to the options main page.
    $courseurl = new moodle_url ( '/blocks/mt/options.php', $urlparams);
    redirect ( $courseurl );
} else if ($fromform = $mtranks->get_data ()) {
    // If submit was clicked insert or update the selection and redirect to
    // options main page.
    $courseurl = new moodle_url ( '/blocks/mt/options.php', $urlparams);

    $recordcount = $DB->count_records ( 'block_mt_ranks_prefs', $parameters);
    if ($recordcount != 0) {
        // Update.
        $recordid = $DB->get_field ( 'block_mt_ranks_prefs', 'id', $parameters);

        $parameters['id'] = $recordid;
        $parameters['anonymous'] = $fromform->displayanonymous;
        $DB->update_record ( 'block_mt_ranks_prefs', $parameters);
    } else {
        $parameters['anonymous'] = $fromform->displayanonymous;
        $DB->insert_record ( 'block_mt_ranks_prefs', $parameters);
    }

    redirect ( $courseurl );
} else {
    // Form didn't validate or this is the first display.
    $site = get_site ();
    echo $OUTPUT->header ();
    $mtranks->display ();
    echo $OUTPUT->footer ();
}

$settingsnode = $PAGE->settingsnav->add ( get_string ( 'mt:options_rankings_settings', 'block_mt' ) );
$editurl = new moodle_url ( '/blocks/mt/options.php', $urlparams);
$editnode = $settingsnode->add ( get_string ( 'mt:options_rankings_editpage', 'block_mt' ), $editurl );
$editnode->make_active ();