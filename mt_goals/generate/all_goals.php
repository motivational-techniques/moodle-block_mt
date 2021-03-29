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
 * This generates the goals
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * add goal
 * @param array $user
 * @return null
 */
function add_goal($user) {
    global $DB;
    $parameters = array (
            'userid' => $user->userid,
            'courseid' => $user->courseid,
            'goalname' => $user->goalname
    );
    $recordcount = $DB->count_records ( 'block_mt_goals_user', $parameters );
    if ($recordcount < 1) {
        $parameters ['achieved'] = $user->achieved;
        $parameters ['timeachieved'] = $user->timeachieved;
        $parameters ['goal'] = $user->goal;
        $DB->insert_record ( 'block_mt_goals_user', $parameters );
    } else {
        $parameters ['id'] = $DB->get_field ( 'block_mt_goals_user', 'id', $parameters );
        $parameters ['achieved'] = $user->achieved;
        $parameters ['timeachieved'] = $user->timeachieved;
        $parameters ['goal'] = $user->goal;
        $DB->update_record ( 'block_mt_goals_user', $parameters );
    }
}

/**
 * generate all goals
 * @param string $courseid
 * @return null
 */
function generate_goal_all($courseid) {
    add_assignment_grade_goals($courseid);

    add_quiz_grade_goals($courseid);

    add_overall_grade_goals($courseid);

    add_quiz_to_start_goals($courseid);

    add_quiz_to_complete_goals($courseid);

    add_assignment_to_complete_goals($courseid);

    add_ranks_goals($courseid);

    add_awards_goals($courseid);
}

/**
 * add assignment grade goals
 * @param string $courseid
 * @return array
 */
function add_assignment_grade_goals($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid
    );
    $goaldata = $DB->get_records ( 'block_mt_goals_assign', $parameters);
    foreach ($goaldata as $goal) {
        $user = new stdClass ();
        $user->userid = $goal->userid;
        $user->courseid = $courseid;
        $user->goal_type_id = GOAL_TYPE_GRADES;
        $user->goal = $goal->goal;
        $user->goalname = get_string('mt_awards:generate_grade_name', 'block_mt',
                block_mt_ get_assignment_name($goal->assignid));
        $user->achieved = $goal->achieved;
        $user->timeachieved = get_assign_time_achieved($goal->userid, $goal->assignid );
        add_goal($user);
    }
}

/**
 * add quiz grade goals
 * @param string $courseid
 * @return array
 */
function add_quiz_grade_goals($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid
    );
    $goaldata = $DB->get_records ( 'block_mt_goals_quiz', $parameters);
    foreach ($goaldata as $goal) {
        $user = new stdClass ();
        $user->userid = $goal->userid;
        $user->courseid = $courseid;
        $user->goal_type_id = GOAL_TYPE_GRADES;
        $user->goal = $goal->goal;
        $user->goalname = get_string('mt_awards:generate_grade_name', 'block_mt', get_quiz_name ($goal->quizid));
        $user->achieved = $goal->achieved;
        $user->timeachieved = get_quiz_time_achieved ( $goal->userid, $goal->quizid );
        add_goal($user);
    }
}

/**
 * add overall grade goals
 * @param string $courseid
 * @return array
 */
function add_overall_grade_goals($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid
    );
    $goaldata = $DB->get_records ( 'block_mt_goals_overall', $parameters);
    foreach ($goaldata as $goal) {
        $user = new stdClass ();
        $user->userid = $goal->userid;
        $user->courseid = $courseid;
        $user->goal_type_id = GOAL_TYPE_GRADES;
        $user->goal = $goal->goal;
        $user->goalname = get_string('mt_goals:generate_overall_grade', 'block_mt');
        $user->achieved = $goal->achieved;
        $user->timeachieved = $goal->timeachieved;
        add_goal($user);
    }
}

/**
 * add quiz to start goals
 * @param string $courseid
 * @return array
 */
function add_quiz_to_start_goals($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid
    );
    $goaldata = $DB->get_records ( 'block_mt_goals_quiz_start', $parameters);
    foreach ($goaldata as $goal) {
        $quizname = get_quiz_name ($goal->quizid);
        $user = new stdClass ();
        $user->userid = $goal->userid;
        $user->courseid = $courseid;
        $user->goal_type_id = GOAL_TYPE_TIME;
        $user->goal = $goal->goal;
        $user->goalname = get_string('mt_goals:generate_quiz_to_start', 'block_mt', $quizname);
        $user->achieved = $goal->achieved;
        $user->timeachieved = get_quiz_time_achieved ( $goal->userid, $goal->quizid );
        add_goal($user);
    }
}

/**
 * add quiz to complete goals
 * @param string $courseid
 * @return array
 */
function add_quiz_to_complete_goals($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid
    );
    $goaldata = $DB->get_records ( 'block_mt_goals_quiz_comp', $parameters);
    foreach ($goaldata as $goal) {
        $quizname = get_quiz_name ($goal->quizid);
        $user = new stdClass ();
        $user->userid = $goal->userid;
        $user->courseid = $courseid;
        $user->goal_type_id = GOAL_TYPE_TIME;
        $user->goal = $goal->goal;
        $user->goalname = get_string('mt_goals:generate_quiz_to_complete', 'block_mt', $quizname);
        $user->achieved = $goal->achieved;
        $user->timeachieved = get_quiz_time_achieved ( $goal->userid, $goal->quizid );
        add_goal($user);
    }
}

/**
 * add assignment to complete goals
 * @param string $courseid
 * @return array
 */
function add_assignment_to_complete_goals($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid
    );
    $goaldata = $DB->get_records ( 'block_mt_goals_assign_comp', $parameters);
    foreach ($goaldata as $goal) {
        $assignname = get_assign_name ($courseid, $goal->assignid);
        $user = new stdClass ();
        $user->userid = $goal->userid;
        $user->courseid = $courseid;
        $user->goal_type_id = GOAL_TYPE_TIME;
        $user->goal = $goal->goal;
        $user->goalname = get_string('mt_goals:generate_assign_to_complete', 'block_mt', $assignname);
        $user->achieved = $goal->achieved;
        $user->timeachieved = $goal->timeachieved;
        add_goal($user);
    }
}

/**
 * add ranks goals
 * @param string $courseid
 * @return array
 */
function add_ranks_goals($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid
    );
    $goaldata = $DB->get_records ( 'block_mt_goals_ranks', $parameters);
    foreach ($goaldata as $goal) {
        $rankname = get_rank_name_byid ( $goal->ranktypeid);
        $user = new stdClass ();
        $user->userid = $goal->userid;
        $user->courseid = $courseid;
        $user->goal_type_id = GOAL_TYPE_RANKS;
        $user->goal = $goal->goal;
        $user->goalname = get_string('mt_goals:generate_rank', 'block_mt', $rankname);
        $user->achieved = $goal->achieved;
        $user->timeachieved = $goal->timeachieved;
        add_goal($user);
    }
}

/**
 * add awards goals
 * @param string $courseid
 */
function add_awards_goals($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid
    );
    $goaldata = $DB->get_records ( 'block_mt_goals_awards', $parameters);
    foreach ($goaldata as $goal) {
        $awardname = get_award_name_byid ( $goal->awardid);
        $user = new stdClass ();
        $user->userid = $goal->userid;
        $user->courseid = $courseid;
        $user->goal_type_id = GOAL_TYPE_AWARDS;
        $user->goal = $goal->goal;
        $user->goalname = get_string('mt_goals:generate_award', 'block_mt', $awardname);
        $user->achieved = $goal->achieved;
        $user->timeachieved = $goal->timeachieved;
        add_goal($user);
    }
}