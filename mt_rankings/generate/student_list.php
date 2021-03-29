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
 * This gives student listings
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * returns students in course
 * @param integer $courseid
 * @return array
 */
function block_mt_students_in_course($courseid) {
    global $DB;
    // Get users that are students roleid=5 and active in the course.
    $sql = "SELECT {logstore_standard_log}.userid
            FROM {logstore_standard_log}
            JOIN {role_assignments}
            ON {logstore_standard_log}.userid={role_assignments}.userid
            WHERE courseid=:course and roleid=5
            GROUP BY {logstore_standard_log}.userid";
    $parameters = array(
                'course' => $courseid);
    return $DB->get_records_sql($sql, $parameters);
}

/**
 * returns students in course forum
 * @param integer $courseid
 * @return array
 */
function block_mt_students_in_course_forum($courseid) {
    global $DB;
    // Get users that are students roleid=5 and active in the course.
    $sql = "SELECT {forum_posts}.userid
            FROM {forum_posts}
            JOIN {forum_discussions}
            ON {forum_discussions}.id={forum_posts}.discussion
            JOIN {role_assignments}
            ON {forum_posts}.userid={role_assignments}.userid
            WHERE course=:course and roleid=5
            GROUP BY {forum_posts}.userid";
    $parameters = array('course' => $courseid);
    return $DB->get_records_sql($sql, $parameters);
}