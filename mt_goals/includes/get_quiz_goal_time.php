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
 * This gets the quiz goal time
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * get quiz goal time start
 * @param string $userid
 * @param string $quizid
 * @param string $courseid
 * @return string
 */
function block_mt_goals_get_quiz_goal_time_start($userid, $quizid, $courseid) {
    global $DB;
    $goal = null;
    $params = array (
                'courseid' => $courseid,
                'userid' => $userid,
                'quizid' => $quizid
        );
    if ($DB->record_exists ( 'block_mt_goals_quiz_start', $params )) {
        $goal = $DB->get_record ( 'block_mt_goals_quiz_start', $params )->goal;
    } else {
        $goal = null;
    }
    return block_mt_goals_get_course_week($goal);
}

/**
 * get quiz goal time completed
 * @param string $userid
 * @param string $quizid
 * @param string $courseid
 * @return string
 */
function block_mt_goals_get_quiz_goal_time_completed($userid, $quizid, $courseid) {
    global $DB;
    $goal = null;
    $params = array (
                'courseid' => $courseid,
                'userid' => $userid,
                'quizid' => $quizid
        );
    if ($DB->record_exists ( 'block_mt_goals_quiz_comp', $params )) {
        $goal = $DB->get_record ( 'block_mt_goals_quiz_comp', $params )->goal;
    } else {
        $goal = null;
    }
    return block_mt_goals_get_course_week($goal);
}