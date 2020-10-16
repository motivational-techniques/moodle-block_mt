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
 * This generates the goals for quiz complete
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz complete process entry
 * @param array $param
 * @return null
 */
function quiz_complete_process_entry($param) {
    global $DB;

    $parameters = array (
        'courseid' => $param["courseid"],
        'userid' => $param["userid"],
        'quizid' => $param["quizid"]
    );
    $recordcount = $DB->count_records ( 'block_mt_goals_quiz_comp', $parameters );
    if ($recordcount > 0) {
        $parameters ['id'] = $DB->get_field ( 'block_mt_goals_quiz_comp', 'id', $parameters );
        $parameters ['goal'] = $DB->get_field ( 'block_mt_goals_quiz_comp', 'goal', $parameters );
        $parameters ['achieved'] = has_achieved($parameters ['goal'], $param["timeachieved"]);
        $parameters ['timeachieved'] = $param["timeachieved"];
        $DB->update_record ( 'block_mt_goals_quiz_comp', $parameters, false );
    }
}

/**
 * Quiz complete generate goals
 * @param string $courseid
 * @return null
 */
function generate_goal_quiz_complete($courseid) {
    global $DB;

    $quizlist = block_mt_get_quiz_list($courseid);
    foreach ($quizlist as $quizitem) {
        $parameters = array (
                'itemid' => $quizitem->iteminstance,
        );
        if ($DB->record_exists ( 'grade_grades', $parameters )) {
            $quizzes = $DB->get_records ( 'grade_grades', $parameters);
            foreach ($quizzes as $quiz) {
                $params = array (
                        'courseid' => $courseid,
                        'quizid' => $quizitem->iteminstance,
                        'userid' => $quiz->userid,
                        'timeachieved' => $quiz->timemodified
                );
                quiz_complete_process_entry( $params );
            }
        }
    }
}