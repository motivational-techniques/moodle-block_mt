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
 * This displays the goals for awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$pagename = get_string('mt_goals:award_page_name', 'block_mt');
$pageurl = '/blocks/mt/mt_goals/ranking_award/award.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_goals/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/determine_status.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/display_button.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/display_award.php');

global $DB, $OUTPUT, $PAGE;

echo $OUTPUT->heading(get_string('mt_goals:award_description', 'block_mt'), 2, 'description', 'uniqueid');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/buttons_mainmenu.php');

$table = new html_table();
$table->width = '70%';

$table->head = array(
    get_string('mt_goals:award_award', 'block_mt'),
    get_string('mt_goals:award_myaward', 'block_mt'),
    get_string('mt_goals:award_goal', 'block_mt'),
    get_string('mt_goals:award_status', 'block_mt'),
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
if ($DB->record_exists ('block_mt_awards_user', $params)) {
    $awards = $DB->get_records ('block_mt_awards_user', $params);
    foreach ($awards as $id => $awards) {
        if (!$awards->awardid) {
            continue;
        }
        $params = array(
            'courseid' => $courseid,
            'awardid' => $awards->id,
            'userid' => $userid
        );
        if ($DB->record_exists('block_mt_goals_awards', $params, false)) {
            $goal = $DB->get_record('block_mt_goals_awards', $params)->goal;
            $status = block_mt_goals_determine_awards_status($awards->awardid, $goal);
        } else {
            $goal = '';
            $status = '';
        }
        $linkparams = new stdClass();
        $linkparams->url = 'award_settings.php';
        if ($goal == '') {
            $linkparams->text = get_string('mt_goals:add_button', 'block_mt');
        } else {
            $linkparams->text = get_string('mt_goals:update_button', 'block_mt');
        }
        $urlparams = array(
            'courseid' => $courseid,
            'awardid' => $awards->id
        );
        $button = display_button($urlparams, $linkparams);
        $tablerow = new html_table_row(array(
            $awards->award_name,
            block_mt_goals_get_award_text($awards->awardid),
            block_mt_goals_get_award_text($goal),
            $status,
            $button
        ));
        $table->data[] = $tablerow;
    }
} else {
    $tablerow = new html_table_row ( array (
        get_string ( 'mt_goals:award_noaward', 'block_mt' )
    ) );
    $tablerow->attributes ['class'] = 'highlight';
    $tablerow->cells [0]->colspan = '100%';
    $table->data [] = $tablerow;
}
echo html_writer::table($table);
echo $OUTPUT->footer();