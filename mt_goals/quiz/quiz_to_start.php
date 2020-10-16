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
 * This displays the goals for start date for quizes
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$pagename = get_string ( 'mt_goals:quiz_start_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_goals/quiz/quiz_to_start.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/includes/display_no_records.php');
require($CFG->dirroot . '/blocks/mt/includes/get_quiz_list.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/determine_status.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/display_button.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/display_goal_dates.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_quiz_start_time.php');

global $DB, $OUTPUT;

echo $OUTPUT->heading ( get_string ( 'mt_goals:quiz_start_description', 'block_mt' ), 2, 'description', 'uniqueid' );
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/buttons_mainmenu.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
        get_string ( 'mt_goals:quiz_start_quiz', 'block_mt' ),
        get_string ( 'mt_goals:quiz_start_current', 'block_mt' ),
        get_string ( 'mt_goals:quiz_start_start_label', 'block_mt' ),
        get_string ( 'mt_goals:quiz_start_started', 'block_mt' ),
        get_string ( 'mt_goals:quiz_start_status', 'block_mt' ),
        ""
);
$table->size = array (
        '100',
        '100',
        '100',
        '100',
        '100',
        '50'
);
$table->id = "myTable";

$quizlist = block_mt_get_quiz_list($courseid);
if (count ( ( array ) $quizlist ) > 0) {
    $currentday = date_timestamp_get(date_create () );
    foreach ($quizlist as $quiz) {
        $quizid = $quiz->iteminstance;
        $goaltostartdate = get_quiz_goal_start_time($userid, $quizid, $courseid);
        $starteddate = get_quiz_start_time($userid, $quizid);
        $status = days_remaining ( $goaltostartdate, $currentday, $starteddate );

        $linkparams = new stdClass ();
        $linkparams->url = 'quiz_to_start_settings.php';
        if ($goaltostartdate) {
            $linkparams->text = get_string ( 'mt_goals:update_button', 'block_mt' );
        } else {
            $linkparams->text = get_string ( 'mt_goals:add_button', 'block_mt' );
        }

        if ($starteddate) {
            $button = '';
        } else {
            $urlparams = array (
                    'courseid' => $courseid,
                    'userid' => $userid,
                    'quizid' => $quizid
            );
            $button = display_button ( $urlparams, $linkparams );
        }

        $tablerow = new html_table_row ( array (
                $quiz->itemname,
                display_goal_date($currentday),
                display_goal_date($goaltostartdate),
                display_goal_date($starteddate),
                $status,
                $button
        ) );
        $table->data [] = $tablerow;
    }
} else {
    $table->data[] = block_mt_get_no_records_row('mt_goals:quiz_noquizzes');
}

echo html_writer::table ( $table );
echo $OUTPUT->footer();