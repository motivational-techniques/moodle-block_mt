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
 * These are helper functions
 *
 * @package block_mt
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Called to check that the course with the given course id has this block installed.
 *
 * @param int $courseid The course id
 * @return boolean
 */
function block_is_installed(&$courseid) {
    global $DB;

    // Get a list of courses for which this block is installed.
    $sql = "SELECT c.id FROM {course} c
              JOIN {context} ctx ON c.id = ctx.instanceid AND ctx.contextlevel = :contextcourse
             WHERE ctx.id in (SELECT distinct parentcontextid FROM {block_instances}
                               WHERE blockname = 'mt')
          ORDER BY c.sortorder";
    $list = $DB->get_records_sql($sql, array('contextcourse' => CONTEXT_COURSE));

    // Check that the courseid passed belongs to a course where this block is installed.
    if ($list) {
        foreach ($list as $course) {
            if ($course->id == $courseid) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Redirect to dashboard if no block is installed for course.
 *
 * @param int $courseid The course id
 */
function send_to_dashboard_if_no_block_installed($courseid) {
    // Send user to dashboard if they passed a bad courseid.
    if (! block_is_installed($courseid)) {
        redirect(new moodle_url('/my'));
    }
}