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
 * This is the main includes for all pages
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined ( 'MOODLE_INTERNAL' ) || die ();

global $DB, $PAGE, $OUTPUT, $CFG, $USER;
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

$currentpath = $CFG->wwwroot;

$courseid = required_param ( 'courseid', PARAM_INT );
$userid = $USER->id;

$course = get_course($courseid);
require_login ( $course );

send_to_dashboard_if_no_block_installed($courseid);

$userinformation = $DB->get_record ( 'user', array (
    'id' => $userid
) );
$coursename = $course->fullname;

$userinformation->coursename = $coursename;
$userinformation->pagename = $pagename;

$title = get_string ( 'mt_rankings:page_title', 'block_mt', $userinformation );

if (!isset($pageurlparams)) {
    $pageurlparams = array(
        'courseid' => $courseid
    );
} else {
    $pageurlparams['courseid'] = $courseid;
}
$PAGE->set_title($title );
$PAGE->set_heading($title );
$PAGE->set_url($pageurl, $pageurlparams);
$PAGE->set_pagelayout('standard');

if (isset ( $logmtpage )) {
    if ($logmtpage) {
        $context = context_course::instance($courseid);
        $event = \block_mt\event\mt_rankings_viewed::create
        (array('context' => $context));
        $event->set_url($pageurl);
        $event->trigger();

        $logmtpage = false;
    }
}

require_once($CFG->dirroot . '/blocks/mt/includes/page_requires.php');

echo $OUTPUT->header ();

require_once($CFG->dirroot . '/blocks/mt/includes/includes.php');
require_once($CFG->dirroot . '/blocks/mt/includes/get_rank_name.php');
require_once($CFG->dirroot . '/blocks/mt/mt_rankings/includes/display_anonymous.php');
require_once($CFG->dirroot . '/blocks/mt/mt_rankings/includes/get_current_ranking.php');