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
 * This displays the goals for completion date for assignments
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$pagename = get_string ( 'mt_goals:assign_complete_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_goals/assign/assign_to_complete.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/includes/display_no_records.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/determine_status.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/display_button.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/display_goal_dates.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_assign_completed_time.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_assign_goal_time.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_assign_final_grade.php');
require($CFG->dirroot . "/blocks/mt/includes/get_assignment_list.php");
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_course_week.php');

global $DB, $OUTPUT;

echo $OUTPUT->heading ( get_string ( 'mt_goals:assign_complete_description', 'block_mt' ), 2, 'description', 'uniqueid' );
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/buttons_mainmenu.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
        get_string ( 'mt_goals:assign_complete_assign', 'block_mt' ),
        get_string ( 'mt_goals:assign_complete_current', 'block_mt' ),
        get_string ( 'mt_goals:assign_complete_complete', 'block_mt' ),
        get_string ( 'mt_goals:assign_complete_completed', 'block_mt' ),
        get_string ( 'mt_goals:assign_complete_remain', 'block_mt' ),
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
$assignlist = block_mt_get_assignment_list($courseid);
if (count ( ( array ) $assignlist ) > 0) {
    $currentday = date_timestamp_get ( date_create () );
    foreach ($assignlist as $assign) {
        $assignfinalgrade = block_mt_goals_get_assign_final_grade($assign->id, $userid);
        $goaltocomplete = block_mt_goals_get_assign_goal_time($userid, $assign->iteminstance, $courseid);

        if ($assignfinalgrade != null) {
            $parameters = array (
                    'userid' => $userid,
                    'assignment' => $assign->iteminstance
            );
            $completed = $DB->get_record ( 'assign_submission', $parameters,
                    $fields = 'timemodified')->timemodified;
        } else {
            $completed = '';
        }

        $linkparams = new stdClass ();
        $linkparams->url = 'assign_to_complete_settings.php';
        if ($goaltocomplete == '') {
            $linkparams->text = get_string ( 'mt_goals:add_button', 'block_mt' );
        } else {
            $linkparams->text = get_string ( 'mt_goals:update_button', 'block_mt' );
        }

        if ($completed) {
            $button = '';
        } else {
            $urlparams = array (
                    'courseid' => $courseid,
                    'assignid' => $assign->iteminstance
            );
            $button = display_button ( $urlparams, $linkparams );
        }
        $status = block_mt_goals_days_remaining ( $goaltocomplete, $currentday, $completed );
        $tablerow = new html_table_row ( array (
                $assign->itemname,
                display_goal_date ( $currentday ),
                display_goal_date ( $goaltocomplete ),
                display_goal_date ( $completed ),
                $status,
                $button
        ) );
        $table->data [] = $tablerow;
    }
} else {
    $table->data[] = block_mt_get_no_records_row('mt_goals:assign_noassignments');
}
echo html_writer::table ( $table );
echo $OUTPUT->footer();