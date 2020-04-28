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
 * This gets the id for quizzes or assignments
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * get the quiz id
 *
 * @return integer
 */
function get_quiz_id() {
    global $DB;
    $parameters = array (
            'name' => 'quiz'
    );
    return $DB->get_record('modules', $parameters)->id;
}

/**
 * get the assign id
 *
 * @return integer
 */
function get_assign_id() {
    global $DB;
    $parameters = array (
            'name' => 'assign'
    );
    return $DB->get_record('modules', $parameters)->id;
}

/**
 * get quiz id
 * @param string $iteminstance
 * @param string $courseid
 * @return string
 */
function get_quiz_id_for_grade($iteminstance, $courseid) {
    global $DB;

    $parameters = array (
            'iteminstance' => $iteminstance,
            'courseid' => $courseid,
            'itemmodule' => 'quiz'
    );
    return $DB->get_record( 'grade_items', $parameters)->id;
}