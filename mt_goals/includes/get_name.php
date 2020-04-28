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
 * These are functions to get the name
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * get quiz name
 * @param string $quizid
 * @return string
 */
function get_quiz_name($quizid) {
    global $DB;
    $quizname = "";
    $parameters = array(
        'id' => $quizid
    );
    if ($DB->record_exists('quiz', $parameters)) {
        $quizname = $DB->get_record('quiz', $parameters)->name;
    }
    return $quizname;
}

/**
 * get assign name
 * @param string $courseid
 * @param string $assignid
 * @return string
 */
function get_assign_name($courseid, $assignid) {
    global $DB;
    $assignname = "";
    $parameters = array(
        'courseid' => $courseid,
        'iteminstance' => $assignid,
        'itemmodule' => 'assign'
    );
    if ($DB->record_exists('grade_items', $parameters)) {
        $assignname = $DB->get_record('grade_items', $parameters)->itemname;
    }
    return $assignname;
}

/**
 * get assignment name by id
 * @param string $assignid
 * @return string
 */
function get_assignment_name($assignid) {
    global $DB;
    $assignname = "";
    $parameters = array(
        'id' => $assignid,
    );
    if ($DB->record_exists('grade_items', $parameters)) {
        $assignname = $DB->get_record('grade_items', $parameters)->itemname;
    }
    return $assignname;
}

/**
 * get award name by id
 * @param string $awardid
 * @return string
 */
function get_award_name_byid($awardid) {
    global $DB;
    $awardname = "";
    $parameters = array (
        'id' => $awardid
    );

    if ($DB->record_exists ( 'block_mt_awards_user', $parameters )) {
        $awardname = $DB->get_record ( 'block_mt_awards_user', $parameters )->award_name;
    }
    return $awardname;
}

/**
 * get rank name by id
 * @param string $rankid
 * @return string
 */
function get_rank_name_byid($rankid) {
    global $DB;
    $rankname = "";
    $parameters = array (
        'id' => $rankid
    );

    if ($DB->record_exists ( 'block_mt_ranks_user', $parameters )) {
        $rankname = $DB->get_record ( 'block_mt_ranks_user', $parameters )->rankname;
    }
    return $rankname;
}