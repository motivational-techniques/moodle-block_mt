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
 * This displays the goals for grade for Assignments
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$pagename = get_string ( 'mt_goals:assign_grade_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_goals/assign/assign_grade.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/includes/display_no_records.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/determine_status.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/display_button.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/display_goal_grades.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_assign_final_grade.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/get_assign_goal_grade.php');

global $DB, $OUTPUT;

echo $OUTPUT->heading ( get_string ( 'mt_goals:assign_grade_description', 'block_mt' ), 2, 'description', 'uniqueid' );
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/buttons_mainmenu.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
        get_string ( 'mt_goals:assign_grade_assign', 'block_mt' ),
        get_string ( 'mt_goals:assign_grade_grade_label', 'block_mt' ),
        get_string ( 'mt_goals:assign_grade_goal', 'block_mt' ),
        get_string ( 'mt_goals:assign_grade_status', 'block_mt' ),
        ""
);
$table->size = array (
        '100',
        '100',
        '100',
        '100',
        '50'
);
$table->id = "myTable";
$assignparams = array (
    'courseid' => $courseid,
    'itemmodule' => 'assign'
);
$assignlist = $DB->get_records ( 'grade_items', $assignparams );
if (count((array) $assignlist ) > 0) {
    foreach ($assignlist as $assign) {
        $assignfinalgrade = get_assign_final_grade($assign->id, $userid);
        $goalgrade = get_assign_goal_grade($assign->id, $userid, $courseid);
        $status = determine_grade_status ( $goalgrade, $assignfinalgrade );

        $linkparams = new stdClass ();
        $linkparams->url = 'assign_grade_settings.php';
        if ($goalgrade == null) {
            $linkparams->text = get_string ( 'mt_goals:add_button', 'block_mt' );
        } else {
            $linkparams->text = get_string ( 'mt_goals:update_button', 'block_mt' );
        }
        if ($assignfinalgrade == null) {
            $urlparams = array (
                    'courseid' => $courseid,
                    'assignid' => $assign->id
            );
            $button = display_button ( $urlparams, $linkparams );
        } else {
            $button = "";
        }
        $tablerow = new html_table_row ( array (
                $assign->itemname,
                display_goal_grade($assignfinalgrade),
                display_goal_grade($goalgrade),
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