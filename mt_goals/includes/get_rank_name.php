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
 * This gets the rank name
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/blocks/mt/includes/determine_rank_name.php");
require_once($CFG->dirroot . "/blocks/mt/includes/get_id.php");

/**
 * get rank name
 * @param array $param
 * @return string
 */
function block_mt_get_rank_name($param) {
    switch ($param->rank_type_id) {
        case RANK_TYPE_GRADES :
            $rankname = get_string ( 'mt_goals:grades', 'block_mt_goals' , $param->rankname);
            break;
        case RANK_TYPE_ONLINE_TIME :
            $rankname = get_string ( 'mt_goals:onlinetime', 'block_mt_goals' );
            $rankname = determine_overall_or_period_rankname($param->period_type, $rankname, 'goal');
            break;
        case RANK_TYPE_NUMBER_POSTS :
            $rankname = get_string ( 'mt_goals:numposts', 'block_mt_goals' );
            $rankname = determine_overall_or_period_rankname($param->period_type, $rankname, 'goal');
            break;
        case RANK_TYPE_WEEKLY_POSTS :
            $rankname = get_string ( 'mt_goals:weeklyposts', 'block_mt_goals' );
            $rankname = determine_overall_or_period_rankname($param->period_type, $rankname, 'goal');
            break;
        case RANK_TYPE_POST_RATING :
            $rankname = get_string ( 'mt_goals:averagepostrating', 'block_mt_goals' );
            $rankname = determine_overall_or_period_rankname($param->period_type, $rankname, 'goal');
            break;
        case RANK_TYPE_MILESTONE :
            $rankname = get_string ( 'mt_goals:timemilestone', 'block_mt_goals' );
            if ($param->period_type == RANK_PERIOD_OVERALL) {
                $rankname = get_string ( 'mt_goals:overall', 'block_mt_goals', $rankname );
            } else {
                $milestoneid = get_milestone_id ( $param->gradeid, $param->period_type, $param->courseid );

                $milestonename = get_milestone_name ( $milestoneid, $param->period_type, $param->courseid );
                $rankname = $rankname . ' - ' . $milestonename;
            }
            break;
        case RANK_TYPE_ACHIEVEMENT :
            $rankname = get_string ( 'mt_goals:achievements', 'block_mt_goals' );
            $rankname = determine_overall_or_period_rankname($param->period_type, $rankname, 'goal');
            break;
        default :
            break;
    }
    return $rankname;
}

/**
 * get milestone module
 * @param string $id
 * @return string
 */
function block_mt_get_milestone_module($id) {
    global $DB;

    $parameters = array (
        'id' => $id
    );
    return $DB->get_record('mt_p_timeline', $parameters)->module;
}
/**
 * get milestone instance
 * @param string $id
 * @return string
 */
function block_mt_get_milestone_instance($id) {
    global $DB;

    $parameters = array (
            'id' => $id
    );
    return $DB->get_record('mt_p_timeline', $parameters)->instanceid;
}

/**
 * get milestone id
 * @param string $module
 * @param string $instance
 * @param string $courseid
 * @return string
 */
function block_mt_get_milestone_id($module, $instance, $courseid) {
    global $DB;
    $sql = "SELECT id
            FROM {mt_p_timeline}
            WHERE course=:course
            AND module=:module
            AND instance=:instance";
    return $DB->get_record_sql ( $sql, array (
        'course' => $courseid,
        'module' => $module,
        'instance' => $instance
    ) )->id;
}

/**
 * get milestone id
 * @param string $id
 * @param string $instance
 * @param string $courseid
 * @return string
 */
function block_mt_get_milestone_name($id, $instance, $courseid) {
    global $DB;

    $quizid = get_quiz_id();
    $assignmentid = get_assign_id();

    // Get module id.
    $sql = "SELECT module
            FROM {mt_p_timeline}
            WHERE id=:id
            AND course=:course";
    $moduleid = $DB->get_record_sql ( $sql, array (
        'id' => $id,
        'course' => $courseid
    ) )->module;

    if ($moduleid == $quizid) {
        $sql = "SELECT name
                FROM {quiz}
                WHERE id=:id";
    }
    if ($moduleid == $assignmentid) {
        $sql = "SELECT name
                FROM {assign}
                WHERE id=:id";
    }
    return $DB->get_record_sql ( $sql, array (
        'id' => $instance
    ) )->name;
}

/**
 * get milestone name by id
 * @param string $id
 * @return string
 */
function block_mt_get_milestone_name_by_id($id) {
    global $DB;

    $quizid = get_quiz_id();
    $assignmentid = get_assign_id();

    $instance = block_mt_get_milestone_instance($id);
    $moduleid = block_mt_get_milestone_module($id);

    if ($moduleid == $quizid) {
        $sql = "SELECT name
                FROM {quiz}
                WHERE id=:id";
    }
    if ($moduleid == $assignmentid) {
        $sql = "SELECT name
                FROM {assign}
                WHERE id=:id";
    }
    return $DB->get_record_sql ( $sql, array (
        'id' => $instance
    ) )->name;
}