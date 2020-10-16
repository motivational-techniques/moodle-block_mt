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
 * This displays the awards list for the ratings posts for the current month
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$active = optional_param('active', 'true', PARAM_STRINGID);

global $DB;

$pagename = get_string('mt_awards:rating_posts_month_page_name', 'block_mt');
$pageurl = '/blocks/mt/mt_awards/participation/rating_posts_month.php';
$pageurlparams = array(
    'active' => $active
);
$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/includes.php');
require_once($CFG->dirroot . '/blocks/mt/includes/configuration_settings.php');

$counts = new stdClass();
$counts->gold = get_awards_settings('mt_awards:rating_posts_gold_count_value', $courseid);
$counts->silver = get_awards_settings('mt_awards:rating_posts_silver_count_value', $courseid);
$counts->bronze = get_awards_settings('mt_awards:rating_posts_bronze_count_value', $courseid);

echo html_writer::tag('h2', get_string('mt_awards:rating_posts_month_desc', 'block_mt'));
echo html_writer::tag('h4', get_string('mt_awards:rating_posts_month_calc', 'block_mt', $counts));

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_mainmenu.php');

$table = new html_table();
$table->width = '70%';

$table->head = array(
    get_string('mt_awards:rating_posts_month_name', 'block_mt'),
    get_string('mt_awards:rating_posts_month_award', 'block_mt')
);

$table->size = array(
    '200px',
    '50px'
);
$table->id = "myTable";
$table->attributes['class'] = 'tablesorter-blue';

$awardname = get_string('mt_awards:rating_posts_month_award_name', 'block_mt', get_current_period_year_month());

$params = array(
    'award_name' => $awardname,
    'courseid' => $courseid
);
if ($DB->record_exists('block_mt_awards_user', $params)) {
    $studentdata = $DB->get_records('block_mt_awards_user', $params, 'awardid');
    foreach ($studentdata as $student) {
        $student->active = is_active($student->userid, $courseid);
        if (display_anonymous($student->userid, $courseid)) {
            $studentname = get_string('mt_awards:rating_posts_month_anonymous', 'block_mt');
        } else {
            $studentname = get_string('mt_awards:rating_posts_month_student_name', 'block_mt', block_mt_get_user_name($student->userid));
        }
        $tablerow = new html_table_row(array(
            $studentname,
            get_award_name($student->awardid)
        ));
        $tablerow->cells[1]->attributes['class'] = 'gradeColumn';
        if ($student->userid == $userid) {
            $tablerow->attributes['class'] = 'highlight';
        }
        if ($active == 'true') {
            if ($student->active) {
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