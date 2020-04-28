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
 *  This displays a list of all the rankings.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../config.php');

defined('MOODLE_INTERNAL') || die();

global $DB;

$pagename = get_string ( 'mt_rankings:all_rankings', 'block_mt' );

$pageurl = '/blocks/mt/mt_rankings/all_ranks.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/includes/get_milestones.php');

echo html_writer::tag ( 'h2', get_string ( 'mt_rankings:all_rankings_description', 'block_mt' ) );

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
        get_string ( 'mt_rankings:all_rankings_sarea', 'block_mt' ),
        get_string ( 'mt_rankings:all_rankings_rank', 'block_mt' )
);

$table->size = array (
        '200px',
        '10px'
);
$table->id = "myTable";
$table->attributes ['class'] = 'tablesorter-blue';

$params = array (
    'userid' => $userid,
    'courseid' => $courseid
);
if ($DB->record_exists ( 'block_mt_ranks_user', $params )) {
    $rankings = $DB->get_records ( 'block_mt_ranks_user', $params, 'rank asc' );
    foreach ($rankings as $id => $rankings) {
        $tablerow = new html_table_row ( array (
                get_rank_name ( $rankings ),
                $rankings->rank
        ) );
        $tablerow->cells [1]->attributes ['class'] = 'gradeColumn';
        $table->data [] = $tablerow;
    }
} else {
    $table->data[] = get_no_records_row('mt_rankings:no_student_records');
}

echo html_writer::table($table);
echo $OUTPUT->footer();