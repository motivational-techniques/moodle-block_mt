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
 * This displays the awards list for the read posts overall
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$active = optional_param('active', 'true', PARAM_STRINGID);

global $DB, $CFG;

$pagename = get_string('mt_awards:read_posts_overall_page_name', 'block_mt');
$pageurl = '/blocks/mt/mt_awards/participation/read_posts_overall.php';
$pageurlparams = array(
    'active' => $active
);
$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/includes.php');
require_once($CFG->dirroot . '/blocks/mt/includes/configuration_settings.php');

$counts = new stdClass();
$counts->gold = get_awards_settings('mt_awards:read_posts_gold_weight_value', $courseid);
$counts->silver = get_awards_settings('mt_awards:read_posts_silver_weight_value', $courseid);
$counts->bronze = get_awards_settings('mt_awards:read_posts_bronze_weight_value', $courseid);

echo html_writer::tag('h2', get_string('mt_awards:read_posts_overall_desc', 'block_mt'));
echo html_writer::tag('h4', get_string('mt_awards:read_posts_overall_calc', 'block_mt', $counts));

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_active.php');

$table = new html_table();
$table->width = '70%';

$tableheader = array(
    get_string('mt_awards:read_posts_overall_name', 'block_mt'),
    get_string('mt_awards:read_posts_overall_active', 'block_mt'),
    get_string('mt_awards:read_posts_overall_total', 'block_mt')
);

$table->head = generate_table_header_months($tableheader);

$table->size = array(
    '100',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px'
);
$table->id = "myTable";
$table->attributes['class'] = 'tablesorter-blue';

$params = array(
    'courseid' => $courseid,
    'awardtype' => '5'
);
if ($DB->record_exists('block_mt_awards_count_all', $params)) {
    $studentlist = $DB->get_records('block_mt_awards_count_all', $params, 'awardtotal desc');
    foreach ($studentlist as $id => $studentlist) {
        if ($studentlist->awardtotal == 0) {
            continue;
        }
        $studentisactive = is_active($studentlist->userid, $courseid);
        if (display_anonymous($studentlist->userid, $courseid)) {
            $studentname = get_string('mt_awards:read_posts_overall_anonymous', 'block_mt');
        } else {
            $studentname = get_string('mt_awards:read_posts_overall_student_name', 'block_mt', block_mt_get_user_name($studentlist->userid));
        }
        $row = array(
            $studentname,
            display_active_flag($studentisactive),
            $studentlist->awardtotal
        );
        $previousmonths = block_mt_get_current_date();

        $cell = new html_table_cell();
        $cell->attributes['class'] = 'gradeColumn';

        for ($monthcounter = 12; $monthcounter > 0; $monthcounter --) {
            $cell = new html_table_cell();
            $cell->attributes['class'] = 'gradeColumn';

            $period = $previousmonths->format('Y-n-d');

            $awardname = get_string('mt_awards:read_posts_overall_award_name', 'block_mt', $previousmonths->format('Y-n'));
            $titletext = get_string('mt_awards:read_posts_overall_title', 'block_mt', $previousmonths->format('F Y'));

            $parameters = array(
                'courseid' => $courseid,
                'userid' => $studentlist->userid,
                'award_name' => $awardname
            );
            $currentmonthresult = $DB->get_record('block_mt_awards_user', $parameters);

            $cell->attributes['title'] = $titletext;
            if ($currentmonthresult && $currentmonthresult->awardid) {
                $cell->text = get_award_name($currentmonthresult->awardid);
            } else {
                $cell->text = null;
            }
            $previousmonths->modify('previous month');
            $row[] = $cell;
        }
        $tablerow = new html_table_row($row);

        $tablerow->cells[1]->attributes['class'] = 'activeColumn';
        $tablerow->cells[2]->attributes['class'] = 'gradeColumn';
        $tablerow->cells[2]->attributes['title'] = $titletext;
        if ($studentlist->userid == $userid) {
            $tablerow->attributes['class'] = 'highlight';
        }
        if ($active == 'true') {
            if ($studentisactive) {
                // If active flag only display if student is active.
                $table->data[] = $tablerow;
            }
        } else {
            // Display all students if no active flag.
            $table->data[] = $tablerow;
        }
    }
} else {
    $tablerow = new html_table_row(array(
        get_string('mt_awards:no_records', 'block_mt')
    ));
    $tablerow->attributes['class'] = 'highlight';
    $tablerow->cells[0]->colspan = '100%';
    $table->data[] = $tablerow;
}

echo html_writer::table($table);
echo $OUTPUT->footer();