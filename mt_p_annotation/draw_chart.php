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
 * This displays the chart.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/mt/mt_p_annotation/locallib.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

defined('MOODLE_INTERNAL') || die();

$courseid = required_param('courseid', PARAM_INT);
$userid = $USER->id;

$course = $DB->get_record('course', array('id' => $courseid), "*", MUST_EXIST);
require_login($course);

$pagename = get_string('mt_p_annotation:chart_title', 'block_mt');
$pageurl = '/blocks/mt/mt_p_annotation/draw_chart.php';

block_mt_send_to_dashboard_if_no_block_installed($courseid);

// Log an annotation viewed event.
$context = context_course::instance($courseid);
$event = \block_mt\event\mt_progress_annotation_viewed::create(array('context' => $context));
$event->set_url($pageurl);
$event->trigger();

// Outgoing data.
$out = array(
    'title'     => get_string('mt_p_annotation:drawchart_title', 'block_mt'),
    'tooltip'   => get_string('mt_p_annotation:drawchart_text', 'block_mt'),
    'wwwroot'   => $CFG->wwwroot,
    'courseid'  => $courseid,
    'userid'    => $userid,
    'chartdata' => block_mt_p_annotation_get_chart_data($courseid, $userid)
);

// Set up the page.
$PAGE->set_url($pageurl, array('courseid' => $courseid));
$PAGE->set_title(get_string('mt_p_annotation:drawchart_title', 'block_mt'));

$PAGE->requires->js_init_call('init', array($out), true);
$PAGE->requires->js("/blocks/mt/mt_p_annotation/draw_chart.js");

$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);

// Output the page.
echo $OUTPUT->header();

echo html_writer::tag('script', '', array('src' => "http://www.google.com/jsapi"));
echo html_writer::div(get_string('mt_p_annotation:drawchart_description', 'block_mt'));
echo html_writer::tag('div', '', array(
    'id'    => "visualization",
    'style' => "width: 800px; height: 400px;"
));

echo $OUTPUT->footer();

/**
 * Function to retrieve the module completion information for the chart. The
 * data is in a table format required by the charting api.
 *
 * @param int $courseid The course id
 * @param int $userid The user id
 * @return array
 */
function block_mt_p_annotation_get_chart_data(&$courseid, &$userid) {
    global $DB;

    // Get the student selections.
    $results = $DB->get_records('block_mt_annotation', array(
        'course' => $courseid,
        'userid' => $userid
    ));

    $countdone  = 0;
    $countdoing = 0;
    $countnot   = 0;

    // Get all the learning object for this course.
    $modnames = populate_instancenames($courseid, true);

    // Count those that are done and in progress.
    foreach ($results as &$result1) {
        switch ($result1->value) {
            case 1 :
                $countdone += 1;
                break;
            case 3 :
                $countdoing += 1;
                break;
        }

        // Remove this module from the complete set.
        if ($result1->value != 2) {
            unset($modnames[$result1->object]);
        }
    }

    // What's left over is not yet done.
    $countnot = count($modnames);

    // Return data table.
    return array(
        'cols' => array(
            array(
                'id'      => '',
                'label'   => 'progress',
                'pattern' => '',
                'type'    => 'string'
            ),
            array(
                'id'      => '',
                'label'   => 'count',
                'pattern' => '',
                'type'    => 'number'
            )
        ),
        'rows' => array(
            array(
                'c' => array(
                    array(
                        'v' => get_string('mt_p_annotation:chart_done', 'block_mt'),
                        'f' => null
                    ),
                    array('v' => $countdone, 'f' => null)
                )
            ),
            array(
                'c' => array(
                    array(
                        'v' => get_string('mt_p_annotation:chart_not_done', 'block_mt'),
                        'f' => null
                    ),
                    array('v' => $countnot, 'f' => null)
                )
            ),
            array(
                'c' => array(
                    array(
                        'v' => get_string('mt_p_annotation:chart_doing', 'block_mt'),
                        'f' => null
                    ),
                    array('v' => $countdoing, 'f' => null)
                )
            )
        )
    );
}

