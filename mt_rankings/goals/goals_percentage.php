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
 * This displays the percentage of goals achieved.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

defined('MOODLE_INTERNAL') || die();

$logmtpage = true;

$active = optional_param('active', 'true', PARAM_STRINGID);

global $DB;

$pagename = get_string ( 'mt_rankings:goals_percentage_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_rankings/participation/goals_percentage.php';
$pageurlparams = array(
    'active' => $active
);
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');
require_once($CFG->dirroot . '/blocks/mt/mt_rankings/includes/display_anonymous.php');

echo html_writer::tag ( 'h2', get_string ( 'mt_rankings:goals_percentage_desc', 'block_mt' ) );
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_active.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
    get_string ( 'mt_rankings:goals_percentage_rank', 'block_mt' ),
    get_string ( 'mt_rankings:goals_percentage_name', 'block_mt' ),
    get_string ( 'mt_rankings:goals_percentage_active', 'block_mt' ),
    get_string ( 'mt_rankings:goals_percentage_number_times', 'block_mt' )
);

$table->size = array (
    '10px',
    '200px',
    '50px',
    '50px'
);
$table->id = "myTable";
$table->attributes ['class'] = 'tablesorter-blue';

$tablerow = new html_table_row ( array (
    get_string ( 'mt_rankings:goals_percentage_not_implemented', 'block_mt' )
) );

$tablerow->attributes ['class'] = 'highlight';
$tablerow->cells [0]->colspan = '100%';

$table->data [] = $tablerow;

echo html_writer::table ( $table );
echo $OUTPUT->footer();