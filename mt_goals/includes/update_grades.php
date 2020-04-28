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
 * This updates the grades for a user
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * udpate final grade status
 * @param string $courseid
 * @param string $userid
 * @param string $finalgrade
 * @param boolean $achieved
 * @return null
 */
function update_final_grade_status($courseid, $userid, $finalgrade, $achieved) {
    global $DB;

    $parameters = array (
            'userid' => $userid,
            'courseid' => $courseid
    );

    if ($finalgrade == '') {
        $finalgrade = null;
    }
    if ($DB->record_exists ( 'block_mt_goals_overall', $parameters )) {
        $parameters ['id'] = $DB->get_field ( 'block_mt_goals_overall', 'id', $parameters );
        $parameters ['grade'] = $finalgrade;
        $parameters ['achieved'] = $achieved;
        $DB->update_record ( 'block_mt_goals_overall', $parameters );
    }
}

/**
 * udpate assign grade status
 * @param string $assignid
 * @param string $userid
 * @param string $grade
 * @param boolean $achieved
 * @return null
 */
function update_assign_grade_status($assignid, $userid, $grade, $achieved) {
    global $DB;

    $parameters = array (
            'userid' => $userid,
            'assignid' => $assignid
    );

    if ($grade == '') {
        $grade = null;
    }
    if ($DB->record_exists ( 'block_mt_goals_assign', $parameters )) {
        $parameters ['id'] = $DB->get_field ( 'block_mt_goals_assign', 'id', $parameters );
        $parameters ['grade'] = $grade;
        $parameters ['achieved'] = $achieved;
        $DB->update_record ( 'block_mt_goals_assign', $parameters );
    }
}

/**
 * udpate quiz grade status
 * @param string $quizid
 * @param string $userid
 * @param string $grade
 * @param boolean $achieved
 * @return null
 */
function update_quiz_grade_status($quizid, $userid, $grade, $achieved) {
    global $DB;

    $parameters = array (
            'userid' => $userid,
            'quizid' => $quizid
    );

    if ($grade == '') {
        $grade = null;
    }
    if ($DB->record_exists ( 'block_mt_goals_quiz', $parameters )) {
        $parameters ['id'] = $DB->get_field ( 'block_mt_goals_quiz', 'id', $parameters );
        $parameters ['grade'] = $grade;
        $parameters ['achieved'] = $achieved;
        $DB->update_record ( 'block_mt_goals_quiz', $parameters );
    }
}