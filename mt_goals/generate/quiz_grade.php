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
 * This generates the goals for quiz grades
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz grade process entry
 * @param array $param
 * @return null
 */
function quiz_grades_process_entry($param) {
    global $DB;

    $parameters = array (
        'courseid' => $param["courseid"],
        'userid' => $param["userid"],
        'quizid' => $param["quizid"]
    );

    $recordcount = $DB->count_records('block_mt_goals_quiz', $parameters );
    if ($recordcount > 0) {
        $parameters ['id'] = $DB->get_field ( 'block_mt_goals_quiz', 'id', $parameters );
        $parameters ['goal'] = $DB->get_field ( 'block_mt_goals_quiz', 'goal', $parameters );
        if ($parameters ['goal'] <= $param["grade"]) {
            $parameters ['achieved'] = true;
        } else {
            $parameters ['achieved'] = false;
        }
        $parameters ['grade'] = $param["grade"];
        $parameters ['timeachieved'] = $param["timeachieved"];
        $DB->update_record ( 'block_mt_goals_quiz', $parameters, false );
    }
}

/**
 * Quiz grade generate goals
 * @param string $courseid
 * @return null
 */
function generate_goal_quiz_grade($courseid) {
    global $DB;
    // Get the grade id for the quiz grade.
    $parameters = array (
        'courseid' => $courseid,
        'itemmodule' => 'quiz'
    );
    $quizzes = $DB->get_records ( 'grade_items', $parameters);
    foreach ($quizzes as $quiz) {
        $parameters = array (
            'itemid' => $quiz->id,
        );
        if ($DB->record_exists('grade_grades', $parameters)) {
            $grades = $DB->get_records ('grade_grades', $parameters);
            foreach ($grades as $grade) {
                $finalgrade = ($grade->finalgrade / $grade->rawgrademax) * 100;
                $params = array (
                    'courseid' => $courseid,
                    'quizid' => $quiz->iteminstance,
                    'grade' => $finalgrade,
                        'userid' => $grade->userid,
                    'timeachieved' => $grade->timemodified
                );
                quiz_grades_process_entry($params);
            }
        }
    }
}