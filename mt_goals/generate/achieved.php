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
 * This calculates whether a goal has been achieved
 *
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate achieved all
 * @param string $courseid
 * @return null
 */
function generate_achieved_all($courseid) {
    global $DB;

    $user = new stdClass ();
    $params = array (
        'courseid' => $courseid
    );
    $goallist = $DB->get_records('block_mt_goals_quiz', $params);
    foreach ($goallist as $goal) {
        $user->userid = $goal->userid;
        $user->courseid = $courseid;
        $user->goal_type_id = GOAL_TYPE_GRADES;
        $user->goal = $goal->goal;
        $user->goalname = block_mt_goals_get_quiz_name($courseid, $goal->quizid);
        $user->achieved = $goal->achieved;
        $user->timeachieved = block_mt_goals_get_quiz_time_achieved($goal->userid, $goal->quizid);
        add_goal($user);
    }

    // Get overall grade goals.
    $goallist = $DB->get_records('block_mt_goals_overall', $params);
    foreach ($goallist as $goal) {
        $finalgrade = block_mt_goals_get_overall_grade ($goal->userid, $courseid);
        $achieved = block_mt_goals_determine_grade_status_achieved ($goal->goal, $finalgrade);
        block_mt_goals_update_final_grade_status ($courseid, $goal->userid, $finalgrade, $achieved);
    }

    // Get assignment grade goals.
    $goallist = $DB->get_records('block_mt_goals_assign', $params);
    foreach ($goallist as $goal) {
        $assigngrade = block_mt_goals_get_assign_grade ($goal->userid, $goal->assignid);
        $achieved = block_mt_goals_determine_grade_status_achieved ($goal->goal, $assigngrade);
        block_mt_goals_update_assign_grade_status ($goal->assignid, $goal->userid, $assigngrade, $achieved);
    }

    // Get quiz grade goals.
    $goallist = $DB->get_records('block_mt_goals_quiz', $params);
    foreach ($goallist as $goal) {
        $quizgrade = block_mt_goals_get_quiz_grade ($goal->userid, $goal->quizid);
        $achieved = block_mt_goals_determine_grade_status_achieved ($goal->goal, $quizgrade);
        block_mt_goals_update_quiz_grade_status ($goal->quizid, $goal->userid, $quizgrade, $achieved);
    }
}