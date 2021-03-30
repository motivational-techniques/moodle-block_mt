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
 * This displays the goals for rankings
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$pagename = get_string('mt_goals:ranking_page_name', 'block_mt');
$pageurl = '/blocks/mt/mt_goals/ranking_award/ranking.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_goals/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/determine_status.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/display_button.php');
require($CFG->dirroot . '/blocks/mt/includes/get_rank_name.php');
require($CFG->dirroot . '/blocks/mt/includes/get_milestones.php');

global $DB, $OUTPUT, $PAGE;

echo $OUTPUT->heading(get_string('mt_goals:ranking_description', 'block_mt'), 2, 'description', 'uniqueid');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/buttons_mainmenu.php');

$table = new html_table();
$table->width = '70%';

$table->head = array(
    get_string('mt_goals:ranking_area', 'block_mt'),
    get_string('mt_goals:ranking_rank', 'block_mt'),
    get_string('mt_goals:ranking_goal', 'block_mt'),
    get_string('mt_goals:ranking_status', 'block_mt'),
    ""
);
$table->align = array(
    'left',
    'center',
    'center',
    'center',
    'center'
);

$table->size = array(
    '100',
    '100',
    '100',
    '100',
    '50'
);
$table->id = "myTable";

$params = array(
    'userid' => $userid,
    'courseid' => $courseid
);
if ($DB->record_exists('block_mt_ranks_user', $params)) {
    $button = "";
    $rankings = $DB->get_records('block_mt_ranks_user', $params, 'rank ASC, period DESC');
    foreach ($rankings as $id => $rankings) {
        $params = array(
            'courseid' => $courseid,
            'ranktypeid' => $rankings->id,
            'userid' => $userid
        );
        if ($DB->record_exists('block_mt_goals_ranks', $params, false)) {
            $goalranking = $DB->get_record('block_mt_goals_ranks', $params)->goal;
            $status = block_mt_goals_determine_ranks_status($rankings->rank, $goalranking);
        } else {
            $goalranking = '';
            $status = '';
        }
        $linkparams = new stdClass();
        $linkparams->url = 'ranking_settings.php';
        if ($goalranking == '') {
            $linkparams->text = get_string('mt_goals:add_button', 'block_mt');
        } else {
            $linkparams->text = get_string('mt_goals:update_button', 'block_mt');
        }
        $urlparams = array(
            'courseid' => $courseid,
            'rankid' => $rankings->id
        );
        $button = display_button($urlparams, $linkparams);
        $tablerow = new html_table_row(array(
            block_mt_get_rank_name($rankings),
            $rankings->rank,
            $goalranking,
            $status,
            $button
        ));
        $table->data[] = $tablerow;
    }
} else {
    $tablerow = new html_table_row ( array (
        get_string ( 'mt_goals:ranking_noranks', 'block_mt' )
    ) );
    $tablerow->attributes ['class'] = 'highlight';
    $tablerow->cells [0]->colspan = '100%';
    $table->data [] = $tablerow;
}
echo html_writer::table($table);
echo $OUTPUT->footer();