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
 * This displays the admin options page for the progress timeline.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Biswajeet Mishra
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/mt/mt_p_timeline/mt_ptimeline_admin.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

defined('MOODLE_INTERNAL') || die();

$courseid = required_param('courseid', PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), "*", MUST_EXIST);
require_login($course);

$context = context_course::instance($courseid);
require_capability('block/mt_p_timeline:admin', $context);

block_mt_send_to_dashboard_if_no_block_installed($courseid);

// Set up the page.
$PAGE->set_url('/blocks/mt/mt_p_timeline/admin.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('mt_ptimeline:admin_heading', 'block_mt', $course->fullname));

// Make the form.
$mtptimeline = new mt_ptimeline_form ();
$toform['courseid'] = $courseid;

// Get the milestones from plugin table.
$milestones = $DB->get_records('block_mt_p_timeline', array('course' => $courseid));

// If there are milestones, get the week values.
foreach ($milestones as $id => $milestone) {

    $params = array(
        'course'   => $milestone->course,
        'module'   => $milestone->module,
        'instance' => $milestone->instance
    );
    $result = $DB->get_record('course_modules', $params);

    // If no DB result, module has been removed from course page.
    // Remove from timeline table as well.
    if (! $result) {
        $DB->delete_records('block_mt_p_timeline', $params);
        continue;
    }

    $toform['m'.$result->id] = $milestone->week;
}

// Set the form data.
$mtptimeline->set_data($toform);

$url = new moodle_url('/blocks/mt/admin.php', array('courseid' => $courseid));

if ($mtptimeline->is_cancelled()) {
    // Cancelled forms redirect to the main admin page.
    redirect($url);
} else if ($fromform = $mtptimeline->get_data()) {
    // Saved forms get form data transferred to DB.

    // Loop until we get [submitbutton].
    foreach ($fromform as $id => $from) {

        $id = substr($id, 1);
        if (is_numeric($id)) {
            add_update_milestone($id, $from);
        }
    }
    // ... then redirect to main page.
    redirect($url);
} else {
    // Form didn't validate or this is the first display.
    echo $OUTPUT->header();
    $mtptimeline->display();
    echo $OUTPUT->footer();
}

// Add to settings.
$settingsnode = $PAGE->settingsnav->add(get_string('mt_ptimeline:admin_settings', 'block_mt'));
$editurl = new moodle_url('/blocks/mt/mt_p_timeline/admin.php', array('courseid' => $courseid));

$editnode = $settingsnode->add(get_string('mt_ptimeline:admin_editpage', 'block_mt'), $editurl);
$editnode->make_active();

/**
 * Function to insert or update records in the timeline DB table.
 *
 * @param int $id The course module id
 * @param int $value The milestone week value
 */
function add_update_milestone($id, $value) {
    global $DB;

    // Get course module information.
    $milestoneinfo = $DB->get_record('course_modules', array('id' => $id), "course, module, instance");

    // Get the relevant record from the timeline table.
    $params = array (
        'course'   => $milestoneinfo->course,
        'module'   => $milestoneinfo->module,
        'instance' => $milestoneinfo->instance
    );

    $result = $DB->get_record('block_mt_p_timeline', $params);

    // Add in week value for insert/update.
    $params['week'] = $value;

    // Insert or update the record.
    if ($result) {
        $params['id'] = $result->id;
        $DB->update_record('block_mt_p_timeline', $params);
    } else {
        $DB->insert_record('block_mt_p_timeline', $params);
    }
}

