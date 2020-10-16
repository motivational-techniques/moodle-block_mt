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
 * This is the admin page for teacher options
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');

require_once($CFG->dirroot . '/blocks/mt/mt_goals/mt_goals_admin.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

global $DB, $OUTPUT, $PAGE;

$courseid = required_param ( 'courseid', PARAM_INT );

$course = get_course($courseid);
require_login ( $course );
require_capability('block/mt_goals:admin', context_course::instance($courseid));

block_mt_send_to_dashboard_if_no_block_installed($courseid);

$coursename = $course->fullname;

$PAGE->set_url ( '/blocks/mt/mt_goals/admin.php', array (
        'courseid' => $courseid
) );
$PAGE->set_pagelayout ( 'standard' );
$PAGE->set_heading ( get_string ( 'mt_goals:admin_header_config', 'block_mt' ) . ' for '.$coursename);

$mtranks = new mt_goals_form ();

$toform ['courseid'] = $courseid;

$mtranks->set_data ( $toform );

if ($mtranks->is_cancelled ()) {
    // Cancelled forms redirect to the main admin page.
    $courseurl = new moodle_url ( '/blocks/mt/admin.php', array (
            'courseid' => $courseid
    ) );
    redirect ( $courseurl );
} else if ($fromform = $mtranks->get_data ()) {
    // If submit was clicked insert or update the selection and redirect to the main admin page.
    $courseurl = new moodle_url ( '/blocks/mt/admin.php', array (
            'courseid' => $courseid) );

    redirect ( $courseurl );
} else {
    // Form didn't validate or this is the first display.
    $site = get_site ();
    echo $OUTPUT->header ();
    $mtranks->display ();
    echo $OUTPUT->footer ();
}

$settingsnode = $PAGE->settingsnav->add ( get_string ( 'mt_goals:admin_settings', 'block_mt' ) );
$editurl = new moodle_url ( '/blocks/mt/mt_goals/admin.php', array (
        'courseid' => $courseid
) );
$editnode = $settingsnode->add ( get_string ( 'mt_goals:admin_edit_page', 'block_mt' ), $editurl );
$editnode->make_active ();