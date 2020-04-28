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
 * This displays a list of all the goals.
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../config.php');

$pagename = get_string ( 'mt_goals:main_goals', 'block_mt' );
$pageurl = '/blocks/mt/mt_goals/main_goals.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_goals/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_goals/includes/display_goal.php');
require_once($CFG->dirroot . "/blocks/mt/includes/get_period.php");

global $DB;

$urlparams = array (
        'courseid' => $courseid,
        'userid' => $userid
);

$linkparams = new stdClass ();
$linkparams->url = '';
$linkparams->text = '';

$rankingsparams = new stdClass ();
$rankingsparams->userid = $userid;
$rankingsparams->courseid = $courseid;
$rankingsparams->period = get_current_period();
$rankingsparams->gradeid = '';
$rankingsparams->ranktype = '';

$menuheader = array (
        'class' => 'menu-header'
);
echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_goals:main_goals_header', 'block_mt' ), $menuheader );
$linkparams->url = 'all_goals.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_all_goals', 'block_mt' );
display_link ( $linkparams, $rankingsparams );
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_goals:main_goals_header', 'block_mt' ), $menuheader );
$linkparams->url = 'course/course_progress.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_course_progress', 'block_mt' );
display_link ( $linkparams, $rankingsparams );

$linkparams->url = 'course/course_average.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_course_average', 'block_mt' );
display_link ( $linkparams, $rankingsparams );
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_goals:main_goals_header_overall_grade', 'block_mt' ), $menuheader );
$linkparams->url = 'grade/overall_grade.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_overall_grade', 'block_mt' );
display_link ( $linkparams, $rankingsparams );
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_goals:main_goals_header_assign', 'block_mt' ), $menuheader );
$linkparams->url = 'assign/assign_to_complete.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_assign_tocomplete', 'block_mt' );
display_link ( $linkparams, $rankingsparams );

$linkparams->url = 'assign/assign_grade.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_assign_grade', 'block_mt' );
display_link ( $linkparams, $rankingsparams );
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_goals:main_goals_header_quiz', 'block_mt' ), $menuheader );
$linkparams->url = 'quiz/quiz_to_start.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_quiz_tostart', 'block_mt' );
display_link ( $linkparams, $rankingsparams );

$linkparams->url = 'quiz/quiz_to_complete.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_quiz_tocomplete', 'block_mt' );
display_link ( $linkparams, $rankingsparams );

$linkparams->url = 'quiz/quiz_grade.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_quiz_grade', 'block_mt' );
display_link ( $linkparams, $rankingsparams );
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_goals:main_goals_header_rank_award', 'block_mt' ), $menuheader );
$linkparams->url = 'ranking_award/ranking.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_rank', 'block_mt' );
display_link ( $linkparams, $rankingsparams );

$linkparams->url = 'ranking_award/award.php';
$linkparams->text = get_string ( 'mt_goals:main_goals_award', 'block_mt' );
display_link ( $linkparams, $rankingsparams );
echo html_writer::end_tag ( 'div' );
echo $OUTPUT->footer();