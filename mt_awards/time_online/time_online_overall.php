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
 * This displays the awards list for the overall time oneline
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

defined('MOODLE_INTERNAL') || die();

$active = optional_param('active', 'true', PARAM_STRINGID);

global $DB, $CFG;

$pagename = get_string('mt_awards:time_online_overall_page_name', 'block_mt');
$pageurl = '/blocks/mt/mt_awards/time_online/time_online_overall.php';
$pageurlparams = array(
    'active' => $active
);
$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/includes.php');
require_once($CFG->dirroot . '/blocks/mt/includes/configuration_settings.php');

$goldawardweight = block_mt_get_awards_settings('mt_awards:time_online_gold_weight_value', $courseid);
$silverawardweight = block_mt_get_awards_settings('mt_awards:time_online_silver_weight_value', $courseid);
$bronzeawardweight = block_mt_get_awards_settings('mt_awards:time_online_bronze_weight_value', $courseid);

$weights = new stdClass();
$weights->gold = block_mt_get_awards_settings('mt_awards:time_online_gold_weight_value', $courseid);
$weights->silver = block_mt_get_awards_settings('mt_awards:time_online_silver_weight_value', $courseid);
$weights->bronze = block_mt_get_awards_settings('mt_awards:time_online_bronze_weight_value', $courseid);

echo html_writer::tag('h2', get_string('mt_awards:time_online_overall_desc', 'block_mt'));
echo html_writer::tag('h4', get_string('mt_awards:time_online_overall_calc', 'block_mt', $weights));
echo html_writer::tag('h4', get_string('mt_awards:time_online_month_calc', 'block_mt'));

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_active.php');

$table = new html_table();
$table->width = '70%';

$tableheader = array(
    get_string('mt_awards:time_online_overall_name', 'block_mt'),
    get_string('mt_awards:time_online_overall_active', 'block_mt'),
    get_string('mt_awards:time_online_overall_total', 'block_mt')
);

$table->head = block_mt_generate_table_header_months($tableheader);

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
    'awardtype' => '3',
);
if ($DB->record_exists('block_mt_awards_count_all', $params)) {
    $studentlist = $DB->get_records('block_mt_awards_count_all', $params, 'awardtotal desc');
    foreach ($studentlist as $id => $studentlist) {
        $studentisactive = block_mt_is_active($studentlist->userid, $courseid);

        if (display_anonymous($studentlist->userid, $courseid)) {
            $studentname = get_string('mt_awards:time_online_overall_anonymous', 'block_mt');
        } else {
            $studentname = get_string('mt_awards:time_online_overall_student_name', 'block_mt',
                block_mt_get_user_name($studentlist->userid));
        }
        $row = array(
            $studentname,
            block_mt_display_active_flag($studentisactive),
            $studentlist->awardtotal
        );
        $previousmonths = block_mt_get_current_date();

        $cell = new html_table_cell();
        $cell->attributes['class'] = 'gradeColumn';

        for ($monthcounter = 12; $monthcounter > 0; $monthcounter --) {
            $cell = new html_table_cell();
            $cell->attributes['class'] = 'gradeColumn';

            $awardname = get_string('mt_awards:time_online_month_award_name', 'block_mt', $previousmonths->format('Y-n'));
            $titletext = get_string('mt_awards:time_online_month_award_for', 'block_mt', $previousmonths->format('F Y'));

            $parameters = array(
                'courseid' => $courseid,
                'userid' => $studentlist->userid,
                'award_name' => $awardname
            );
            $currentmonthresult = $DB->get_record('block_mt_awards_user', $parameters);

            $cell->attributes['title'] = $titletext;
            if ($currentmonthresult != null) {
                $cell->text = block_mt_get_award_name($currentmonthresult->awardid);
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