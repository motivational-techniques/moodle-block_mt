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
 * This gets the grades a user
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * get overall grade
 * @param string $userid
 * @param string $courseid
 * @return string
 */
function get_overall_grade($userid, $courseid) {
    global $DB;
    $finalgrade = null;

    $params = array(
        'courseid' => $courseid,
        'itemtype' => 'course'
    );

    $gradeid = $DB->get_record ( 'grade_items', $params )->id;

    $params = array(
        'itemid' => $gradeid,
        'userid' => $userid
    );
    if ($DB->record_exists ( 'grade_grades', $params )) {

        $finalgrade = $DB->get_record ( 'grade_grades', $params )->finalgrade;
    } else {
        $finalgrade = null;
    }
    return $finalgrade;
}


/**
 * get assign grade
 * @param string $userid
 * @param string $assignid
 * @return string
 */
function get_assign_grade($userid, $assignid) {
    global $DB;
    $grade = null;

    $params = array(
        'userid' => $userid,
        'assignment' => $assignid
    );
    if ($DB->record_exists ( 'assign_grades', $params )) {
        $grade = $DB->get_record ( 'assign_grades', $params)->grade;
    } else {
        $grade = null;
    }
    return $grade;
}

/**
 * get quiz grade
 * @param string $userid
 * @param string $quizid
 * @return string
 */
function get_quiz_grade($userid, $quizid) {
    global $DB;
    $grade = null;

    $params = array(
        'userid' => $userid,
        'quiz' => $quizid
    );
    if ($DB->record_exists ( 'quiz_grades', $params)) {

        $grade = $DB->get_record ( 'quiz_grades', $params )->grade;
    } else {
        $grade = null;
    }
    return $grade;
}