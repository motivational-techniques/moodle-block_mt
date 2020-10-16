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
 * This updates the selection of the checkmarks.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

defined('MOODLE_INTERNAL') || die();

$a = explode('-', required_param('item', PARAM_TEXT)); // To separate the instance and the value.
$courseid = required_param('course', PARAM_INT);
$userid   = $USER->id;

$course = $DB->get_record('course', array('id' => $courseid), "*", MUST_EXIST);
require_login($course);
require_sesskey();

block_mt_send_to_dashboard_if_no_block_installed($courseid);

$object = $a[0];
$value  = $a[1];

// Do nothing if bad value passed.
if ($value != 1 && $value != 2 && $value != 3) {
    die();
}

// Check to ensure that the object exists in this course.
$params = array('id' => $object, 'course' => $courseid);
$result = $DB->get_record('course_modules', $params);

// Do nothing if bad object value passed.
if (! $result) {
    die();
}

// Delete old records.
$params = array(
    'object' => $object,
    'course' => $courseid,
    'userid' => $userid
);

$DB->delete_records('block_mt_annotation', $params);

// Insert new/updated value.
$params['value'] = $value;
$DB->insert_record('block_mt_annotation', $params, false);