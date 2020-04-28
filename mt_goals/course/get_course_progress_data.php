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
 * This gets the generates the graph data
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require($CFG->dirroot . '/blocks/mt/mt_goals/includes/add_data_progress_graph.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_assign_completed_time.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_assign_goal_time.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_name.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_quiz_completed_time.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_quiz_goal_time.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_quiz_start_time.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_course_week.php');

/**
 * Get chart data
 * @param string $courseid
 * @param string $userid
 * @return array
 */
function get_chart_data(&$courseid, &$userid) {
    global $DB;

    $table = array ();
    $table ['cols'] = array (
            array (
                'id' => '',
                'label' => get_string('mt_goals:course_progress_goal', 'block_mt'),
                'pattern' => '',
                'type' => 'string'
            ),
            array (
                'id' => '',
                'label' => get_string('mt_goals:course_progress_my_goal', 'block_mt'),
                'pattern' => '',
                'type' => 'number'
            ),
            array (
                'id' => '',
                'label' => get_string('mt_goals:course_progress_my_progress', 'block_mt'),
                'pattern' => '',
                'type' => 'number'
            )
    );
    $params = array (
            'course' => $courseid
    );
    $quizdata = $DB->get_records('quiz', $params, 'timemodified');
    foreach ($quizdata as $id => $quizdata) {
        $quizid = $quizdata->id;
        $data = new stdClass ();
        $data->name = get_quiz_name($quizdata->id );
        $data->goaltext = get_string('mt_goals:course_progress_to_start', 'block_mt');
        $data->progress = get_quiz_start_time_week($userid, $quizid);
        $data->goal = get_quiz_goal_time_start($userid, $quizid, $courseid);
        $rows [] = add_data_progress_graph($data );
    }

    $quizdata = $DB->get_records('quiz', $params, 'timemodified');
    foreach ($quizdata as $id => $quizdata) {
        $quizid = $quizdata->id;
        $data = new stdClass ();
        $data->name = get_quiz_name($quizdata->id);
        $data->goaltext = get_string('mt_goals:course_progress_to_complete', 'block_mt');
        $data->progress = get_quiz_completed_time_week($userid, $quizid);
        $data->goal = get_quiz_goal_time_completed($userid, $quizid, $courseid);
        $rows [] = add_data_progress_graph($data);
    }

    $assigndata = $DB->get_records('assign', $params, 'timemodified');
    foreach ($assigndata as $id => $assigndata) {
        $assignid = $assigndata->id;
        $data = new stdClass ();
        $data->name = get_assign_name($courseid, $assigndata->id);
        $data->goaltext = get_string('mt_goals:course_progress_to_complete', 'block_mt');
        $data->progress = get_assign_completed_time_week($userid, $assigndata->id);
        $data->goal = get_assign_goal_time_week($userid, $assignid, $courseid);
        $rows [] = add_data_progress_graph($data);
    }

    $table ['rows'] = $rows;
    return $table;
}