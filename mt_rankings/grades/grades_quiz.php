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
 * This displays the rankings for grades for a give quiz
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

defined('MOODLE_INTERNAL') || die();

global $DB;

$quizid = required_param ( 'quizid', PARAM_INT );
$active = optional_param ( 'active', 'true', PARAM_STRINGID );

$quizname = $DB->get_record ( 'grade_items', array (
    'id' => $quizid
) )->itemname;

$pagename = get_string ( 'mt_rankings:rank_name_grades_quiz', 'block_mt', $quizname );

$pageurl = '/blocks/mt/mt_rankings/grades/grades_quiz.php';
$pageurlparams = array(
    'quizid' => $quizid,
    'active' => $active
);

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');

echo html_writer::tag ( 'h2', get_string ( 'mt_rankings:grade_quiz_desc', 'block_mt' ) );
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_quiz.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_active.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
    get_string ( 'mt_rankings:grade_quiz_rank', 'block_mt' ),
    get_string ( 'mt_rankings:grade_quiz_name', 'block_mt' ),
    get_string ( 'mt_rankings:grade_quiz_active', 'block_mt' ),
    get_string ( 'mt_rankings:grade_quiz_grade', 'block_mt' )
);

$table->size = array (
    '10px',
    '200px',
    '50px',
    '50px'
);
$table->id = "myTable";
$table->attributes ['class'] = 'tablesorter-blue';

$sql = "SELECT {grade_grades}.userid, finalgrade
        FROM {grade_grades}
        JOIN {grade_items}
        ON {grade_items}.id={grade_grades}.itemid
        WHERE itemid=:quizid
        AND {grade_grades}.finalgrade is not null
        ORDER BY finalgrade DESC";
$params = array (
        'quizid' => $quizid
);
if ($DB->record_exists_sql ( $sql, $params )) {
    $studentlist = $DB->get_records_sql ( $sql, $params );
    $i = 1;
    foreach ($studentlist as $student) {
        $student->active = block_mt_is_active($student->userid, $courseid);
        if (display_anonymous ( $student->userid, $courseid)) {
            $studentname = get_string ( 'mt_rankings:grade_quiz_anonymous', 'block_mt');
        } else {
            $studentname = get_string ( 'mt_rankings:grade_quiz_student_name', 'block_mt',
                block_mt_get_user_name($student->userid));
        }

        $tablerow = new html_table_row ( array (
            $i,
            $studentname,
            block_mt_display_active_flag ($student->active),
            number_format ($student->finalgrade, 2)
        ) );
        $tablerow->cells [2]->attributes ['class'] = 'activeColumn';
        $tablerow->cells [3]->attributes ['class'] = 'gradeColumn';
        if ($student->userid == $userid) {
            $tablerow->attributes ['class'] = 'highlight';
        }
        if ($active == 'true') {
            if ($student->active) {
                // If active flag only display if student is active.
                $table->data[] = $tablerow;
                $i ++;
            }
        } else {
            // Display all students if no active flag.
            $table->data[] = $tablerow;
            $i ++;
        }
    }
} else {
    $table->data[] = block_mt_get_no_records_row('mt_rankings:no_records');
}

echo html_writer::table ( $table );
echo $OUTPUT->footer();