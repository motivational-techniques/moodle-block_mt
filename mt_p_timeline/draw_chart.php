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
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

defined('MOODLE_INTERNAL') || die();

$courseid = required_param('courseid', PARAM_INT);
$userid = $USER->id;

$course = $DB->get_record('course', array('id' => $courseid), "*", MUST_EXIST);
require_login($course);

// Page name and url.
$pagename = get_string('mt_ptimeline:chart_page_name', 'block_mt');
$pageurl = '/blocks/mt/mt_p_timeline/draw_chart.php';

block_mt_send_to_dashboard_if_no_block_installed($courseid);

// Log a timeline viewed event.
$context = context_course::instance($courseid);
$event = \block_mt\event\mt_progress_timeline_viewed::create(array('context' => $context));
$event->set_url($pageurl);
$event->trigger();

// Outgoing data.
$out = array(
    'title' => get_string('mt_ptimeline:chart_title', 'block_mt'),
    'vaxis' => get_string('mt_ptimeline:chart_vaxis', 'block_mt'),
    'haxis' => get_string('mt_ptimeline:chart_haxis', 'block_mt'),
    'chartdata' => get_chart_data($courseid, $userid)
);

// Get the page title.
$userinfo = array(
    'firstname'  => $USER->firstname,
    'lastname'   => $USER->lastname,
    'coursename' => $course->fullname,
    'pagename'   => $pagename
);
$title = get_string('mt_ptimeline:page_title', 'block_mt', $userinfo);

// Set up the page.
$PAGE->set_url($pageurl, array('courseid' => $courseid));
$PAGE->set_title($title);

$PAGE->requires->js_init_call('init', array($out), true);
$PAGE->requires->js("/blocks/mt/includes/js/draw_chart.js");

$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);

// Output the page.
echo $OUTPUT->header();

echo html_writer::tag('script', '', array('src' => "http://www.google.com/jsapi"));
echo html_writer::div(get_string('mt_ptimeline:chart_description', 'block_mt'));
echo html_writer::tag('div', '', array(
    'id'    => "visualization",
    'style' => "width: 900px; height: 400px;"
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
function get_chart_data(&$courseid, &$userid) {
    global $DB;

    // Get quiz module id.
    $quizid = $DB->get_record('modules', array('name' => 'quiz'), 'id')->id;

    // Set up the table.
    $table = array(
        'cols' => array(
            array(
                'label' => get_string('mt_ptimeline:chart_milestone', 'block_mt'),
                'type'  => 'string'),
            array(
                'label' => get_string('mt_ptimeline:chart_ideal', 'block_mt'),
                'type'  => 'number'),
            array(
                'label' => get_string('mt_ptimeline:chart_average', 'block_mt'),
                'type'  => 'number'),
            array(
                'label' => get_string('mt_ptimeline:chart_progress', 'block_mt'),
                'type'  => 'number')
        ),
        'rows' => array()
    );

    // Get the milestone records.
    $milestones = $DB->get_records('block_mt_p_timeline', array('course' => $courseid), "week");

    foreach ($milestones as $milestone) {

        // Do not show milestones with 0 week value, as per settings.
        if ($milestone->week == 0) {
            continue;
        }
        // Get the students enrolled in the course.
        $sql = "SELECT A.userid, A.timestart
                  FROM {user_enrolments} A, {enrol} B
                 WHERE A.enrolid = B.id
                   AND B.courseid = :courseid";

        $students = $DB->get_records_sql($sql, array('courseid' => $courseid));

        // Get the table name and sql for the next queries based on module type.
        $tablename = 'assign';

        $sql = "SELECT timecreated
                  FROM {assign_grades}
                 WHERE assignment = :instance
                   AND userid = :userid
                   AND grade > 0";

        if ($milestone->module == $quizid) {
            $tablename = 'quiz';

            $sql = "SELECT timefinish
                      FROM {quiz_attempts}
                     WHERE quiz = :instance
                       AND userid = :userid
                       AND sumgrades > 0
                  ORDER BY attempt";
        }

        // Reset variables for next iteration through students.
        $weekuser = null;
        $averageweeks = null;
        $temp = array();
        $sumweeks = 0;
        $count = 0;

        // Add module name and ideal week row data.
        $result = $DB->get_record($tablename, array('id' => $milestone->instance), 'name');

        // If no DB result, module has been removed from course page.
        // Remove from timeline table as well and do not add to chart.
        if (! $result) {
            $DB->delete_records('block_mt_p_timeline', array(
                'course'   => $courseid,
                'module'   => $milestone->module,
                'instance' => $milestone->instance
            ));
            continue;
        }

        $temp[] = array('v' => $result->name);
        $temp[] = array('v' => $milestone->week);

        // Determine if student has completed the module.
        foreach ($students as $student) {

            // Get the module completion result.
            $success = $DB->get_records_sql($sql, array(
                'instance' => $milestone->instance,
                'userid'   => $student->userid
            ));

            // If module attempted successfully.
            if ($success) {

                // ... calculate the time taken for completion.
                if ($milestone->module == $quizid) {
                    $timefinish = $success[key($success)]->timefinish;
                } else {
                    $timefinish = $success[key($success)]->timecreated;
                }

                $time = abs($timefinish - $student->timestart);
                $weeks = ceil($time / 60 / 60 / 24 / 7);

                $sumweeks = $sumweeks + $weeks;
                $count = $count + 1;

                // If the user viewing the page has data, add it.
                if ($student->userid == $userid) {
                    $weekuser = $weeks;
                }
            }
        }
        // Get the average number of weeks taken for this module.
        if ($sumweeks > 0) {
            $averageweeks = ceil($sumweeks / $count);
        }

        // Add average weeks and user data to row.
        $temp[] = array('v' => $averageweeks);
        $temp[] = array('v' => $weekuser);

        $table['rows'][] = array ('c' => $temp);
    }

    return $table;
}