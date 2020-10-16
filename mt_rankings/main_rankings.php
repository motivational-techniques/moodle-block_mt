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
 * This displays a list of all the rankings.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../config.php');

defined('MOODLE_INTERNAL') || die();

$pagename = get_string ( 'mt_rankings:main_rankings', 'block_mt' );
$pageurl = '/blocks/mt/mt_rankings/main_rankings.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/display_links.php');

global $DB;

$urlparams = array(
    'courseid' => $courseid,
);

$parameters = array(
    'courseid' => $courseid,
    'itemtype' => 'course'
);

$coursefinalgradeid = $DB->get_field('grade_items', 'id',
    $parameters);

$linkparams = new stdClass();
$linkparams->url = '';
$linkparams->text = '';

$rankingsparams = new stdClass();
$rankingsparams->userid = $userid;
$rankingsparams->courseid = $courseid;
$rankingsparams->period = block_mt_get_current_period();
$rankingsparams->gradeid = '';
$rankingsparams->ranktype = '';

$menuheader = array (
    'class' => 'menu-header'
);
echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_rankings:main_rankings_all', 'block_mt' )
    , $menuheader );
$linkparams->url = 'all_ranks.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_show_all', 'block_mt' );
display_link_without_ranking($linkparams, $rankingsparams);
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_rankings:main_rankings_grades', 'block_mt' ), $menuheader );
$linkparams->url = 'grades/grades_assignment_main.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_grades_assign', 'block_mt' );
display_link_without_ranking($linkparams, $rankingsparams);

$linkparams->url = 'grades/grades_quiz_main.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_grades_quiz', 'block_mt' );
display_link_without_ranking($linkparams, $rankingsparams);

$rankingsparams->ranktype = RANK_TYPE_GRADES;
$rankingsparams->gradeid = $coursefinalgradeid;
$rankingsparams->period_type = RANK_PERIOD_OVERALL;

$linkparams->url = 'grades/grades_overall.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_grades_overall', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_rankings:main_rankings_achievements', 'block_mt' ), $menuheader );
$rankingsparams->gradeid = null;
$rankingsparams->ranktype = RANK_TYPE_ACHIEVEMENT;
$rankingsparams->period_type = RANK_PERIOD_OVERALL;

$linkparams->url = 'achievements/total_awards.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_num_awards_achieved', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_rankings:main_rankings_time_online', 'block_mt' ), $menuheader );
$rankingsparams->ranktype = RANK_TYPE_ONLINE_TIME;
$rankingsparams->period_type = RANK_PERIOD_OVERALL;
$rankingsparams->gradeid = null;
$linkparams->url = 'time_online/time_online_overall_average.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_time_online_average', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);

$rankingsparams->gradeid = null;
$rankingsparams->ranktype = RANK_TYPE_ONLINE_TIME;
$rankingsparams->period_type = RANK_PERIOD_MONTHLY;
$linkparams->url = 'time_online/time_online_month.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_time_online_month', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_rankings:main_rankings_participation', 'block_mt' ), $menuheader );

$rankingsparams->ranktype = RANK_TYPE_NUMBER_POSTS;
$rankingsparams->period_type = RANK_PERIOD_OVERALL;
$rankingsparams->gradeid = null;

$linkparams->url = 'participation/total_posts_overall_average.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_num_posts_average', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);

$rankingsparams->ranktype = RANK_TYPE_NUMBER_POSTS;
$rankingsparams->period_type = RANK_PERIOD_MONTHLY;

$linkparams->url = 'participation/total_posts_month.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_num_posts_month', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);

$rankingsparams->ranktype = RANK_TYPE_POST_RATING;
$rankingsparams->period_type = RANK_PERIOD_OVERALL;

$linkparams->url = 'participation/rating_posts_overall_average.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_average_posts_rating', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);

$rankingsparams->ranktype = RANK_TYPE_POST_RATING;
$rankingsparams->period_type = RANK_PERIOD_MONTHLY;

$linkparams->url = 'participation/rating_posts_month.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_average_posts_rating_month', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);

$rankingsparams->ranktype = RANK_TYPE_WEEKLY_POSTS;
$rankingsparams->period_type = RANK_PERIOD_OVERALL;

$linkparams->url = 'participation/read_posts_overall_average.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_read_all_posts', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);

$rankingsparams->ranktype = RANK_TYPE_WEEKLY_POSTS;
$rankingsparams->period_type = RANK_PERIOD_MONTHLY;

$linkparams->url = 'participation/read_posts_month.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_read_all_posts_month', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);
echo html_writer::end_tag ( 'div' );

echo html_writer::start_tag ( 'div', $menuheader );
echo html_writer::tag ( 'p', get_string ( 'mt_rankings:main_rankings_milestones', 'block_mt' ), $menuheader );
$linkparams->url = 'milestones/time_main.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_milestone_time', 'block_mt' );
display_link_without_ranking($linkparams, $rankingsparams);

$rankingsparams->ranktype = RANK_TYPE_MILESTONE;
$rankingsparams->period_type = RANK_PERIOD_OVERALL;

$linkparams->url = 'milestones/milestone_pace.php';
$linkparams->text = get_string ( 'mt_rankings:main_rankings_milestone_pace', 'block_mt' );
display_link_with_ranking($linkparams, $rankingsparams);
echo html_writer::end_tag ( 'div' );
echo $OUTPUT->footer();