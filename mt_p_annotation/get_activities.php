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
 * This shows a list of learning objects.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/mt/mt_p_annotation/locallib.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

defined('MOODLE_INTERNAL') || die();

$courseid = required_param('courseid', PARAM_INT);
$value    = required_param('value', PARAM_INT);
$userid   = $USER->id;

$course = $DB->get_record('course', array('id' => $courseid), "*", MUST_EXIST);
require_login($course);

// Determine module completion status and value.
$status = get_string('mt_p_annotation:chart_doing', 'block_mt');
if ($value == 1) {
    $status = get_string('mt_p_annotation:chart_done', 'block_mt');
} else if ($value == 2) {
    $status = get_string('mt_p_annotation:chart_not_done', 'block_mt');
} else {
    $value = 3;
}

$pageurl = '/blocks/mt/mt_p_annotation/get_activities.php';

block_mt_send_to_dashboard_if_no_block_installed($courseid);

// Set up the page.
$PAGE->set_url($pageurl, array('courseid' => $courseid, 'value' => $value));
$PAGE->set_title(get_string('mt_p_annotation:list_objects_title', 'block_mt', array('status' => $status)));

$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);

// SQL for completed and in progress modules.
$sql = "SELECT A.* FROM {block_mt_instancenames} A, {block_mt_annotation} B
         WHERE B.course = :courseid
           AND B.userid = :userid
           AND B.value  = :value
           AND B.object = A.instanceid";

// SQL for incomplete modules.
if ($value == 2) {
    $sql = "SELECT * FROM {block_mt_instancenames}
             WHERE courseid = :cid
               AND instanceid NOT IN (
                   SELECT object FROM {block_mt_annotation}
                    WHERE course = :courseid AND userid = :userid AND value != 2)";
}

$results = $DB->get_records_sql($sql, array(
    'courseid' => $courseid,
    'userid'   => $userid,
    'value'    => $value,
    'cid'      => $courseid
));

// Get the pretty HTML module names with icons.
$modnames = populate_instancenames($courseid);

// Output the page.
echo $OUTPUT->header();

// Back button.
echo html_writer::tag('input', '', array(
    'type'    => "button",
    'value'   => get_string('mt_p_annotation:getinfo_back', 'block_mt'),
    'onclick' => 'goBack()'));

echo html_writer::tag('script', 'function goBack() { window.history.back(); }');

echo html_writer::empty_tag('br');
echo html_writer::empty_tag('br');

// The list of learning objects.
foreach ($results as $result) {
    echo html_writer::tag('p', $modnames[$result->instanceid]);
}

echo $OUTPUT->footer();

