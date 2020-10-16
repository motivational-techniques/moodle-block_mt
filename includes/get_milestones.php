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
 * This gets the milestone information for a user
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/blocks/mt/includes/get_id.php");

/**
 * get the milestone module
 *
 * @param integer $id
 * @return integer
 */
function block_mt_get_milestone_module($id) {
    global $DB;
    $parameters = array(
        'id' => $id
    );
    return $DB->get_record('block_mt_p_timeline', $parameters)->module;
}

/**
 * get the milestone instance
 *
 * @param integer $id
 * @return integer
 */
function block_mt_get_milestone_instance($id) {
    global $DB;
    $parameters = array(
        'id' => $id
    );
    return $DB->get_record('block_mt_p_timeline', $parameters)->instance;
}

/**
 * get milestone id
 *
 * @param integer $module
 * @param integer $instance
 * @param integer $courseid
 * @return integer
 */
function block_mt_get_milestone_id($module, $instance, $courseid) {
    global $DB;
    $parameters = array(
        'course' => $courseid,
        'module' => $module,
        'instance' => $instance
    );
    return $DB->get_record('block_mt_p_timeline', $parameters)->id;
}

/**
 * get milestone name
 *
 * @param integer $id
 * @param integer $instance
 * @param integer $courseid
 * @return integer
 */
function block_mt_get_milestone_name($id, $instance, $courseid) {
    global $DB;

    $quizid = block_mt_get_quiz_id();
    $assignmentid = block_mt_get_assign_id();

    // Get module id.
    $parameters = array(
        'id' => $id,
        'course' => $courseid
    );
    $moduleid = $DB->get_record('block_mt_p_timeline', $parameters)->module;

    $parameters = array(
        'id' => $instance
    );
    if ($moduleid == $quizid) {
        $milestonename = $DB->get_record('quiz', $parameters)->name;
    }
    if ($moduleid == $assignmentid) {
        $milestonename = $DB->get_record('assign', $parameters)->name;
    }

    return $milestonename;
}

/**
 * get milestone name by id
 *
 * @param integer $id
 * @return string
 */
function block_mt_get_milestone_name_by_id($id) {
    global $DB;

    $quizid = block_mt_get_quiz_id();
    $assignmentid = block_mt_get_assign_id();

    $instance = block_mt_get_milestone_instance($id);
    $moduleid = block_mt_get_milestone_module($id);

    $parameters = array(
        'id' => $instance
    );
    $milestonename = null;
    if ($moduleid == $quizid) {
        $milestonename = $DB->get_record('quiz', $parameters)->name;
    }
    if ($moduleid == $assignmentid) {
        $milestonename = $DB->get_record('assign', $parameters)->name;
    }
    if ($milestonename == null) {
        $milestonename = "";
    }
    return $milestonename;
}