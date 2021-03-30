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
 * This displays the goals for overall grades
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$pagename = get_string ( 'mt_goals:overall_grade_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_goals/grade/overall_grade.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_goals/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/determine_status.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/display_goal_grades.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/display_button.php');

global $DB, $OUTPUT, $PAGE;

echo $OUTPUT->heading ( get_string ( 'mt_goals:overall_grade_description', 'block_mt' ), 2, 'description', 'uniqueid' );
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/buttons_mainmenu.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
        get_string ( 'mt_goals:overall_grade_grade_label', 'block_mt' ),
        get_string ( 'mt_goals:overall_grade_goal', 'block_mt' ),
        get_string ( 'mt_goals:overall_grade_status', 'block_mt' ),
        ""
);
$table->size = array (
        '100',
        '100',
        '100',
        '50'
);
$table->id = "myTable";

$gradeid = $DB->get_records ( 'grade_items', array (
        'courseid' => $courseid,
        'itemtype' => 'course'
) );

if (count ( ( array ) $gradeid ) > 0) {
    foreach ($gradeid as $id => $gradeid) {
        $parameters = array (
            'itemid' => $gradeid->id,
            'userid' => $userid
        );
        if ($DB->record_exists ( 'grade_grades', $parameters )) {
            $grades = $DB->get_record ( 'grade_grades', $parameters);
            $finalgrade = ($grades->finalgrade / $grades->rawgrademax ) * 100;
        } else {
            $finalgrade = '';
        }

        $parameters = array (
            'userid' => $userid,
            'courseid' => $courseid
        );

        if ($DB->record_exists ( 'block_mt_goals_overall', $parameters)) {
            $goalgrade = $DB->get_record ( 'block_mt_goals_overall', $parameters)->goal;
        } else {
            $goalgrade = '';
        }

        $status = block_mt_goals_determine_grade_status ( $goalgrade, $finalgrade );

        $linkparams = new stdClass ();
        $linkparams->url = 'overall_grade_settings.php';
        if ($goalgrade == '') {
            $linkparams->text = get_string ( 'mt_goals:add_button', 'block_mt' );
        } else {
            $linkparams->text = get_string ( 'mt_goals:update_button', 'block_mt' );
        }

        $urlparams = array (
                'courseid' => $courseid,
                'userid' => $userid
        );

        $button = display_button ( $urlparams, $linkparams );

        $tablerow = new html_table_row ( array (
                block_mt_goals_display_goal_grade ( $finalgrade ),
                block_mt_goals_display_goal_grade ( $goalgrade ),
                $status,
                $button
        ) );

        $table->data [] = $tablerow;
    }
} else {
    $table->data [] = new html_table_row ( array (
            get_string ( 'mt_goals:overall_nograde', 'block_mt' ),
            "",
            "",
            "",
            ""
    ) );
}
echo html_writer::table ( $table );
echo $OUTPUT->footer();