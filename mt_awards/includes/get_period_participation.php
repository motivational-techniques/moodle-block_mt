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
 * This gets periods for participation awardsthe grade award name
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * get all periods for read posts
 *
 * @param string $courseid
 * @return array
 */
function get_periods_read_posts_all($courseid) {
    global $DB;
    $parameters = array(
        'courseid' => $courseid,
        'period_type' => RANK_PERIOD_MONTHLY
    );
    $sql = "SELECT period
            FROM {block_mt_ranks_read_posts}
            join {user}
            on {user}.id={block_mt_ranks_read_posts}.userid
            WHERE courseid=:courseid
            and {user}.trackforums=1
            AND period_type=:period_type
            GROUP BY period";
    return $DB->get_records_sql($sql, $parameters);
}

/**
 * get periods for read posts since last run period
 *
 * @param string $courseid
 * @return array
 */
function get_periods_read_posts_since_last_run_period($courseid) {
    global $DB;
    $lastperiod = get_last_period_run_awards($courseid, RANK_TYPE_WEEKLY_POSTS);
    $parameters = array(
            'courseid' => $courseid,
            'period' => $lastperiod,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    $sql = "SELECT period
            FROM {block_mt_ranks_read_posts}
            join {user}
            on {user}.id={block_mt_ranks_read_posts}.userid
            WHERE courseid=:courseid
            and {user}.trackforums=1
            AND period_type=:period_type
            AND period >= :period
            GROUP BY period";
    return $DB->get_records_sql($sql, $parameters);
}

/**
 * get periods for read posts
 *
 * @param string $courseid
 * @return array
 */
function get_periods_read_posts($courseid) {
    if (has_no_last_period_run_awards($courseid, RANK_TYPE_WEEKLY_POSTS)) {
        $periods = get_periods_read_posts_all($courseid);
    } else {
        $periods = get_periods_read_posts_since_last_run_period($courseid);
    }
    return $periods;
}

/**
 * get periods for number posts
 *
 * @param string $courseid
 * @return array
 */
function get_periods_number_posts($courseid) {
    if (has_no_last_period_run_awards($courseid, RANK_TYPE_NUMBER_POSTS)) {
        $periods = get_periods_number_posts_all($courseid);
    } else {
        $periods = get_periods_number_posts_since_last_run_period($courseid);
    }
    return $periods;
}

/**
 * get all periods for number posts
 *
 * @param string $courseid
 * @return array
 */
function get_periods_number_posts_all($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    $sql = "SELECT period
            FROM {block_mt_ranks_num_posts}
            WHERE courseid=:courseid
            AND period_type=:period_type
            GROUP BY period";
    return $DB->get_records_sql($sql, $parameters);
}

/**
 * get periods for number posts since last run period
 *
 * @param string $courseid
 * @return array
 */
function get_periods_number_posts_since_last_run_period($courseid) {
    global $DB;
    $lastperiod = get_last_period_run_awards($courseid, RANK_TYPE_NUMBER_POSTS);
    $parameters = array (
            'courseid' => $courseid,
            'period' => $lastperiod,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    $sql = "SELECT period
            FROM {block_mt_ranks_num_posts}
            WHERE courseid=:courseid
            AND period_type=:period_type
            AND period >=:period
            GROUP BY period";
    return $DB->get_records_sql($sql, $parameters);
}

/**
 * get periods for ratings posts
 *
 * @param string $courseid
 * @return array
 */
function get_periods_rating_posts($courseid) {
    if (has_no_last_period_run_awards($courseid, RANK_TYPE_POST_RATING)) {
        $periods = get_periods_rating_posts_all($courseid);
    } else {
        $periods = get_periods_rating_posts_since_last_run($courseid);
    }
    return $periods;
}

/**
 * get all periods for ratings posts
 *
 * @param string $courseid
 * @return array
 */
function get_periods_rating_posts_all($courseid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    $sql = "SELECT period
            FROM {block_mt_ranks_rating_posts}
            WHERE courseid=:courseid
            AND period_type=:period_type
            GROUP BY period";
    return $DB->get_records_sql($sql, $parameters);
}

/**
 * get periods for ratings posts since last run period
 *
 * @param string $courseid
 * @return array
 */
function get_periods_rating_posts_since_last_run($courseid) {
    global $DB;
    $lastperiod = get_last_period_run_awards($courseid, RANK_TYPE_POST_RATING);
    $sql = "SELECT period
            FROM {block_mt_ranks_rating_posts}
            WHERE courseid=:courseid
            AND period_type=:period_type
            AND period > :period
            GROUP BY period";
    $parameters = array (
            'courseid' => $courseid,
            'period' => $lastperiod,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    return $DB->get_records_sql($sql, $parameters);
}