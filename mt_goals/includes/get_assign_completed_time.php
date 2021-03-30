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
 * This gets the assignment completed time
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * get assign completed time
 * @param string $userid
 * @param string $assignid
 * @return string
 */
function block_mt_goals_get_assign_completed_time($userid, $assignid) {
    global $DB;
    $completed = '';
    $sql = "SELECT {assign_submission}.*
        FROM {assign_submission}
        join {assign_grades}
        on ({assign_submission}.userid={assign_grades}.userid)
        and ({assign_submission}.assignment = {assign_grades}.assignment)
        and ({assign_grades}.grade > 0)
        WHERE {assign_submission}.assignment = :assignid
        AND {assign_submission}.userid = :userid";
    $params = array(
            'userid' => $userid,
            'assignid' => $assignid
    );
    if ($DB->record_exists_sql ( $sql, $params)) {
            $completed = $DB->get_record_sql ( $sql, $params, IGNORE_MULTIPLE )->timemodified;
    } else {
        $completed = '';
    }
    return $completed;
}

/**
 * get assign completed time
 * @param string $userid
 * @param string $assignid
 * @return string
 */
function block_mt_goals_get_assign_completed_time_week($userid, $assignid) {
    return block_mt_goals_get_course_week(block_mt_goals_get_assign_completed_time($userid, $assignid));
}

/**
 * get assign average completed time
 * @param string $assignid
 * @return string
 */
function block_mt_goals_get_assign_average_completed_time($assignid) {
    global $DB;

    $averagetime = null;
    $params = array(
        'assignment' => $assignid
    );
    if ($DB->record_exists ( 'assign_grades', $params )) {
        $sql = "select avg(timemodified) as averagetime
                from {assign_grades}
                where assignment=:assignment
                and grade > 0";
        $averagetime = $DB->get_record_sql ( $sql, $params, IGNORE_MULTIPLE )->averagetime;
    } else {
        $averagetime = null;
    }
    return block_mt_goals_get_course_week($averagetime);
}