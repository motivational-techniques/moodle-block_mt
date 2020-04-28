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
 * This displays a list of all the goals.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../config.php');

$pagename = get_string ( 'mt_goals:main_goals', 'block_mt' );
$pageurl = '/blocks/mt/mt_goals/all_goals.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/includes/display_no_records.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/determine_status.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/display_button.php');

global $DB, $OUTPUT, $PAGE;

echo $OUTPUT->heading ( get_string ( 'mt_goals:all_description', 'block_mt' ), 2, 'description', 'uniqueid' );
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/buttons_mainmenu.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
        get_string ( 'mt_goals:all_goal', 'block_mt' ),
        get_string ( 'mt_goals:all_achieved', 'block_mt' )
);
$table->size = array (
        '100',
        '100'
);
$table->id = "myTable";

$params = array(
    'courseid' => $courseid,
    'userid' => $userid
);
$goaldata = $DB->get_records('block_mt_goals_user', $params, 'achieved desc');

if (count ( ( array ) $goaldata ) > 0) {
    foreach ($goaldata as $id => $goaldata) {

        $tablerow = new html_table_row ( array (
                $goaldata->goalname,
                display_status ( $goaldata->achieved )
        ) );
        $table->data [] = $tablerow;
    }
} else {
    $table->data[] = get_no_records_row('mt_goals:no_student_records');
}
echo html_writer::table ( $table );
echo $OUTPUT->footer();