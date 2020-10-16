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
 * This displays the list of awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../config.php');

defined('MOODLE_INTERNAL') || die();

$pagename = get_string ( 'mt_awards:main_awards', 'block_mt' );
$pageurl = '/blocks/mt/mt_awards/main_awards.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/display_award.php');

global $DB;

$urlparameters = array (
        'courseid' => $courseid
);

$coursefinalgradeidentifier = $DB->get_field ( 'grade_items', 'id', array (
        'courseid' => $courseid,
        'itemtype' => 'course'
) );

$linkparameters = new stdClass ();
$linkparameters->url = '';
$linkparameters->text = '';

$awardsparameters = new stdClass ();
$awardsparameters->userid = $userid;
$awardsparameters->courseid = $courseid;

$menuheader = array (
        'class' => 'menu-header'
);
echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_awards:main_awards_personal_achievements', 'block_mt' ), $menuheader );
$linkparameters->url = 'personal_achievements.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_personal_achievements', 'block_mt' );
display_without_award ( $linkparameters, $awardsparameters );
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_awards:main_awards_grades', 'block_mt' ), $menuheader );
$linkparameters->url = 'grades/grades_assignment_main.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_grades_by_assignment', 'block_mt' );
display_without_award ( $linkparameters, $awardsparameters );

$linkparameters->url = 'grades/grades_quiz_main.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_grades_by_quiz', 'block_mt' );
display_without_award ( $linkparameters, $awardsparameters );

$awardsparameters->awardname = get_string ( 'mt_awards:main_awards_overall_grade', 'block_mt' );
$linkparameters->url = 'grades/grades_overall.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_grades_overall', 'block_mt' );
display_with_award ( $linkparameters, $awardsparameters );
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_awards:main_awards_milestones', 'block_mt' ), $menuheader );
$linkparameters->url = 'milestones/milestones_main.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_milestone_time_period', 'block_mt' );
display_without_award ( $linkparameters, $awardsparameters );
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_awards:main_awards_time_online', 'block_mt' ), $menuheader );
$awardsparameters->awardname = get_string ( 'mt_awards:main_awards_time_online_overall', 'block_mt' );
$linkparameters->url = 'time_online/time_online_overall.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_time_online_month', 'block_mt' );
display_without_award ( $linkparameters, $awardsparameters );


$awardsparameters->awardname = get_string ( 'mt_awards:main_awards_time_online_current_period', 'block_mt',
        block_mt_get_current_period_year_month() );
$linkparameters->url = 'time_online/time_online_month.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_time_online_current_month', 'block_mt' );
display_with_award ( $linkparameters, $awardsparameters );
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_awards:main_awards_participation', 'block_mt' ), $menuheader );

$awardsparameters->awardname = get_string ( 'mt_awards:main_awards_overall_number_posts', 'block_mt' );
$linkparameters->url = 'participation/total_posts_overall.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_posts_submitted_month', 'block_mt' );
display_without_award ( $linkparameters, $awardsparameters );

$awardsparameters->awardname = get_string ( 'mt_awards:main_awards_number_posts', 'block_mt',
        block_mt_get_current_period_year_month() );
$linkparameters->url = 'participation/total_posts_month.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_posts_submitted_this_month', 'block_mt' );
display_with_award ( $linkparameters, $awardsparameters );

$awardsparameters->awardname = get_string ( 'mt_awards:main_awards_overall_rating_posts', 'block_mt' );
$linkparameters->url = 'participation/rating_posts_overall.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_post_rating_month', 'block_mt' );
display_without_award ( $linkparameters, $awardsparameters );

$awardsparameters->awardname = get_string ( 'mt_awards:main_awards_rating_posts', 'block_mt',
        block_mt_get_current_period_year_month() );
$linkparameters->url = 'participation/rating_posts_month.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_average_post_rating_month', 'block_mt' );
display_with_award ( $linkparameters, $awardsparameters );

$awardsparameters->awardname = get_string ( 'mt_awards:main_awards_overall_read_posts', 'block_mt' );

$linkparameters->url = 'participation/read_posts_overall.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_read_all_posts_month', 'block_mt' );
display_without_award ( $linkparameters, $awardsparameters );

$awardsparameters->awardname = get_string ( 'mt_awards:main_awards_read_posts', 'block_mt',
        block_mt_get_current_period_year_month() );
$linkparameters->url = 'participation/read_posts_month.php';
$linkparameters->text = get_string ( 'mt_awards:main_awards_read_all_posts_this_month', 'block_mt' );
display_with_award ( $linkparameters, $awardsparameters );
echo html_writer::end_tag ( 'div' );
echo $OUTPUT->footer();