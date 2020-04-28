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
 * This is the admin page for teacher options.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/mt/mt_p_annotation/mt_p_annotation_admin.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

defined('MOODLE_INTERNAL') || die();

$courseid = required_param('courseid', PARAM_INT);
require_login($courseid);

require_capability('block/mt_p_annotation:admin', context_course::instance($courseid));

send_to_dashboard_if_no_block_installed($courseid);

// Set up the page.
$coursename = $DB->get_record('course', array('id' => $courseid))->shortname;
$PAGE->set_url('/blocks/mt/mt_p_annotation/admin.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('mt_p_annotation:admin_header_config', 'block_mt', $coursename));

// Make the form.
$mtranks = new mt_p_annotation_form ();

$toform['courseid'] = $courseid;

$mtranks->set_data($toform);

$courseurl = new moodle_url('/blocks/mt/admin.php', array('courseid' => $courseid));

if ($mtranks->is_cancelled()) {
    // Cancelled forms redirect to the main admin page.
    redirect($courseurl);
} else if ($fromform = $mtranks->get_data()) {
    // If submit was clicked insert or update the selection and redirect to the main admin page.
    redirect($courseurl);
} else {
    // Form didn't validate or this is the first display.
    echo $OUTPUT->header();
    $mtranks->display();
    echo $OUTPUT->footer();
}

// Add the settings.
$settingsnode = $PAGE->settingsnav->add(get_string('mt_p_annotation:admin_settings', 'block_mt'));
$editurl = new moodle_url('/blocks/mt/mt_p_annotation/admin.php', array('courseid' => $courseid));

$editnode = $settingsnode->add(get_string('mt_p_annotation:admin_edit_page', 'block_mt'), $editurl);
$editnode->make_active();
