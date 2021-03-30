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
 * This gets the quiz name
 *
 * @package block_mt
 * @copyright 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * get quiz name
 * @param string $courseid
 * @param string $quizid
 * @return string
 */
function block_mt_goals_get_quiz_name($courseid, $quizid) {
    global $DB;
    $quizname = "";
    $parameters = array (
        'courseid' => $courseid,
        'iteminstance' => $quizid,
        'itemmodule' => 'quiz'
    );

    if ($DB->record_exists ( 'grade_items', $parameters )) {
        $quizname = $DB->get_record ( 'grade_items', $parameters )->itemname;
    } else {
        $quizname = "";
    }
    return $quizname;
}