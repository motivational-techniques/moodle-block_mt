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
 * This gets the quiz start time
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();


/**
 * get quiz goal start time
 * @param string $userid
 * @param string $quizid
 * @param string $courseid
 * @return string
 */
function get_quiz_goal_start_time($userid, $quizid, $courseid) {
    global $DB;
    $goaltostartdate = null;
    $parameters = array (
            'userid' => $userid,
            'quizid' => $quizid,
            'courseid' => $courseid
    );
    if ($DB->record_exists ( 'block_mt_goals_quiz_start', $parameters )) {
        $goaltostartdate = $DB->get_record ( 'block_mt_goals_quiz_start', $parameters )->goal;
    }
    return $goaltostartdate;
}

/**
 * get quiz start time
 * @param string $userid
 * @param string $quizid
 * @return string
 */
function get_quiz_start_time($userid, $quizid) {
    global $DB;
    $started = null;
    $params = array(
        'userid' => $userid,
        'quiz' => $quizid
    );
    if ($DB->record_exists ( 'quiz_attempts', $params )) {
        $sql = "select timestart
            from {quiz_attempts}
            where userid=:userid and quiz=:quiz
            order by attempt asc";
        $started = $DB->get_record_sql ( $sql, $params, IGNORE_MULTIPLE )->timestart;
    }
    return $started;
}

/**
 * get quiz start time week
 * @param string $userid
 * @param string $quizid
 * @return string
 */
function get_quiz_start_time_week($userid, $quizid) {
    return get_course_week(get_quiz_start_time($userid, $quizid));
}

/**
 * get quiz average start time
 * @param string $quizid
 * @return string
 */
function get_quiz_average_start_time($quizid) {
    global $DB;
    $averagetime = null;
    $params = array(
        'quiz' => $quizid
    );
    if ($DB->record_exists ('quiz_attempts', $params)) {
        $sql = "select avg(timestart) as averagetime
            from {quiz_attempts}
            where quiz=:quiz";
        $averagetime = $DB->get_record_sql ( $sql, $params, IGNORE_MULTIPLE )->averagetime;
    }
    return get_course_week($averagetime);
}