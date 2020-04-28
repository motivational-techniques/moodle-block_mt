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
 * This determines the next level award
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * determine next level total posts num
 *
 * @param string $userid
 * @param string $courseid
 * @param string $period
 * @return string
 */
function determine_next_level_total_posts_num($userid, $courseid, $period) {
    global $DB;
    // Get number of posts.
    $parameters = array(
        'courseid' => $courseid,
        'userid' => $userid,
        'period' => $period,
        'period_type' => RANK_PERIOD_MONTHLY
    );
    if ($DB->record_exists('block_mt_ranks_num_posts', $parameters)) {
        $numberofposts = $DB->get_record('block_mt_ranks_num_posts', $parameters, 'num_posts')->num_posts;
    } else {
        $numberofposts = 0;
    }

    $goldaward = get_awards_settings('mt_awards:num_posts_gold_count_value', $courseid);
    $silveraward = get_awards_settings('mt_awards:num_posts_silver_count_value', $courseid);
    $bronzeaward = get_awards_settings('mt_awards:num_posts_bronze_count_value', $courseid);

    $togetbronze = $bronzeaward - $numberofposts;
    $togetsilver = $silveraward - $numberofposts;
    $togetgold = $goldaward - $numberofposts;

    $achievedgold = $numberofposts - $goldaward;
    $achievevedsilver = $numberofposts - $silveraward;
    $achievedbronze = $numberofposts - $bronzeaward;

    $returnval['currentnum'] = $numberofposts;
    $returnval['currentaward'] = "";
    $returnval['nextaward'] = get_string('mt_awards:bronze', 'block_mt');
    $returnval['nextlevelnum'] = $togetbronze;
    $returnval['percentage'] = 0;
    $returnval['awardnum'] = 0;

    if ($achievedgold >= 0) {
        $returnval['nextaward'] = "";
        $returnval['currentaward'] = get_string('mt_awards:gold', 'block_mt');
        $returnval['awardnum'] = $goldaward;
        $returnval['nextlevelnum'] = 0;
        $returnval['percentage'] = $numberofposts / $returnval['awardnum'];
    } else if ($achievevedsilver >= 0) {
        $returnval['nextaward'] = get_string('mt_awards:gold', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:silver', 'block_mt');
        $returnval['awardnum'] = $silveraward;
        $returnval['nextlevelnum'] = $togetgold;
        $returnval['percentage'] = $numberofposts / $returnval['awardnum'];
    } else if ($achievedbronze >= 0) {
        $returnval['nextaward'] = get_string('mt_awards:silver', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:bronze', 'block_mt');
        $returnval['awardnum'] = $bronzeaward;
        $returnval['nextlevelnum'] = $togetsilver;
        $returnval['percentage'] = $numberofposts / $returnval['awardnum'];
    }
    return $returnval;
}

/**
 * determine next level read posts num
 *
 * @param string $userid
 * @param string $courseid
 * @param string $period
 * @return string
 */
function determine_next_level_read_posts_num($userid, $courseid, $period) {
    global $DB;

    // Get number of posts read.
    $parameters = array(
        'courseid' => $courseid,
        'userid' => $userid,
        'period' => $period,
        'period_type' => RANK_PERIOD_MONTHLY
    );
    if ($DB->record_exists('block_mt_ranks_read_posts', $parameters)) {
        $percentread = $DB->get_record('block_mt_ranks_read_posts', $parameters, 'percent_read')->percent_read;
    } else {
        $percentread = 0;
    }
    $goldaward = get_awards_settings('mt_awards:read_posts_gold_count_value', $courseid);
    $silveraward = get_awards_settings('mt_awards:read_posts_silver_count_value', $courseid);
    $bronzeaward = get_awards_settings('mt_awards:read_posts_bronze_count_value', $courseid);

    $togetbronze = $bronzeaward;
    $togetsilver = $silveraward;
    $togetgold = $goldaward;

    if ($percentread >= $goldaward) {
        $returnval['nextaward'] = get_string('mt_awards:gold', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:gold', 'block_mt');
        $returnval['nextlevelnum'] = 0;
    } else if ($percentread >= $silveraward) {
        $returnval['nextaward'] = get_string('mt_awards:gold', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:silver', 'block_mt');
        $returnval['nextlevelnum'] = $togetgold;
    } else if ($percentread >= $bronzeaward) {
        $returnval['nextaward'] = get_string('mt_awards:silver', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:bronze', 'block_mt');
        $returnval['nextlevelnum'] = $togetsilver;
    } else {
        $returnval['nextaward'] = get_string('mt_awards:bronze', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:bronze', 'block_mt');
        $returnval['nextlevelnum'] = $togetbronze;
    }

    $returnval['currentnum'] = $percentread;
    $returnval['percentage'] = (100 - ($returnval['nextlevelnum'] - $percentread)) / 100;
    return $returnval;
}

/**
 * determine next level time online
 *
 * @param string $courseid
 * @param string $period
 * @return string
 */
function determine_next_level_time_online($courseid, $period) {
    global $DB;

    $parameters = array(
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY,
            'period' => $period,
            'rank_type_id' => RANK_TYPE_ONLINE_TIME
    );
    $returnval['nextlevelnum'] = 0;
    $returnval['nextaward'] = get_string('mt_awards:bronze', 'block_mt');
    $returnval['currentaward'] = '';
    $returnval['currentnum'] = 0;
    $returnval['percentage'] = 0;
    $returnval['totalrecords'] = $DB->count_records('block_mt_ranks_user', $parameters);;
    return $returnval;
}

/**
 * determine next level grades overall
 *
 * @param string $courseid
 * @param string $userid
 * @return string
 */
function determine_next_level_grades_overall($courseid, $userid) {
    global $DB;

    $sql = "SELECT (finalgrade / rawgrademax * 100) as finalgrade
        FROM {grade_grades}
        JOIN {grade_items}
        ON {grade_grades}.itemid={grade_items}.id
        WHERE itemtype=:itemtype
        AND courseid=:courseid
        AND userid=:userid";
    $parameters = array(
            'itemtype' => 'course',
            'courseid' => $courseid,
            'userid' => $userid
    );
    $exists = $DB->record_exists_sql($sql, $parameters);
    if ($exists) {
        $finalgrade = $DB->get_record_sql($sql, $parameters)->finalgrade;
    } else {
        $finalgrade = 0;
    }

    $goldaward = get_awards_settings('mt_awards:grades_gold_count_value', $courseid);
    $silveraward = get_awards_settings('mt_awards:grades_silver_count_value', $courseid);
    $bronzeaward = get_awards_settings('mt_awards:grades_bronze_count_value', $courseid);

    $togetbronze = $bronzeaward;
    $togetsilver = $silveraward;
    $togetgold = $goldaward;

    if ($finalgrade >= $goldaward) {
        $returnval['nextaward'] = get_string('mt_awards:gold', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:gold', 'block_mt');
        $returnval['nextlevelnum'] = 0;
    } else if ($finalgrade >= $silveraward) {
        $returnval['nextaward'] = get_string('mt_awards:gold', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:silver', 'block_mt');
        $returnval['nextlevelnum'] = $togetgold;
    } else if ($finalgrade >= $bronzeaward) {
        $returnval['nextaward'] = get_string('mt_awards:silver', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:bronze', 'block_mt');
        $returnval['nextlevelnum'] = $togetsilver;
    } else {
        $returnval['nextaward'] = get_string('mt_awards:bronze', 'block_mt');
        $returnval['currentaward'] = get_string('mt_awards:bronze', 'block_mt');
        $returnval['nextlevelnum'] = $togetbronze;
    }

    $returnval['currentnum'] = $finalgrade;
    $returnval['percentage'] = $finalgrade / 100;
    return $returnval;
}