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
 * This gets the quiz completed time
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * get quiz goald completed time
 * @param string $userid
 * @param string $quizid
 * @param string $courseid
 * @return string
 */
function block_mt_goals_get_quiz_goal_completed_time($userid, $quizid, $courseid) {
    global $DB;
    $goaltocompletedate = null;
    $parameters = array (
            'userid' => $userid,
            'quizid' => $quizid,
            'courseid' => $courseid
    );
    if ($DB->record_exists ( 'block_mt_goals_quiz_comp', $parameters )) {
        $goaltocompletedate = $DB->get_record ( 'block_mt_goals_quiz_comp', $parameters )->goal;
    }
    return $goaltocompletedate;
}

/**
 * get quiz completed time
 * @param string $userid
 * @param string $quizid
 * @return string
 */
function block_mt_goals_get_quiz_completed_time($userid, $quizid) {
    global $DB;
    $completed = null;
    $params = array (
            'userid' => $userid,
            'quiz' => $quizid
    );
    if ($DB->record_exists ( 'quiz_attempts', $params )) {
        $compltime = $DB->get_records( 'quiz_attempts', $params, 'attempt desc', 'timefinish', 0, 1);
        $completed = array_shift($compltime)->timefinish;
    }
    if ($completed == 0) {
        $completed = null;
    }
    return $completed;
}

/**
 * get quiz completed time week
 * @param string $userid
 * @param string $quizid
 * @return string
 */
function block_mt_goals_get_quiz_completed_time_week($userid, $quizid) {
    return block_mt_goals_get_course_week(block_mt_goals_get_quiz_completed_time($userid, $quizid));
}

/**
 * get quiz average completed time
 * @param string $quizid
 * @return string
 */
function block_mt_goals_get_quiz_average_completed_time($quizid) {
    global $DB;

    $averagetime = null;
    $params = array(
        'quiz' => $quizid
    );
    if ($DB->record_exists ( 'quiz_attempts', $params )) {
        $sql = "select avg(timefinish) as averagetime
                from {quiz_attempts}
                where quiz=:quiz";
        $averagetime = $DB->get_record_sql ( $sql, $params, IGNORE_MULTIPLE )->averagetime;
    } else {
        $averagetime = null;
    }
    return block_mt_goals_get_course_week($averagetime);
}