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
 * This generates the grade rankings
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate ranks for assignments
 * @param integer $courseid
 * @param array $userranking
 */
function generate_ranks_assignments($courseid, $userranking) {
    global $DB;
    $assignmentlist = get_assignment_list($courseid);
    foreach ($assignmentlist as $assignment) {
        $userranking->rankname = $DB->get_record('grade_items',
            array(
                'id' => $assignment->id))->itemname;
            ranks_process_course($userranking, $assignment->id, RANK_TYPE_GRADES,
                RANK_PERIOD_INDIVIDUAL);
    }
}

/**
 * generate ranks for quizzes
 * @param integer $courseid
 * @param array $userranking
 */
function generate_ranks_quizzes($courseid, $userranking) {
    global $DB;
    $quizlist = block_mt_get_quiz_list($courseid);
    foreach ($quizlist as $quiz) {
        $userranking->rankname = $DB->get_record('grade_items',
            array(
                'id' => $quiz->id))->itemname;
            ranks_process_course($userranking, $quiz->id, RANK_TYPE_GRADES, RANK_PERIOD_INDIVIDUAL);
    }
}

/**
 * generate ranks overall grade
 * @param integer $courseid
 * @param array $userranking
 */
function generate_ranks_overall_grade($courseid, $userranking) {
    global $DB;
    $userranking->rankname = get_string('mt_rankings:generate_rank_grade_rank_name', 'block_mt');
    $finalgradeidparams = array(
                'courseid' => $courseid,
                'itemtype' => 'course');
    $finalgradeid = $DB->get_field('grade_items', 'id',
        $finalgradeidparams);
    ranks_process_course($userranking, $finalgradeid, RANK_TYPE_GRADES,
            RANK_PERIOD_OVERALL);
}