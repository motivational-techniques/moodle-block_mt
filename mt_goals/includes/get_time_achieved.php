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
 * These are functions to get the time achieved
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * get quiz time achieved
 * @param string $userid
 * @param string $quizid
 * @return string
 */
function get_quiz_time_achieved($userid, $quizid) {
    global $DB;
    $timeachieved = "";

    $parameters = array (
            'userid' => $userid,
            'itemid' => $quizid
    );
    if ($DB->record_exists ( 'grade_grades', $parameters )) {
        $timeachieved = $DB->get_record ( 'grade_grades', $parameters )->timemodified;
    } else {
        $timeachieved = "";
    }

    return $timeachieved;
}

/**
 * get assign time achieved
 * @param string $userid
 * @param string $assignid
 * @return string
 */
function get_assign_time_achieved($userid, $assignid) {
    global $DB;
    $timeachieved = "";

    $parameters = array (
            'userid' => $userid,
            'itemid' => $assignid
    );
    if ($DB->record_exists ( 'grade_grades', $parameters )) {
        $timeachieved = $DB->get_record ( 'grade_grades', $parameters )->timemodified;
    } else {
        $timeachieved = "";
    }

    return $timeachieved;
}