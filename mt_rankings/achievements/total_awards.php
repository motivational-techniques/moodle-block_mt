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
 *  This displays a list of all the awards rankings.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$active = optional_param ( 'active', 'true', PARAM_STRINGID );

global $DB, $CFG;

$pagename = get_string ( 'mt_rankings:total_awards_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_rankings/achievements/total_awards.php';
$pageurlparams = array(
    'active' => $active
);
$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');
require_once($CFG->dirroot . '/blocks/mt/includes/configuration_settings.php');

$weights = new stdClass ();
$weights->gold = get_awards_settings ( 'mt_awards:achievements_gold_weight_value', $courseid );
$weights->silver = get_awards_settings ( 'mt_awards:achievements_silver_weight_value', $courseid );
$weights->bronze = get_awards_settings ( 'mt_awards:achievements_bronze_weight_value', $courseid );

echo html_writer::tag ( 'h2', get_string ( 'mt_rankings:total_awards_desc', 'block_mt' ) );
echo html_writer::tag ( 'h4', get_string ( 'mt_rankings:total_awards_calc', 'block_mt', $weights ) );

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_active.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
    get_string ( 'mt_rankings:total_awards_rank', 'block_mt' ),
    get_string ( 'mt_rankings:total_awards_name', 'block_mt' ),
    get_string ( 'mt_rankings:total_awards_active', 'block_mt' ),
    get_string ( 'mt_rankings:total_awards_gold', 'block_mt' ),
    get_string ( 'mt_rankings:total_awards_silver', 'block_mt' ),
    get_string ( 'mt_rankings:total_awards_bronze', 'block_mt' ),
    get_string ( 'mt_rankings:total_awards_total', 'block_mt' )
);
$table->size = array (
    '10px',
    '200px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px'
);
$table->id = "myTable";
$table->attributes ['class'] = 'tablesorter-blue';

$params = array (
    'courseid' => $courseid
);
if ($DB->record_exists('block_mt_ranks_achiev', $params)) {
    $studentdata = $DB->get_records('block_mt_ranks_achiev', $params, 'total desc, gold desc, silver desc, bronze desc');
        $i = 1;
    foreach ($studentdata as $student) {
        $student->active = block_mt_is_active($student->userid, $courseid);
        if (display_anonymous ( $student->userid, $courseid )) {
            $studentname = get_string ( 'mt_rankings:total_awards_anonymous', 'block_mt' );
        } else {
            $studentname = get_string ( 'mt_rankings:total_awards_student_name', 'block_mt', block_mt_get_user_name($student->userid) );
        }

        $tablerow = new html_table_row ( array (
            $i,
            $studentname,
            display_active_flag ( $student->active ),
            $student->gold,
            $student->silver,
            $student->bronze,
            $student->total
        ) );
        $tablerow->cells [2]->attributes ['class'] = 'activeColumn';
        $tablerow->cells [3]->attributes ['class'] = 'gradeColumn';
        $tablerow->cells [4]->attributes ['class'] = 'gradeColumn';
        $tablerow->cells [5]->attributes ['class'] = 'gradeColumn';
        $tablerow->cells [6]->attributes ['class'] = 'gradeColumn';
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