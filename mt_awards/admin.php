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
 * This is the admin page for teacher options
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');

require_once($CFG->dirroot . '/blocks/mt/mt_awards/mt_awards_admin.php');
require_once($CFG->dirroot . '/blocks/mt/includes/configuration_settings.php');
require_once($CFG->dirroot . '/blocks/mt/includes/block_is_installed.php');

global $OUTPUT, $PAGE;

$courseid = required_param ( 'courseid', PARAM_INT );

$course = get_course($courseid);
require_login ( $course );
require_capability ( 'block/mt_awards:admin', context_course::instance ( $courseid ) );

block_mt_send_to_dashboard_if_no_block_installed($courseid);

$coursename = $course->fullname;

$PAGE->set_url ( '/blocks/mt/mt_awards/admin.php', array (
        'courseid' => $courseid
) );
$PAGE->set_pagelayout ( 'standard' );
$PAGE->set_heading ( get_string ( 'mt_awards:admin_heading', 'block_mt' ) . ' for ' . $coursename );

$mtawards = new mt_awards_form ();

$toform ['courseid'] = $courseid;

$toform ['grades_gold_count'] = block_mt_get_awards_settings ( 'mt_awards:grades_gold_count_value', $courseid );
$toform ['grades_silver_count'] = block_mt_get_awards_settings ( 'mt_awards:grades_silver_count_value', $courseid );
$toform ['grades_bronze_count'] = block_mt_get_awards_settings ( 'mt_awards:grades_bronze_count_value', $courseid );

$toform ['online_time_gold_weight'] = block_mt_get_awards_settings ( 'mt_awards:time_online_gold_weight_value', $courseid );
$toform ['online_time_silver_weight'] = block_mt_get_awards_settings ( 'mt_awards:time_online_silver_weight_value', $courseid );
$toform ['online_time_bronze_weight'] = block_mt_get_awards_settings ( 'mt_awards:time_online_bronze_weight_value', $courseid );

$toform ['num_posts_gold_count'] = block_mt_get_awards_settings ( 'mt_awards:num_posts_gold_count_value', $courseid );
$toform ['num_posts_silver_count'] = block_mt_get_awards_settings ( 'mt_awards:num_posts_silver_count_value', $courseid );
$toform ['num_posts_bronze_count'] = block_mt_get_awards_settings ( 'mt_awards:num_posts_bronze_count_value', $courseid );

$toform ['num_posts_gold_weight'] = block_mt_get_awards_settings ( 'mt_awards:num_posts_gold_weight_value', $courseid );
$toform ['num_posts_silver_weight'] = block_mt_get_awards_settings ( 'mt_awards:num_posts_silver_weight_value', $courseid );
$toform ['num_posts_bronze_weight'] = block_mt_get_awards_settings ( 'mt_awards:num_posts_bronze_weight_value', $courseid );

$toform ['read_posts_gold_count'] = block_mt_get_awards_settings ( 'mt_awards:read_posts_gold_count_value', $courseid );
$toform ['read_posts_silver_count'] = block_mt_get_awards_settings ( 'mt_awards:read_posts_silver_count_value', $courseid );
$toform ['read_posts_bronze_count'] = block_mt_get_awards_settings ( 'mt_awards:read_posts_bronze_count_value', $courseid );

$toform ['read_posts_gold_weight'] = block_mt_get_awards_settings ( 'mt_awards:read_posts_gold_weight_value', $courseid );
$toform ['read_posts_silver_weight'] = block_mt_get_awards_settings ( 'mt_awards:read_posts_silver_weight_value', $courseid );
$toform ['read_posts_bronze_weight'] = block_mt_get_awards_settings ( 'mt_awards:read_posts_bronze_weight_value', $courseid );

$toform ['rating_posts_gold_count'] = block_mt_get_awards_settings ( 'mt_awards:rating_posts_gold_count_value', $courseid );
$toform ['rating_posts_silver_count'] = block_mt_get_awards_settings ( 'mt_awards:rating_posts_silver_count_value', $courseid );
$toform ['rating_posts_bronze_count'] = block_mt_get_awards_settings ( 'mt_awards:rating_posts_bronze_count_value', $courseid );

$toform ['rating_posts_gold_weight'] = block_mt_get_awards_settings ( 'mt_awards:rating_posts_gold_weight_value', $courseid );
$toform ['rating_posts_silver_weight'] = block_mt_get_awards_settings ( 'mt_awards:rating_posts_silver_weight_value', $courseid );
$toform ['rating_posts_bronze_weight'] = block_mt_get_awards_settings ( 'mt_awards:rating_posts_bronze_weight_value', $courseid );

$toform ['milestones_gold_weight'] = block_mt_get_awards_settings ( 'mt_awards:milestones_gold_days_value', $courseid );
$toform ['milestones_silver_weight'] = block_mt_get_awards_settings ( 'mt_awards:milestones_silver_days_value', $courseid );
$toform ['milestones_bronze_weight'] = block_mt_get_awards_settings ( 'mt_awards:milestones_bronze_days_value', $courseid );

$toform ['achievements_gold_weight'] = block_mt_get_awards_settings ( 'mt_awards:achievements_gold_weight_value', $courseid );
$toform ['achievements_silver_weight'] = block_mt_get_awards_settings ( 'mt_awards:achievements_silver_weight_value', $courseid );
$toform ['achievements_bronze_weight'] = block_mt_get_awards_settings ( 'mt_awards:achievements_bronze_weight_value', $courseid );

$mtawards->set_data ( $toform );

if ($mtawards->is_cancelled ()) {
    // Cancelled forms redirect to the admin main page.
    $courseurl = new moodle_url ( '/blocks/mt/admin.php', array (
            'courseid' => $courseid
    ) );
    redirect ( $courseurl );
} else if ($fromform = $mtawards->get_data ()) {
    // If submit was clicked insert or update the selection and redirect to
    // admin main page.
    $courseurl = new moodle_url ( '/blocks/mt/admin.php', array (
            'courseid' => $courseid
    ) );

    block_mt_upd_awards_settings('mt_awards:grades_gold_count_value', $courseid, $fromform->grades_gold_count);
    block_mt_upd_awards_settings('mt_awards:grades_silver_count_value', $courseid, $fromform->grades_silver_count);
    block_mt_upd_awards_settings('mt_awards:grades_bronze_count_value', $courseid, $fromform->grades_bronze_count);

    block_mt_upd_awards_settings('mt_awards:time_online_gold_weight_value', $courseid, $fromform->online_time_gold_weight);
    block_mt_upd_awards_settings('mt_awards:time_online_silver_weight_value', $courseid, $fromform->online_time_silver_weight);
    block_mt_upd_awards_settings('mt_awards:time_online_bronze_weight_value', $courseid, $fromform->online_time_bronze_weight);

    block_mt_upd_awards_settings('mt_awards:num_posts_gold_count_value', $courseid, $fromform->num_posts_gold_count);
    block_mt_upd_awards_settings('mt_awards:num_posts_silver_count_value', $courseid, $fromform->num_posts_silver_count);
    block_mt_upd_awards_settings('mt_awards:num_posts_bronze_count_value', $courseid, $fromform->num_posts_bronze_count);

    block_mt_upd_awards_settings('mt_awards:num_posts_gold_weight_value', $courseid, $fromform->num_posts_gold_weight);
    block_mt_upd_awards_settings('mt_awards:num_posts_silver_weight_value', $courseid, $fromform->num_posts_silver_weight);
    block_mt_upd_awards_settings('mt_awards:num_posts_bronze_weight_value', $courseid, $fromform->num_posts_bronze_weight);

    block_mt_upd_awards_settings('mt_awards:read_posts_gold_count_value', $courseid, $fromform->read_posts_gold_count);
    block_mt_upd_awards_settings('mt_awards:read_posts_silver_count_value', $courseid, $fromform->read_posts_silver_count);
    block_mt_upd_awards_settings('mt_awards:read_posts_bronze_count_value', $courseid, $fromform->read_posts_bronze_count);

    block_mt_upd_awards_settings('mt_awards:read_posts_gold_weight_value', $courseid, $fromform->read_posts_gold_weight);
    block_mt_upd_awards_settings('mt_awards:read_posts_silver_weight_value', $courseid, $fromform->read_posts_silver_weight);
    block_mt_upd_awards_settings('mt_awards:read_posts_bronze_weight_value', $courseid, $fromform->read_posts_bronze_weight);

    block_mt_upd_awards_settings('mt_awards:rating_posts_gold_count_value', $courseid, $fromform->rating_posts_gold_count);
    block_mt_upd_awards_settings('mt_awards:rating_posts_silver_count_value', $courseid, $fromform->rating_posts_silver_count);
    block_mt_upd_awards_settings('mt_awards:rating_posts_bronze_count_value', $courseid, $fromform->rating_posts_bronze_count);

    block_mt_upd_awards_settings ( 'mt_awards:rating_posts_gold_weight_value', $courseid, $fromform->rating_posts_gold_weight );
    block_mt_upd_awards_settings ( 'mt_awards:rating_posts_silver_weight_value', $courseid, $fromform->rating_posts_silver_weight );
    block_mt_upd_awards_settings ( 'mt_awards:rating_posts_bronze_weight_value', $courseid, $fromform->rating_posts_bronze_weight );

    block_mt_upd_awards_settings ( 'mt_awards:milestones_gold_days_value', $courseid, $fromform->milestones_gold_weight );
    block_mt_upd_awards_settings ( 'mt_awards:milestones_silver_days_value', $courseid, $fromform->milestones_silver_weight );
    block_mt_upd_awards_settings ( 'mt_awards:milestones_bronze_days_value', $courseid, $fromform->milestones_bronze_weight );

    block_mt_upd_awards_settings ( 'mt_awards:achievements_gold_weight_value', $courseid, $fromform->achievements_gold_weight );
    block_mt_upd_awards_settings ( 'mt_awards:achievements_silver_weight_value', $courseid, $fromform->achievements_silver_weight );
    block_mt_upd_awards_settings ( 'mt_awards:achievements_bronze_weight_value', $courseid, $fromform->achievements_bronze_weight );

    redirect ( $courseurl );
} else {
    // Form didn't validate or this is the first display.
    $site = get_site ();
    echo $OUTPUT->header ();
    $mtawards->display ();
    echo $OUTPUT->footer ();
}

$settingsnode = $PAGE->settingsnav->add ( get_string ( 'mt_awards:admin_settings', 'block_mt' ) );
$editurl = new moodle_url ( '/blocks/mt/mt_awards/admin.php', array (
        'courseid' => $courseid
) );
$editnode = $settingsnode->add ( get_string ( 'mt_awards:admin_page', 'block_mt' ), $editurl );
$editnode->make_active ();
