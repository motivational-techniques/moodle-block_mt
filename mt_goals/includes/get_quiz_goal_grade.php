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
 * This gets the quiz goal grade
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * get quiz goal grade
 * @param string $quizid
 * @param string $userid
 * @param string $courseid
 * @return string
 */
function block_mt_goals_get_quiz_goal_grade($quizid, $userid, $courseid) {
    global $DB;
    $params = array (
            'quizid' => $quizid,
            'userid' => $userid,
            'courseid' => $courseid
    );
    if ($DB->record_exists ( 'block_mt_goals_quiz', $params )) {
        $goalgrade = $DB->get_record ( 'block_mt_goals_quiz', $params )->goal;
    } else {
        $goalgrade = null;
    }
    return $goalgrade;
}