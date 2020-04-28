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
 * This generates the goals for overall grades
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Overall grades process entry
 * @param array $param
 * @return null
 */
function overall_grades_process_entry($param) {
    global $DB;

    $parameters = array (
        'courseid' => $param["courseid"],
        'userid' => $param["userid"]
    );

    $recordcount = $DB->count_records ( 'block_mt_goals_overall', $parameters );
    if ($recordcount > 0) {
        $parameters ['id'] = $DB->get_field ( 'block_mt_goals_overall', 'id', $parameters );
        $parameters ['goal'] = $DB->get_field ( 'block_mt_goals_overall', 'goal', $parameters );
        if ($parameters ['goal'] <= $param["grade"]) {
            $parameters ['achieved'] = true;
        } else {
            $parameters ['achieved'] = false;
        }
        $parameters ['grade'] = $param["grade"];
        $parameters ['timeachieved'] = $param["timeachieved"];
        $DB->update_record ( 'block_mt_goals_overall', $parameters, false );
    }
}

/**
 * Overall grades generate goals
 * @param string $courseid
 * @return null
 */
function generate_goal_overall_grade($courseid) {
    global $DB;
    // Get the grade id for the overall grade.
    $parameters = array (
        'courseid' => $courseid,
        'itemtype' => 'course'
    );
    $gradeid = $DB->get_record ( 'grade_items', $parameters);
    $parameters = array (
        'itemid' => $gradeid->id,
    );
    if ($DB->record_exists ( 'grade_grades', $parameters )) {
        $grades = $DB->get_records ( 'grade_grades', $parameters);
        foreach ($grades as $grade) {
            $finalgrade = ($grade->finalgrade / $grade->rawgrademax ) * 100;
            $params = array (
                'courseid' => $courseid,
                'grade' => $finalgrade,
                'userid' => $grade->userid,
                'timeachieved' => $grade->timemodified
            );
            overall_grades_process_entry( $params );
        }
    }
}