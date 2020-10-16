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
 * This generates the grade awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate assignment awards
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_assignment($paramcourseid) {
    mtrace(get_string('mt:cron_awards_assign', 'block_mt'));

    $assignmentlist = get_assignment_list($paramcourseid);
    foreach ($assignmentlist as $assignment) {
        awards_process_course($paramcourseid, $assignment->id, get_grade_award_name($assignment->itemname),
            get_current_period(), RANK_PERIOD_INDIVIDUAL);
    }
}

/**
 * generate quiz awards
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_quiz($paramcourseid) {
    mtrace(get_string('mt:cron_awards_quiz', 'block_mt'));

    $quizlist = block_mt_get_quiz_list($paramcourseid);
    foreach ($quizlist as $quiz) {
        awards_process_course($paramcourseid, $quiz->id, get_grade_award_name($quiz->itemname),
            get_current_period(), RANK_PERIOD_INDIVIDUAL);
    }
}

/**
 * generate overall grade awards
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_overall_grade($paramcourseid) {
    global $DB;

    mtrace(get_string('mt:cron_awards_overall', 'block_mt'));

    $finalgradedataid = $DB->get_field('grade_items', 'id', array(
        'courseid' => $paramcourseid,
        'itemtype' => 'course'
    ));
    awards_process_course($paramcourseid, $finalgradedataid, 'Overall Grade', get_current_period(), RANK_PERIOD_OVERALL);
}