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
 * This displays the award list for a given milestones
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

defined('MOODLE_INTERNAL') || die();

require($CFG->dirroot . '/blocks/mt/mt_awards/../includes/get_milestones.php');

$instanceid = required_param('instanceid', PARAM_INT);
$active = optional_param('active', 'true', PARAM_STRINGID);

global $DB;

$milestonename = get_milestone_name_by_id($instanceid);

$milestone = new stdClass();
$milestone->id = $instanceid;
$milestone->name = $milestonename;

$pagename = get_string('mt_awards:milestones_page_name', 'block_mt', $milestone);
$pageurl = '/blocks/mt/mt_awards/milestones/milestones.php';
$pageurlparams = array(
    'active' => $active,
    'instanceid' => $instanceid
);
$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/includes.php');
require_once($CFG->dirroot . '/blocks/mt/includes/configuration_settings.php');

$goldaward = get_awards_settings('mt_awards:milestones_gold_days_value', $courseid);
$silveraward = get_awards_settings('mt_awards:milestones_silver_days_value', $courseid);
$bronzeaward = get_awards_settings('mt_awards:milestones_bronze_days_value', $courseid);

$weights = new stdClass();
$weights->gold = $goldaward;
$weights->silver = $silveraward;
$weights->bronze = $bronzeaward;

echo html_writer::tag('h2', get_string('mt_awards:milestones_desc', 'block_mt'));
echo html_writer::tag('h4', get_string('mt_awards:milestones_calc', 'block_mt', $weights));

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_milestones.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_active.php');

$table = new html_table();
$table->width = '70%';

$table->head = array(
    get_string('mt_awards:time_online_month_name', 'block_mt'),
    get_string('mt_awards:time_online_overall_active', 'block_mt'),
    get_string('mt_awards:time_online_month_award', 'block_mt')
);

$table->size = array(
    '200px',
    '50px',
    '50px'
);
$table->id = "myTable";
$table->attributes['class'] = 'tablesorter-blue';

$params = array (
    'milestone' => $instanceid,
    'courseid' => $courseid
);
if ($DB->record_exists('block_mt_ranks_milestones', $params)) {
    $studentlist = $DB->get_records('block_mt_ranks_milestones', $params, 'milestone_time asc');
    foreach ($studentlist as $student) {
        $student->active = is_active($student->userid, $courseid);
        if (display_anonymous ( $student->userid, $courseid )) {
            $studentname = get_string ('mt_rankings:milestone_time_anonymous', 'block_mt');
        } else {
            $studentname = get_string ('mt_rankings:milestone_time_student_name', 'block_mt',
                get_user_name($student->userid));
        }
        // Determine award based on time taken to complete the milestone.
        $timetaken = $student->milestone_time / (60 * 60 * 24);
        $award = '';
        if ($timetaken <= $goldaward) {
            $award = get_string('mt_awards:gold', 'block_mt');
        } else if ($timetaken <= $silveraward) {
            $award = get_string('mt_awards:silver', 'block_mt');
        } else if ($timetaken <= $bronzeaward) {
            $award = get_string('mt_awards:bronze', 'block_mt');
        }
        // If no award, no row in table.
        if (strlen($award) == 0) {
            continue;
        }

        $tablerow = new html_table_row (array (
            $studentname,
            display_active_flag ( $student->active ),
            $award
        ));
        $tablerow->cells [1]->attributes ['class'] = 'activeColumn';
        $tablerow->cells [2]->attributes ['class'] = 'gradeColumn';
        if ($student->userid == $userid) {
            $tablerow->attributes ['class'] = 'highlight';
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