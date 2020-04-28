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
 * This generates the goals for assign complete
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Assign complete process entry
 * @param array $param
 * @return null
 */
function assign_complete_process_entry($param) {
    global $DB;

    $parameters = array (
        'courseid' => $param["courseid"],
        'userid' => $param["userid"],
        'assignid' => $param["assignid"]
    );
    $recordcount = $DB->count_records ( 'block_mt_goals_assign_comp', $parameters );
    if ($recordcount > 0) {
        $parameters ['id'] = $DB->get_field ( 'block_mt_goals_assign_comp', 'id', $parameters );
        $parameters ['goal'] = $DB->get_field ( 'block_mt_goals_assign_comp', 'goal', $parameters );
        if (assignment_submitted($param["userid"], $param["assignid"])) {
            $parameters ['achieved'] = has_achieved($parameters ['goal'], $param["timeachieved"]);
        } else {
            $parameters ['achieved'] = false;
        }
        $parameters ['timeachieved'] = $param["timeachieved"];
        $DB->update_record ( 'block_mt_goals_assign_comp', $parameters, false );
    }
}

/**
 * generate goals Assign complete
 * @param string $courseid
 * @return null
 */
function generate_goal_assign_complete($courseid) {
    global $DB;
    // Get the grade id for the assign grade.
    $parameters = array (
        'courseid' => $courseid,
        'itemmodule' => 'assign'
    );
    $assignlist = $DB->get_records ( 'grade_items', $parameters);
    foreach ($assignlist as $assign) {
        $sql = "SELECT {assign_submission}.*
            FROM {assign_submission}
            join {assign_grades}
            on ({assign_submission}.userid={assign_grades}.userid)
            and ({assign_submission}.assignment = {assign_grades}.assignment)
            WHERE {assign_submission}.assignment = :assignment";
        $parameters = array (
            'assignment' => $assign->iteminstance
        );
        if ($DB->record_exists_sql($sql, $parameters)) {
            $assignments = $DB->get_records_sql($sql, $parameters);
            foreach ($assignments as $assignment) {
                $params = array (
                    'courseid' => $courseid,
                    'assignid' => $assign->id,
                    'userid' => $assignment->userid,
                    'timeachieved' => $assignment->timemodified
                );
                assign_complete_process_entry( $params );
            }
        }
    }
}