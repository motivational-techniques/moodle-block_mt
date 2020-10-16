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
 * This displays the rankings for the milestone pace.
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

$active = optional_param ( 'active', 'true', PARAM_STRINGID );

$pagename = get_string ( 'mt_rankings:milestone_pace', 'block_mt' );

$pageurl = '/blocks/mt/mt_rankings/milestones/milestone_pace.php';
$pageurlparams = array(
    'active' => $active
);
$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');
require_once($CFG->dirroot . '/blocks/mt/includes/configuration_settings.php');

echo html_writer::tag ( 'h2', get_string ( 'mt_rankings:milepacedesc', 'block_mt' ) );

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_active.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
    get_string ( 'mt_rankings:milestone_pace_rank', 'block_mt' ),
    get_string ( 'mt_rankings:milestone_pace_name', 'block_mt' ),
    get_string ( 'mt_rankings:milestone_pace_active', 'block_mt' ),
    get_string ( 'mt_rankings:milestone_pace_milestone', 'block_mt' )
);

$table->size = array (
    '10px',
    '200px',
    '50px',
    '50px'
);
$table->id = "myTable";
$table->attributes ['class'] = 'tablesorter-blue';

$params = array (
    'courseid' => $courseid
);
$sql = "SELECT userid, avg(milestone_time) AS milestone_time
            FROM {block_mt_ranks_milestones}
            WHERE courseid = :courseid
            GROUP BY userid
            ORDER BY avg(milestone_time) ASC";
if ($DB->record_exists('block_mt_ranks_milestones', $params)) {
    $studentlist = $DB->get_records_sql($sql, $params);
    $i = 1;
    foreach ($studentlist as $student) {
        $student->active = is_active($student->userid, $courseid);

        if (display_anonymous ( $student->userid, $courseid )) {
            $studentname = get_string ( 'mt_rankings:milestone_pace_anonymous', 'block_mt' );
        } else {
            $studentname = get_string ( 'mt_rankings:milestone_pace_student_name', 'block_mt', block_mt_get_user_name($student->userid));
        }

        $tablerow = new html_table_row ( array (
            $i,
            $studentname,
            display_active_flag($student->active),
            number_format($student->milestone_time / (60 * 60 * 24), 2 )
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
    $tablerow = new html_table_row ( array (
        get_string ( 'mt_rankings:no_records', 'block_mt' )
    ) );

    $tablerow->attributes ['class'] = 'highlight';
    $tablerow->cells [0]->colspan = '100%';

    $table->data [] = $tablerow;
}

echo html_writer::table ( $table );
echo $OUTPUT->footer();