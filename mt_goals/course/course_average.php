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
 * This displays the goals for the course average
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$pagename = get_string ( 'mt_goals:course_average_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_goals/course/course_average.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_goals/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/determine_status.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/display_button.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/course/get_course_average_data.php');

global $DB, $OUTPUT, $PAGE;

echo $OUTPUT->heading ( get_string ( 'mt_goals:course_average_page_name', 'block_mt' ), 2, 'description', 'uniqueid' );
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/buttons/buttons_mainmenu.php');

// Outgoing data.
$out = array(
        'title' => get_string('mt_goals:course_average_page_name', 'block_mt'),
        'vaxis' => get_string('mt_goals:course_average_week', 'block_mt'),
        'haxis' => get_string('mt_goals:course_average_goal', 'block_mt'),
        'chartdata' => get_chart_data($courseid, $userid)
);

$PAGE->requires->js_init_call('init', array($out), true);
$PAGE->requires->js("/blocks/mt/includes/js/draw_chart.js");

echo html_writer::tag('script', '', array('src' => "http://www.google.com/jsapi"));
echo html_writer::div(get_string('mt_ptimeline:chart_description', 'block_mt'));
echo html_writer::tag('div', '', array(
        'id'    => "visualization",
        'style' => "width: 100%; height: 400px;"
));
echo $OUTPUT->footer();