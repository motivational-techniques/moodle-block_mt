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
 * This generates the participation rankings
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate all ranks for the number of posts
 * @param integer $courseid
 * @param integer $userranking
 * @param array $periodparam
 */
function block_mt_generate_ranks_number_posts($courseid, $userranking, $periodparam) {
    $onlineusers = block_mt_students_in_course_forum($courseid);
    foreach ($onlineusers as $onlineuser) {
        $numberposts = get_number_forum_posts_student($courseid, $onlineuser->userid,
                $periodparam->year, $periodparam->month);
        add_update_number_posts_monthly($numberposts, $onlineuser->userid, $periodparam->period, $courseid);
    }
    $rankings = get_number_forum_posts_rankings_monthly($periodparam->period, $courseid);
    foreach ($rankings as $ranking) {
        $userranking->rank = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = $periodparam->period;
        $userranking->rank_type_id = RANK_TYPE_NUMBER_POSTS;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_number_posts', 'block_mt', $periodparam);
        $userranking->period_type = RANK_PERIOD_MONTHLY;
        ranks_process_entry ( $userranking );
    }
}

/**
 * generate active ranks for the number of posts
 * @param integer $courseid
 * @param integer $userranking
 * @param array $periodparam
 */
function block_mt_generate_ranks_number_posts_active($courseid, $userranking, $periodparam) {
    $rankings = get_number_forum_posts_rankings_monthly_active($periodparam->period, $courseid);
    foreach ($rankings as $ranking) {
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = $periodparam->period;
        $userranking->rank_type_id = RANK_TYPE_NUMBER_POSTS;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_number_posts', 'block_mt', $periodparam);
        $userranking->period_type = RANK_PERIOD_MONTHLY;
        $userranking->rankactive = $ranking->rank;
        active_ranks_process_entry($userranking);
    }
}
/**
 * generate ranks for the all posts
 * @param integer $courseid
 * @param string $userranking
 */
function block_mt_generate_ranks_number_posts_all($courseid, $userranking) {
    if (has_no_last_period_run_ranks($courseid, RANK_TYPE_NUMBER_POSTS)) {
        $periodyears = get_all_years_forum_posts($courseid);
    } else {
        $lastperiodyear = get_last_period_run_ranks_year($courseid, RANK_TYPE_NUMBER_POSTS);
        $periodyears = get_year_forum_posts($courseid, $lastperiodyear);
    }
    foreach ($periodyears as $periodyear) {
        if (has_no_last_period_run_ranks($courseid, RANK_TYPE_NUMBER_POSTS)) {
            $periodmonths = get_all_months_forum_posts($courseid, $periodyear->year);
        } else {
            $lastperiodmonth = get_last_period_run_ranks_month($courseid, RANK_TYPE_NUMBER_POSTS);
            $periodmonths = get_month_year_forum_posts($courseid, $periodyear->year, $lastperiodmonth);
        }
        foreach ($periodmonths as $periodmonth) {
            $period = new stdClass ();
            $period->year = $periodyear->year;
            $period->month = $periodmonth->month;
            $period->period = get_string('mt_rankings:generate_rank_period', 'block_mt', $period);
            block_mt_generate_ranks_number_posts($courseid, $userranking, $period);
            block_mt_generate_ranks_number_posts_active($courseid, $userranking, $period);
        }
    }
    update_last_period_run_ranks($courseid, RANK_TYPE_NUMBER_POSTS);
}

/**
 * generate active ranks for the number of overall posts
 * @param integer $courseid
 * @param string $userranking
 */
function block_mt_generate_ranks_number_posts_overall_active($courseid, $userranking) {
    $rankings = get_average_forum_posts_overall_rankings($courseid);
    foreach ($rankings as $ranking) {
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_NUMBER_POSTS;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_average_number_posts', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        $userranking->rankactive = $ranking->rank;
        active_ranks_process_entry($userranking);
    }
}

/**
 * generate all ranks for the number of overall posts
 * @param integer $courseid
 * @param string $userranking
 */
function block_mt_generate_ranks_number_posts_overall($courseid, $userranking) {
    $rankings = get_average_forum_posts_overall_rankings($courseid);
    foreach ($rankings as $ranking) {
        $userranking->rank = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_NUMBER_POSTS;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_average_number_posts', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        ranks_process_entry ( $userranking );
    }
}

/**
 * generate ranks for read postings
 * @param integer $courseid
 * @param integer $userranking
 * @param array $periodparam
 */
function block_mt_generate_ranks_read_posts($courseid, $userranking, $periodparam) {
    $onlineusers = block_mt_students_in_course_forum($courseid);
    $numberposts = get_number_forum_posts_year_month($courseid, $periodparam->year, $periodparam->month);
    foreach ($onlineusers as $onlineuser) {
        $readposts = get_read_forum_posts_period($courseid, $onlineuser->userid, $periodparam->year, $periodparam->month);

        $percentageread = $readposts / $numberposts * 100;
        add_update_read_posts_monthly($numberposts, $percentageread, $readposts, $onlineuser->userid,
            $periodparam->period, $courseid);
    }
    $rankings = get_read_forum_posts_rankings_monthly($periodparam->period, $courseid);
    foreach ($rankings as $ranking) {
        $userranking->rank = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = $periodparam->period;
        $userranking->rank_type_id = RANK_TYPE_WEEKLY_POSTS;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_read_posts', 'block_mt', date ( "Y-n" ));
        $userranking->period_type = RANK_PERIOD_MONTHLY;
        ranks_process_entry ( $userranking );
    }
}

/**
 * generate active ranks for read postings
 * @param integer $courseid
 * @param integer $userranking
 * @param array $periodparam
 */
function block_mt_generate_ranks_read_posts_active($courseid, $userranking, $periodparam) {
    $rankings = get_read_forum_posts_rankings_monthly_active($periodparam->period, $courseid);
    foreach ($rankings as $ranking) {
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = $periodparam->period;
        $userranking->rank_type_id = RANK_TYPE_WEEKLY_POSTS;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_read_posts', 'block_mt', date ( "Y-n" ));
        $userranking->period_type = RANK_PERIOD_MONTHLY;
        $userranking->rankactive = $ranking->rank;
        active_ranks_process_entry($userranking);
    }
}

/**
 * generate ranks read posts all
 * @param integer $courseid
 * @param integer $userranking
 */
function block_mt_generate_ranks_read_posts_all($courseid, $userranking) {
    global $DB, $CFG;

    if (has_no_last_period_run_ranks($courseid, RANK_TYPE_WEEKLY_POSTS)) {
        $periodyears = get_all_years_tracking_forum_posts($courseid);
    } else {
        $lastperiodyear = get_last_period_run_ranks_year($courseid, RANK_TYPE_WEEKLY_POSTS);
        $periodyears = get_period_years_ranks($courseid, $lastperiodyear);
    }

    foreach ($periodyears as $periodyear) {
        if (has_no_last_period_run_ranks($courseid, RANK_TYPE_WEEKLY_POSTS)) {
            // Get all period years.
            if ($CFG->dbtype == DB_TYPE_POSTGRES) {
                $sql = "SELECT extract (month from to_timestamp(created)) AS month,
                    extract (YEAR from to_timestamp(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    join {user}
                    on {user}.id={forum_posts}.userid
                    WHERE course=:course and extract (year from to_timestamp(created))=:year
                    and {user}.trackforums=1
                    GROUP BY month, year";
            } else {
                $sql = "SELECT MONTH(FROM_UNIXTIME(created)) AS month,
                    YEAR(FROM_UNIXTIME(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    join {user}
                    on {user}.id={forum_posts}.userid
                    WHERE course=:course and YEAR(FROM_UNIXTIME(created))=:year
                    and {user}.trackforums=1
                    GROUP BY month";
            }
            $periodmonths = $DB->get_records_sql ( $sql, array (
                'course' => $courseid,
                'year' => $periodyear->year
            ) );
        } else {
            $lastperiodmonth = get_last_period_run_ranks_month($courseid, RANK_TYPE_WEEKLY_POSTS);
            if ($CFG->dbtype == DB_TYPE_POSTGRES) {
                $sql = "select extract (month from to_timestamp(created)) AS month,
                    extract (year from to_timestamp(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    join {user}
                    on {user}.id={forum_posts}.userid
                    WHERE course=:course and extract (year from to_timestamp(created))=:year
                    AND extract (month from to_timestamp(created))>=:month
                    and {user}.trackforums=1
                    GROUP BY month, year";
            } else {
                $sql = "SELECT MONTH(FROM_UNIXTIME(created)) AS month,
                    YEAR(FROM_UNIXTIME(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    join {user}
                    on {user}.id={forum_posts}.userid
                    WHERE course=:course and YEAR(FROM_UNIXTIME(created))=:year
                    and {user}.trackforums=1
                    AND MONTH(FROM_UNIXTIME(created))>=:month
                    GROUP BY month";
            }
            $periodmonthsparams = array (
                            'course' => $courseid,
                            'year' => $periodyear->year,
                            'month' => $lastperiodmonth
                        );
            $periodmonths = $DB->get_records_sql ( $sql, $periodmonthsparams );
        }
        foreach ($periodmonths as $periodmonth) {
            $period = new stdClass ();
            $period->year = $periodyear->year;
            $period->month = $periodmonth->month;
            $period->period = get_string('mt_rankings:generate_rank_period', 'block_mt', $period);
            block_mt_generate_ranks_read_posts($courseid, $userranking, $period);
            block_mt_generate_ranks_read_posts_active($courseid, $userranking, $period);
        }
    }
    update_last_period_run_ranks($courseid, RANK_TYPE_WEEKLY_POSTS);
}

/**
 * generate ranks read posts overall
 * @param integer $courseid
 * @param integer $userranking
 */
function block_mt_generate_ranks_read_posts_overall($courseid, $userranking) {
    $userranking->rank_type_id = RANK_TYPE_WEEKLY_POSTS;
    $userranking->rankname = get_string('mt_rankings:generate_rank_participation_read_posts_overall_period',
        'block_mt', date ( "Y-n" ));
    $userranking->period_type = RANK_PERIOD_OVERALL;

    $onlineusers = block_mt_students_in_course_forum($courseid);
    $numberposts = get_number_forum_posts($courseid);
    foreach ($onlineusers as $onlineuser) {
        $readposts = get_read_forum_posts_rankings_user($onlineuser->userid, $courseid);
        if ($numberposts > 0) {
            $percentageread = $readposts / $numberposts * 100;
        } else {
            $percentageread = 0;
        }
        add_update_read_posts_overall($numberposts, $percentageread, $readposts, $onlineuser->userid, $courseid);
    }
    $rankings = get_read_forum_posts_rankings_overall($courseid);
    foreach ($rankings as $ranking) {
        $userranking->rank = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_WEEKLY_POSTS;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_read_posts_overall', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        ranks_process_entry ( $userranking );
    }
}

/**
 * generate active ranks read posts overall
 * @param integer $courseid
 * @param integer $userranking
 */
function block_mt_generate_ranks_read_posts_overall_active($courseid, $userranking) {
    $rankings = get_read_forum_posts_rankings_overall_active($courseid);
    foreach ($rankings as $ranking) {
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_WEEKLY_POSTS;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_read_posts_overall', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        $userranking->rankactive = $ranking->rank;
        active_ranks_process_entry($userranking);
    }
}

/**
 * generate ranks ratings posts
 * @param integer $courseid
 * @param integer $userranking
 * @param array $periodparam
 */
function block_mt_generate_ranks_ratings_posts($courseid, $userranking, $periodparam) {
    global $DB;

    $onlineusers = block_mt_students_in_course_forum($courseid);
    foreach ($onlineusers as $onlineuser) {
        $ratingsposts = get_ratings_forum_posts_period($courseid, $onlineuser->userid,
                $periodparam->year, $periodparam->month);

        $ratingpercent = 0;
        $ratingcount = 0;
        $ratingtotal = 0;
        foreach ($ratingsposts as $ratingspost) {
            $ratingcount = $ratingcount + 1;
            if ($ratingspost->scaleid < 0) {
                // Lookup in mdl_scale, need to count entries.
                $scale = $DB->get_record ( 'scale', array (
                    'id' => - $ratingspost->scaleid
                ), 'scale' )->scale;

                $scalecount = substr_count ( $scale, ',' ) + 1;
                $ratingtotal = $ratingtotal + $ratingspost->rating / $scalecount * 100;
            } else {
                $ratingtotal = $ratingtotal + $ratingspost->rating / $ratingspost->scaleid * 100;
            }
        }
        if ($ratingcount > 0) {
            $ratingpercent = $ratingtotal / $ratingcount;
        }
        add_update_rating_posts_monthly($ratingpercent, $periodparam->period, $courseid, $onlineuser->userid);
    }
    $rankings = get_ratings_forum_posts_rankings_monthly($periodparam->period, $courseid);
    foreach ($rankings as $ranking) {
        $userranking->rank = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = $periodparam->period;
        $userranking->rank_type_id = RANK_TYPE_POST_RATING;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_rating_posts', 'block_mt', $periodparam);
        $userranking->period_type = RANK_PERIOD_MONTHLY;
        ranks_process_entry ( $userranking );
    }
}

/**
 * generate active ranks ratings posts
 * @param integer $courseid
 * @param integer $userranking
 * @param array $periodparam
 */
function block_mt_generate_ranks_ratings_posts_active($courseid, $userranking, $periodparam) {
    $rankings = get_ratings_forum_posts_rankings_monthly_active($periodparam->period, $courseid);
    foreach ($rankings as $ranking) {
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = $periodparam->period;
        $userranking->rank_type_id = RANK_TYPE_POST_RATING;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_rating_posts', 'block_mt', $periodparam);
        $userranking->period_type = RANK_PERIOD_MONTHLY;
        $userranking->rankactive = $ranking->rank;
        active_ranks_process_entry($userranking);
    }
}

/**
 * generate ranks for ratings of posts all
 * @param integer $courseid
 * @param integer $userranking
 */
function block_mt_generate_ranks_ratings_posts_all($courseid, $userranking) {
    global $DB, $CFG;

    if (has_no_last_period_run_ranks($courseid, RANK_TYPE_POST_RATING)) {
        $periodyears = get_all_years_forum_posts($courseid);
    } else {
        switch ($CFG->dbtype) {
            case DB_TYPE_POSTGRES :
                $sql = "SELECT extract (year from to_timestamp(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    WHERE course=:course and extract (year from to_timestamp(created))=:year
                    GROUP BY year";
                break;
            default :
                $sql = "SELECT YEAR(FROM_UNIXTIME(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    WHERE course=:course and YEAR(FROM_UNIXTIME(created))=:year
                    GROUP BY year";
        }
        $lastperiodyear = get_last_period_run_ranks_year($courseid, RANK_TYPE_WEEKLY_POSTS);
        $periodyearsparams = array (
                    'course' => $courseid,
                    'year' => $lastperiodyear
                );
        $periodyears = $DB->get_records_sql ($sql, $periodyearsparams);
    }

    foreach ($periodyears as $periodyear) {
        if (has_no_last_period_run_ranks($courseid, RANK_TYPE_POST_RATING)) {
            $periodmonths = get_all_months_forum_posts($courseid, $periodyear->year);
        } else {
            $lastperiodmonth = get_last_period_run_ranks_month($courseid, RANK_TYPE_POST_RATING);
            if ($CFG->dbtype == DB_TYPE_POSTGRES) {
                $sql = "SELECT extract (month from to_timestamp(created)) AS month,
                    extract (YEAR from to_timestamp(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    WHERE course=:course and extract (YEAR from to_timestamp(created))=:year
                    AND extract (month from to_timestamp(created))>=:month
                    GROUP BY month, year";
            } else {
                $sql = "SELECT MONTH(FROM_UNIXTIME(created)) AS month,
                    YEAR(FROM_UNIXTIME(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    WHERE course=:course and YEAR(FROM_UNIXTIME(created))=:year
                    AND MONTH(FROM_UNIXTIME(created))>=:month
                    GROUP BY month";
            }
            $periodmonths = $DB->get_records_sql ( $sql, array (
                'course' => $courseid,
                'year' => $periodyear->year,
                'month' => $lastperiodmonth
            ) );
        }
        foreach ($periodmonths as $periodmonth) {
            $period = new stdClass ();
            $period->year = $periodyear->year;
            $period->month = $periodmonth->month;
            $period->period = get_string('mt_rankings:generate_rank_period', 'block_mt', $period);
            block_mt_generate_ranks_ratings_posts($courseid, $userranking, $period);
            block_mt_generate_ranks_ratings_posts_active($courseid, $userranking, $period);
        }
    }
    update_last_period_run_ranks($courseid, RANK_TYPE_POST_RATING);
}

/**
 * generate ranks for ratings posts overall
 * @param integer $courseid
 * @param integer $userranking
 */
function block_mt_generate_ranks_ratings_posts_overall($courseid, $userranking) {
    $userranking->rank_type_id = RANK_TYPE_POST_RATING;
    $userranking->rankname = get_string('mt_rankings:generate_rank_participation_rating_posts_overall_number_posts',
        'block_mt', date ( "Y-n" ));
    $userranking->period_type = RANK_PERIOD_OVERALL;

    $onlineusers = block_mt_students_in_course_forum($courseid);
    foreach ($onlineusers as $onlineuser) {
        $ratingsposts = get_ratings_forum_posts_overall($courseid, $onlineuser->userid);
        $ratingpercent = 0;
        $ratingcount = 0;
        $ratingtotal = 0;
        foreach ($ratingsposts as $ratingpost) {
            $ratingcount = $ratingcount + 1;
            if ($ratingpost->scaleid < 0) {
                $scalecount = get_ratings_scale_count($ratingpost->scaleid);
                $ratingtotal = $ratingtotal + $ratingpost->rating / $scalecount * 100;
            } else {
                $ratingtotal = $ratingtotal + $ratingpost->rating / $ratingpost->scaleid * 100;
            }
        }

        if ($ratingcount > 0) {
            $ratingpercent = $ratingtotal / $ratingcount;
        }
        add_update_rating_posts_overall($ratingpercent, $courseid, $onlineuser->userid);
    }
    $rankings = get_ratings_posts_rankings_overall($courseid);
    foreach ($rankings as $ranking) {
        $userranking->rank = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_POST_RATING;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_rating_posts_overall', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        ranks_process_entry ( $userranking );
    }
}

/**
 * generate active ranks for ratings posts overall
 * @param integer $courseid
 * @param integer $userranking
 */
function block_mt_generate_ranks_ratings_posts_overall_active($courseid, $userranking) {
    $rankings = get_ratings_posts_rankings_overall_active($courseid);
    foreach ($rankings as $ranking) {
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_POST_RATING;
        $userranking->rankname = get_string('mt_rankings:generate_rank_participation_rating_posts_overall', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        $userranking->rankactive = $ranking->rank;
        active_ranks_process_entry($userranking);
    }
}

/**
 * add or update number posts rankings
 * @param integer $numberposts
 * @param string $userid
 * @param string $period
 * @param string $courseid
 */
function add_update_number_posts_monthly($numberposts, $userid, $period, $courseid) {
    global $DB;
    if ($numberposts > 0) {
        $parameters = array (
                'userid' => $userid,
                'period' => $period,
                'courseid' => $courseid,
                'period_type' => RANK_PERIOD_MONTHLY
        );
        $recordcount = $DB->count_records ( 'block_mt_ranks_num_posts', $parameters );
        if ($recordcount < 1) {
            $parameters ['num_posts'] = $numberposts;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->insert_record ( 'block_mt_ranks_num_posts', $parameters );
        } else {
            $parameters ['id'] = $DB->get_field ( 'block_mt_ranks_num_posts', 'id', $parameters );
            $parameters ['num_posts'] = $numberposts;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->update_record ( 'block_mt_ranks_num_posts', $parameters );
        }
    }
}

/**
 * add or update read posts rankings overall
 * @param string $numberposts
 * @param string $percentageread
 * @param string $readposts
 * @param string $userid
 * @param string $courseid
 */
function add_update_read_posts_overall($numberposts, $percentageread, $readposts, $userid, $courseid) {
    global $DB;
    if ($numberposts > 0) {
        $parameters = array (
                'userid' => $userid,
                'period' => null,
                'courseid' => $courseid,
                'period_type' => RANK_PERIOD_OVERALL
        );
        $recordcount = $DB->count_records ( 'block_mt_ranks_read_posts', $parameters );
        if ($recordcount < 1) {
            $parameters ['percent_read'] = $percentageread;
            $parameters ['num_posts'] = $numberposts;
            $parameters ['num_read'] = $readposts;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->insert_record ( 'block_mt_ranks_read_posts', $parameters );
        } else {
            $parameters ['id'] = $DB->get_field ( 'block_mt_ranks_read_posts', 'id', $parameters );
            $parameters ['percent_read'] = $percentageread;
            $parameters ['num_posts'] = $numberposts;
            $parameters ['num_read'] = $readposts;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->update_record ( 'block_mt_ranks_read_posts', $parameters );
        }
    }
}

/**
 * add or update read posts rankings for a given period
 * @param string $numberposts
 * @param string $percentageread
 * @param string $readposts
 * @param string $userid
 * @param string $period
 * @param string $courseid
 */
function add_update_read_posts_monthly($numberposts, $percentageread, $readposts, $userid, $period, $courseid) {
    global $DB;
    if ($numberposts > 0) {
        $parameters = array (
                'userid' => $userid,
                'period' => $period,
                'courseid' => $courseid,
                'period_type' => RANK_PERIOD_MONTHLY
        );
        $recordcount = $DB->count_records ( 'block_mt_ranks_read_posts', $parameters );
        if ($recordcount < 1) {
            $parameters ['percent_read'] = $percentageread;
            $parameters ['num_posts'] = $numberposts;
            $parameters ['num_read'] = $readposts;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->insert_record ( 'block_mt_ranks_read_posts', $parameters );
        } else {
            $parameters ['id'] = $DB->get_field ( 'block_mt_ranks_read_posts', 'id', $parameters );
            $parameters ['percent_read'] = $percentageread;
            $parameters ['num_posts'] = $numberposts;
            $parameters ['num_read'] = $readposts;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->update_record ( 'block_mt_ranks_read_posts', $parameters );
        }
    }
}

/**
 * add or update rating posts rankings for a given period
 * @param string $ratingpercent
 * @param string $period
 * @param string $courseid
 * @param string $userid
 */
function add_update_rating_posts_monthly($ratingpercent, $period, $courseid, $userid) {
    global $DB;
    if ($ratingpercent > 0) {
        $parameters = array (
                'userid' => $userid,
                'period' => $period,
                'courseid' => $courseid,
                'period_type' => RANK_PERIOD_MONTHLY
        );
        $recordcount = $DB->count_records ( 'block_mt_ranks_rating_posts', $parameters );
        if ($recordcount < 1) {
            $parameters ['rating_percent'] = $ratingpercent;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->insert_record ( 'block_mt_ranks_rating_posts', $parameters );
        } else {
            $parameters ['id'] = $DB->get_field ( 'block_mt_ranks_rating_posts', 'id', $parameters );
            $parameters ['rating_percent'] = $ratingpercent;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->update_record ( 'block_mt_ranks_rating_posts', $parameters );
        }
    }
}

/**
 * add or update rating posts rankings overall
 * @param string $ratingpercent
 * @param string $courseid
 * @param string $userid
 */
function add_update_rating_posts_overall($ratingpercent, $courseid, $userid) {
    global $DB;
    if ($ratingpercent > 0) {
        $parameters = array (
                'userid' => $userid,
                'period' => null,
                'courseid' => $courseid,
                'period_type' => RANK_PERIOD_OVERALL
        );
        $recordcount = $DB->count_records ( 'block_mt_ranks_rating_posts', $parameters );
        if ($recordcount < 1) {
            $parameters ['rating_percent'] = $ratingpercent;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->insert_record ( 'block_mt_ranks_rating_posts', $parameters );
        } else {
            $parameters ['id'] = $DB->get_field ( 'block_mt_ranks_rating_posts', 'id', $parameters );
            $parameters ['rating_percent'] = $ratingpercent;
            $parameters ['active'] = block_mt_is_active($userid, $courseid);
            $DB->update_record ( 'block_mt_ranks_rating_posts', $parameters );
        }
    }
}

/**
 * get read forum posts rankings overall
 * @param string $courseid
 * @return array
 */
function get_read_forum_posts_rankings_overall($courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM (SELECT userid, percent_read
                FROM {block_mt_ranks_read_posts}
                join {user}
                on {user}.id={block_mt_ranks_read_posts}.userid
                WHERE courseid=:courseid
                and {user}.trackforums=1
                AND period_type=:period_type
                ORDER BY percent_read desc) rp,
                (SELECT @curRank := 0) r;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by percent_read desc) as rank
                FROM {block_mt_ranks_read_posts}
                join {user}
                on {user}.id={block_mt_ranks_read_posts}.userid
                WHERE courseid=:courseid
                and {user}.trackforums=1
                AND period_type=:period_type
                ORDER BY percent_read desc;";
            break;
    }
    $rankingsparams = array (
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_OVERALL
    );
    return $DB->get_records_sql($sql, $rankingsparams);
}

/**
 * get active read forum posts rankings overall
 * @param string $courseid
 * @return array
 */
function get_read_forum_posts_rankings_overall_active($courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM (SELECT userid, percent_read
                FROM {block_mt_ranks_read_posts}
                join {user}
                on {user}.id={block_mt_ranks_read_posts}.userid
                WHERE courseid=:courseid
                and {user}.trackforums=1
                AND period_type=:period_type
                AND active=1
                ORDER BY percent_read desc) rp,
                (SELECT @curRank := 0) r;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by percent_read desc) as rank
                FROM {block_mt_ranks_read_posts}
                join {user}
                on {user}.id={block_mt_ranks_read_posts}.userid
                WHERE courseid=:courseid
                and {user}.trackforums=1
                AND period_type=:period_type
                AND active=1
                ORDER BY percent_read desc;";
            break;
    }
    $rankingsparams = array (
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_OVERALL
    );
    return $DB->get_records_sql($sql, $rankingsparams);
}

/**
 * get read forum posts rankings user
 * @param string $userid
 * @param string $courseid
 * @return array
 */
function get_read_forum_posts_rankings_user($userid, $courseid) {
    global $DB;
    $sql = "SELECT COUNT(*) as read_posts
            FROM {forum_read}
            JOIN {forum_posts}
            ON {forum_read}.postid = {forum_posts}.id
            JOIN {forum_discussions}
            ON {forum_discussions}.id={forum_posts}.discussion
            join {user}
            on {user}.id={forum_posts}.userid
            WHERE {forum_discussions}.course=:course
            and {user}.trackforums=1
            AND {forum_read}.userid=:userid";

    $readpostsparams = array (
            'course' => $courseid,
            'userid' => $userid
    );
    return $DB->get_record_sql ( $sql, $readpostsparams )->read_posts;
}

/**
 * get read forum posts rankings monthly
 * @param string $period
 * @param string $courseid
 * @return array
 */
function get_read_forum_posts_rankings_monthly($period, $courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, rp.id as userid, rp.percent_read
            from (
            select {user}.id, {block_mt_ranks_read_posts}.percent_read
            FROM {block_mt_ranks_read_posts}
            join {user}
            on {user}.id={block_mt_ranks_read_posts}.userid
            WHERE period=:period AND courseid=:courseid
            and {user}.trackforums=1
            AND period_type=:period_type
            ORDER BY percent_read desc) rp, (select @curRank:=0) r;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by percent_read desc) as rank
                FROM {block_mt_ranks_read_posts}
                join {user}
                on {user}.id={block_mt_ranks_read_posts}.userid
                WHERE period=:period AND courseid=:courseid
                and {user}.trackforums=1
                AND period_type=:period_type
                ORDER BY percent_read desc;";
            break;
    }

    $rankingsparams = array (
            'period' => $period,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    return $DB->get_records_sql ( $sql, $rankingsparams );
}

/**
 * get active read forum posts rankings monthly
 * @param string $period
 * @param string $courseid
 * @return array
 */
function get_read_forum_posts_rankings_monthly_active($period, $courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, rp.id as userid, rp.percent_read
            from (
            select {user}.id, {block_mt_ranks_read_posts}.percent_read
            FROM {block_mt_ranks_read_posts}
            join {user}
            on {user}.id={block_mt_ranks_read_posts}.userid
            WHERE period=:period AND courseid=:courseid
            and {user}.trackforums=1
            AND period_type=:period_type
            AND active=1
            ORDER BY percent_read desc) rp, (select @curRank:=0) r;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by percent_read desc) as rank
                FROM {block_mt_ranks_read_posts}
                join {user}
                on {user}.id={block_mt_ranks_read_posts}.userid
                WHERE period=:period AND courseid=:courseid
                and {user}.trackforums=1
                AND period_type=:period_type
                AND active=1
                ORDER BY percent_read desc;";
            break;
    }

    $rankingsparams = array (
            'period' => $period,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    return $DB->get_records_sql ( $sql, $rankingsparams );
}

/**
 * get ratings forum posts rankings monthly
 * @param string $period
 * @param string $courseid
 * @return array
 */
function get_ratings_forum_posts_rankings_monthly($period, $courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM {block_mt_ranks_rating_posts} np, (SELECT @curRank := 0) r
                WHERE period=:period AND courseid=:courseid
                AND period_type=:period_type
                ORDER BY rating_percent desc;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by rating_percent desc) as rank
                FROM {block_mt_ranks_rating_posts} np
                WHERE period=:period AND courseid=:courseid
                AND period_type=:period_type
                ORDER BY rating_percent desc;";
            break;
    }
    $rankingsparams = array (
            'period' => $period,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    return $DB->get_records_sql ( $sql, $rankingsparams );
}

/**
 * get active ratings forum posts rankings monthly
 * @param string $period
 * @param string $courseid
 * @return array
 */
function get_ratings_forum_posts_rankings_monthly_active($period, $courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM {block_mt_ranks_rating_posts} np, (SELECT @curRank := 0) r
                WHERE period=:period AND courseid=:courseid
                AND period_type=:period_type
                AND active=1
                ORDER BY rating_percent desc;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by rating_percent desc) as rank
                FROM {block_mt_ranks_rating_posts} np
                WHERE period=:period AND courseid=:courseid
                AND period_type=:period_type
                AND active=1
                ORDER BY rating_percent desc;";
            break;
    }
    $rankingsparams = array (
            'period' => $period,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    return $DB->get_records_sql ( $sql, $rankingsparams );
}

/**
 * get ratings posts rankings overall
 * @param string $courseid
 * @return array
 */
function get_ratings_posts_rankings_overall($courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
            FROM {block_mt_ranks_rating_posts} np, (SELECT @curRank := 0) r
            WHERE courseid=:courseid
            AND period_type=:period_type
            ORDER BY rating_percent DESC;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by rating_percent desc) as rank
                FROM {block_mt_ranks_rating_posts}
                WHERE courseid=:courseid
                AND period_type=:period_type
                ORDER BY rating_percent DESC;";
            break;
    }
    $rankingsparams = array (
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_OVERALL
    );
    return $DB->get_records_sql ( $sql, $rankingsparams );
}

/**
 * get active ratings posts rankings overall
 * @param string $courseid
 * @return array
 */
function get_ratings_posts_rankings_overall_active($courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
            FROM {block_mt_ranks_rating_posts} np, (SELECT @curRank := 0) r
            WHERE courseid=:courseid
            AND period_type=:period_type
            AND active=1
            ORDER BY rating_percent DESC;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by rating_percent desc) as rank
                FROM {block_mt_ranks_rating_posts}
                WHERE courseid=:courseid
                AND period_type=:period_type
                AND active=1
                ORDER BY rating_percent DESC;";
            break;
    }
    $rankingsparams = array (
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_OVERALL
    );
    return $DB->get_records_sql ( $sql, $rankingsparams );
}

/**
 * get ratings scale count
 * @param string $scaleid
 * @return integer
 */
function get_ratings_scale_count($scaleid) {
    global $DB;
    $scaleparams = array (
            'id' => - $scaleid
    );
    $scale = $DB->get_record('scale', $scaleparams, 'scale' )->scale;
    return  substr_count( $scale, ',' ) + 1;
}

/**
 * get read forum posts period
 * @param string $courseid
 * @param string $userid
 * @param string $periodyear
 * @param string $periodmonth
 * @return array
 */
function get_read_forum_posts_period($courseid, $userid, $periodyear, $periodmonth) {
    global $DB, $CFG;
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT COUNT(*) as read_posts
                FROM {forum_read}
                JOIN {forum_posts}
                ON {forum_read}.postid = {forum_posts}.id
                JOIN {forum_discussions}
                ON {forum_discussions}.id={forum_posts}.discussion
                join {user}
                on {user}.id={forum_posts}.userid
                WHERE {forum_discussions}.course=:course
                and {user}.trackforums=1
                AND {forum_read}.userid=:userid
                AND extract (YEAR from to_timestamp(lastread))=:year
                AND extract (month from to_timestamp(lastread))=:month";
            break;
        default :
            $sql = "SELECT COUNT(*) as read_posts
                FROM {forum_read}
                JOIN {forum_posts}
                ON {forum_read}.postid = {forum_posts}.id
                JOIN {forum_discussions}
                ON {forum_discussions}.id={forum_posts}.discussion
                join {user}
                on {user}.id={forum_posts}.userid
                WHERE {forum_discussions}.course=:course
                and {user}.trackforums=1
                AND {forum_read}.userid=:userid
                AND YEAR(FROM_UNIXTIME(lastread))=:year
                AND MONTH(FROM_UNIXTIME(lastread))=:month";
    }

    $readpostsparams = array (
            'course' => $courseid,
            'userid' => $userid,
            'year' => $periodyear,
            'month' => $periodmonth
    );
    return $DB->get_record_sql ( $sql, $readpostsparams )->read_posts;
}

/**
 * get ratings forum posts period
 * @param string $courseid
 * @param string $userid
 * @param string $periodyear
 * @param string $periodmonth
 * @return array
 */
function get_ratings_forum_posts_period($courseid, $userid, $periodyear, $periodmonth) {
    global $DB, $CFG;
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT {rating}.*
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    JOIN {rating}
                    ON {forum_posts}.id={rating}.itemid
                    WHERE course=:course AND {forum_posts}.userid=:userid
                    AND extract (year from to_timestamp(timecreated))=:year
                    AND extract (month from to_timestamp(timecreated))=:month";
            break;
        default :
            $sql = "SELECT {rating}.*
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    JOIN {rating}
                    ON {forum_posts}.id={rating}.itemid
                    WHERE course=:course AND {forum_posts}.userid=:userid
                    AND YEAR(FROM_UNIXTIME(timecreated))=:year
                    AND MONTH(FROM_UNIXTIME(timecreated))=:month";
    }
    $ratingspostsparams = array (
            'course' => $courseid,
            'userid' => $userid,
            'year' => $periodyear,
            'month' => $periodmonth
    );
    return $DB->get_records_sql ( $sql, $ratingspostsparams );
}

/**
 * get ratings forum posts overall
 * @param string $courseid
 * @param string $userid
 * @return array
 */
function get_ratings_forum_posts_overall($courseid, $userid) {
    global $DB;
    $sql = "SELECT {rating}.*
        FROM {forum_posts}
        JOIN {forum_discussions}
        ON {forum_discussions}.id={forum_posts}.discussion
        JOIN {rating}
        ON {forum_posts}.id={rating}.itemid
        WHERE course=:course AND {forum_posts}.userid=:userid";
    $ratingspostsparams = array (
        'course' => $courseid,
        'userid' => $userid
    );
    return $DB->get_records_sql ( $sql, $ratingspostsparams );
}

/**
 * get number of forum posts in a course for a student for a given year and month
 * @param integer $courseid
 * @param integer $userid
 * @param integer $periodyear
 * @param integer $periodmonth
 * @return integer
 */
function get_number_forum_posts_student($courseid, $userid, $periodyear, $periodmonth) {
    global $DB, $CFG;
    if ($CFG->dbtype == DB_TYPE_POSTGRES) {
        $sql = "SELECT count(*) as number_posts
            FROM {forum_posts}
            JOIN {forum_discussions}
            ON {forum_discussions}.id={forum_posts}.discussion
            WHERE course=:course AND {forum_posts}.userid=:userid
            AND extract (YEAR FROM to_timestamp(created))=:year
            AND extract (MONTH FROM to_timestamp(created))=:month";
    } else {
        $sql = "SELECT count(*) as number_posts
            FROM {forum_posts}
            JOIN {forum_discussions}
            ON {forum_discussions}.id={forum_posts}.discussion
            WHERE course=:course AND {forum_posts}.userid=:userid
            AND YEAR(FROM_UNIXTIME(created))=:year
            AND MONTH(FROM_UNIXTIME(created))=:month";
    }
    $numpostsparam = array (
            'course' => $courseid,
            'userid' => $userid,
            'year' => $periodyear,
            'month' => $periodmonth
    );
    return $DB->get_record_sql ( $sql, $numpostsparam )->number_posts;
}

/**
 * get number of forum posts in a course for a given year and month
 * @param integer $courseid
 * @param integer $periodyear
 * @param integer $periodmonth
 * @return integer
 */
function get_number_forum_posts_year_month($courseid, $periodyear, $periodmonth) {
    global $DB, $CFG;
    // Get number of posts for the current year/month.
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT count(*) as number_posts
            FROM {forum_posts}
            JOIN {forum_discussions}
            ON {forum_discussions}.id={forum_posts}.discussion
            join {user}
            on {user}.id={forum_posts}.userid
            WHERE course=:course
            and {user}.trackforums=1
            AND extract (year from to_timestamp(created))=:year
            AND extract (month from to_timestamp(created))=:month";
            break;
        default :
            $sql = "SELECT count(*) as number_posts
            FROM {forum_posts}
            JOIN {forum_discussions}
            ON {forum_discussions}.id={forum_posts}.discussion
            join {user}
            on {user}.id={forum_posts}.userid
            WHERE course=:course
            and {user}.trackforums=1
            AND YEAR(FROM_UNIXTIME(created))=:year
            AND MONTH(FROM_UNIXTIME(created))=:month";
    }
    $numberpostsparams = array (
            'course' => $courseid,
            'year' => $periodyear,
            'month' => $periodmonth
    );
    return $DB->get_record_sql ( $sql, $numberpostsparams )->number_posts;
}

/**
 * get average forum posts rankings in a course
 * @param string $courseid
 * @return array
 */
function get_average_forum_posts_overall_rankings($courseid) {
    global $DB, $CFG;

    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT  userid, average_posts_month, rank() over (order by average_posts_month desc)
        FROM (
            SELECT  o_t.userid as userid, SUM(num_posts) as total_num_posts, m_e.num_months,
            SUM(num_posts)/m_e.num_months::float as average_posts_month, m_e.active
        FROM {block_mt_ranks_num_posts} o_t
        JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, 12 * ((extract (year from CURRENT_DATE)
            - (extract (YEAR from to_timestamp(timestart))))
            + (extract (month from CURRENT_DATE)
            - extract (MONTH from to_timestamp(timestart))) + 1) AS num_months,
            active
            FROM {user_enrolments}
            JOIN {enrol}
            ON {enrol}.id={user_enrolments}.enrolid
            JOIN {block_mt_active_users}
            ON {user_enrolments}.userid={block_mt_active_users}.userid
            AND {enrol}.courseid={block_mt_active_users}.courseid
        ) m_e
        ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
        WHERE o_t.courseid=:courseid
        GROUP BY o_t.userid,  m_e.num_months, m_e.active
        ORDER BY average_posts_month desc
        ) t";
            break;
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT  @curRank := @curRank + 1 AS rank, average_posts_month, userid
               FROM (
                SELECT  o_t.userid as userid, SUM(num_posts) as total_num_posts, m_e.num_months,
                    SUM(num_posts)/m_e.num_months as average_posts_month, m_e.active
                FROM {block_mt_ranks_num_posts} o_t
                JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, 12 * (YEAR(CURRENT_DATE)
                    - YEAR(FROM_UNIXTIME(timestart)))
                    + (MONTH(CURRENT_DATE)
                    - MONTH(FROM_UNIXTIME(timestart))) + 1 AS num_months,
                    active
                    FROM {user_enrolments}
                    JOIN {enrol}
                    ON {enrol}.id={user_enrolments}.enrolid
                    JOIN {block_mt_active_users}
                    ON {user_enrolments}.userid={block_mt_active_users}.userid
                    AND {enrol}.courseid={block_mt_active_users}.courseid
                ) m_e
                ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
                WHERE o_t.courseid=:courseid
                GROUP BY userid
                ORDER BY average_posts_month DESC
                ) t , (SELECT @curRank := 0) r";
            break;
        default :
            $sql = "SELECT userid, average_posts_month, rank() over (order by average_posts_month desc) as rank
                FROM (
                SELECT  o_t.userid as userid, SUM(num_posts) as total_num_posts, m_e.num_months,
                SUM(num_posts)/m_e.num_months as average_posts_month, block_mt_active_users.active
                FROM {block_mt_ranks_num_posts} o_t
                JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, 12 * (YEAR(CURRENT_DATE)
                - YEAR(FROM_UNIXTIME(timestart)))
                + (MONTH(CURRENT_DATE)
                - MONTH(FROM_UNIXTIME(timestart))) + 1 AS num_months, block_mt_active_users.active
                FROM {user_enrolments}
                JOIN {enrol}
                ON {enrol}.id={user_enrolments}.enrolid
                JOIN {block_mt_active_users}
                ON {user_enrolments}.userid={block_mt_active_users}.userid
                AND {enrol}.courseid={block_mt_active_users}.courseid
                ) m_e
                ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
                WHERE o_t.courseid=:courseid
                GROUP BY userid
                ORDER BY average_posts_month DESC
                ) t";
    }

    $rankingsparams = array (
            'courseid' => $courseid
    );
    return $DB->get_records_sql ($sql, $rankingsparams);
}

/**
 * get average forum posts rankings in a course
 * @param string $courseid
 * @return array
 */
function get_average_forum_posts_overall_rankings_active($courseid) {
    global $DB, $CFG;

    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT  userid, average_posts_month, rank() over (order by average_posts_month desc)
        FROM (
            SELECT  o_t.userid as userid, SUM(num_posts) as total_num_posts, m_e.num_months,
            SUM(num_posts)/m_e.num_months::float as average_posts_month, m_e.active
        FROM {block_mt_ranks_num_posts} o_t
        JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, 12 * ((extract (year from CURRENT_DATE)
            - (extract (YEAR from to_timestamp(timestart))))
            + (extract (month from CURRENT_DATE)
            - extract (MONTH from to_timestamp(timestart))) + 1) AS num_months,
            active
            FROM {user_enrolments}
            JOIN {enrol}
            ON {enrol}.id={user_enrolments}.enrolid
            JOIN {block_mt_active_users}
            ON {user_enrolments}.userid={block_mt_active_users}.userid
            AND {enrol}.courseid={block_mt_active_users}.courseid
        ) m_e
        ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
        WHERE o_t.courseid=:courseid
        AND m_e.active=1
        GROUP BY o_t.userid,  m_e.num_months, m_e.active
        ORDER BY average_posts_month desc
        ) t";
            break;
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT  @curRank := @curRank + 1 AS rank, average_posts_month, userid
               FROM (
                SELECT  o_t.userid as userid, SUM(num_posts) as total_num_posts, m_e.num_months,
                    SUM(num_posts)/m_e.num_months as average_posts_month, m_e.active
                FROM {block_mt_ranks_num_posts} o_t
                JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, 12 * (YEAR(CURRENT_DATE)
                    - YEAR(FROM_UNIXTIME(timestart)))
                    + (MONTH(CURRENT_DATE)
                    - MONTH(FROM_UNIXTIME(timestart))) + 1 AS num_months,
                    m_e.active
                    FROM {user_enrolments}
                    JOIN {enrol}
                    ON {enrol}.id={user_enrolments}.enrolid
                    JOIN {block_mt_active_users}
                    ON {user_enrolments}.userid={block_mt_active_users}.userid
                    AND {enrol}.courseid={block_mt_active_users}.courseid
                ) m_e
                ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
                WHERE o_t.courseid=:courseid
                AND m_e.active=1
                GROUP BY userid
                ORDER BY average_posts_month DESC
                ) t , (SELECT @curRank := 0) r";
            break;
        default :
            $sql = "SELECT userid, average_posts_month, rank() over (order by average_posts_month desc) as rank
                FROM (
                SELECT  o_t.userid as userid, SUM(num_posts) as total_num_posts, m_e.num_months,
                SUM(num_posts)/m_e.num_months as average_posts_month, block_mt_active_users.active
                FROM {block_mt_ranks_num_posts} o_t
                JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, 12 * (YEAR(CURRENT_DATE)
                - YEAR(FROM_UNIXTIME(timestart)))
                + (MONTH(CURRENT_DATE)
                - MONTH(FROM_UNIXTIME(timestart))) + 1 AS num_months, block_mt_active_users.active
                FROM {user_enrolments}
                JOIN {enrol}
                ON {enrol}.id={user_enrolments}.enrolid
                JOIN {block_mt_active_users}
                ON {user_enrolments}.userid={block_mt_active_users}.userid
                AND {enrol}.courseid={block_mt_active_users}.courseid
                ) m_e
                ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
                WHERE o_t.courseid=:courseid
                GROUP BY userid
                ORDER BY average_posts_month DESC
                ) t";
    }

    $rankingsparams = array (
            'courseid' => $courseid
    );
    return $DB->get_records_sql ($sql, $rankingsparams);
}

/**
 * get number of active forum posts rankings in a course
 * @param string $period
 * @param string $courseid
 * @return array
 */
function get_number_forum_posts_rankings_monthly_active($period, $courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
            FROM {block_mt_ranks_num_posts} np, (SELECT @curRank := 0) r
            WHERE period=:period AND courseid=:courseid
            AND period_type=:period_type
            AND active=1
            ORDER BY num_posts desc;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by num_posts desc) as rank
                FROM {block_mt_ranks_num_posts}
                WHERE period=:period AND courseid=:courseid
                AND period_type=:period_type
                AND active=1
                ORDER BY num_posts desc;";
            break;
    }
    $forumpostsparams = array (
            'period' => $period,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    return $DB->get_records_sql ( $sql,  $forumpostsparams);
}

/**
 * get number of forum posts rankings in a course
 * @param string $period
 * @param string $courseid
 * @return array
 */
function get_number_forum_posts_rankings_monthly($period, $courseid) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
            FROM {block_mt_ranks_num_posts} np, (SELECT @curRank := 0) r
            WHERE period=:period AND courseid=:courseid
            AND period_type=:period_type
            ORDER BY num_posts desc;";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by num_posts desc) as rank
                FROM {block_mt_ranks_num_posts}
                WHERE period=:period AND courseid=:courseid
                AND period_type=:period_type
                ORDER BY num_posts desc;";
            break;
    }
    $forumpostsparams = array (
            'period' => $period,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    return $DB->get_records_sql ( $sql,  $forumpostsparams);
}

/**
 * get number of forum posts in a course
 * @param integer $courseid
 * @return integer
 */
function get_number_forum_posts($courseid) {
    global $DB;
    $sql = "SELECT count(*) AS number_posts
            FROM {forum_posts}
            JOIN {forum_discussions}
            ON {forum_discussions}.id={forum_posts}.discussion
            join {user}
            on {user}.id={forum_posts}.userid
            WHERE course=:course
            and {user}.trackforums=1";

    $numberpostsparams = array (
            'course' => $courseid
    );
    return $DB->get_record_sql ( $sql, $numberpostsparams )->number_posts;
}

/**
 * get number of forum posts in a course
 * @param string $courseid
 * @return string
 */
function get_all_years_tracking_forum_posts($courseid) {
    global $DB, $CFG;
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT extract (YEAR FROM to_timestamp (created)) AS year
                FROM {forum_posts}
                JOIN {forum_discussions}
                ON {forum_discussions}.id = {forum_posts}.discussion
                join {user}
                on {user}.id={forum_posts}.userid
                WHERE {forum_discussions}.course = :course
                and {user}.trackforums=1
                GROUP BY year";
            break;
        default :
            $sql = "SELECT YEAR(FROM_UNIXTIME(created)) AS year
                FROM {forum_posts}
                JOIN {forum_discussions}
                ON {forum_discussions}.id={forum_posts}.discussion
                join {user}
                on {user}.id={forum_posts}.userid
                WHERE course=:course
                and {user}.trackforums=1
                GROUP BY year";
    }
    $periodyearsparams = array (
            'course' => $courseid
    );
    return $DB->get_records_sql ( $sql, $periodyearsparams );
}

/**
 * get number of forum posts in a course
 * @param string $courseid
 * @return string
 */
function get_all_years_forum_posts($courseid) {
    global $DB, $CFG;
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT extract (YEAR FROM to_timestamp(created)) AS year
                FROM {forum_posts}
                JOIN {forum_discussions}
                ON {forum_discussions}.id={forum_posts}.discussion
                WHERE course=:course
                GROUP BY year";
            break;
        default :
            $sql = "SELECT YEAR(FROM_UNIXTIME(created)) AS year
                FROM {forum_posts}
                JOIN {forum_discussions}
                ON {forum_discussions}.id={forum_posts}.discussion
                WHERE course=:course
                GROUP BY year";
    }
    $periodyearsparams = array (
            'course' => $courseid
    );
    return $DB->get_records_sql ( $sql, $periodyearsparams);
}

/**
 * get number of forum posts in a course
 * @param string $courseid
 * @param string $year
 * @return string
 */
function get_year_forum_posts($courseid, $year) {
    global $DB, $CFG;
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT extract (YEAR FROM to_timestamp(created)) AS year
                FROM {forum_posts}
                JOIN {forum_discussions}
                ON {forum_discussions}.id={forum_posts}.discussion
                WHERE course=:course and extract (YEAR FROM to_timestamp(created))=:lastyear
                GROUP BY year";
            break;
        default :
            $sql = "SELECT YEAR(FROM_UNIXTIME(created)) AS year
                FROM {forum_posts}
                JOIN {forum_discussions}
                ON {forum_discussions}.id={forum_posts}.discussion
                WHERE course=:course and YEAR(FROM_UNIXTIME(created))=:lastyear
                GROUP BY year";
    }
    $periodyearsparams = array (
            'course' => $courseid,
            'lastyear' => $year
    );
    return $DB->get_records_sql ( $sql, $periodyearsparams);
}

/**
 * get number of forum posts in a course
 * @param string $courseid
 * @param string $year
 * @return string
 */
function get_all_months_forum_posts($courseid, $year) {
    global $DB, $CFG;
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT EXTRACT (MONTH FROM to_timestamp(created)) AS month,
                    EXTRACT (YEAR FROM to_timestamp(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    WHERE course=:course and EXTRACT (YEAR FROM to_timestamp(created))=:year
                    GROUP BY month, year";
            break;
        default :
            $sql = "SELECT MONTH(FROM_UNIXTIME(created)) AS month,
                    YEAR(FROM_UNIXTIME(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    WHERE course=:course and YEAR(FROM_UNIXTIME(created))=:year
                    GROUP BY month, year";
    }
    $periodmonthsparams = array (
            'course' => $courseid,
            'year' => $year
    );
    return $DB->get_records_sql ( $sql, $periodmonthsparams );
}

/**
 * get number of forum posts in a course
 * @param string $courseid
 * @param string $year
 * @param string $month
 * @return string
 */
function get_month_year_forum_posts($courseid, $year, $month) {
    global $DB, $CFG;
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT extract (MONTH FROM to_timestamp(created)) AS month,
                    extract (YEAR FROM to_timestamp(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    WHERE course=:course and extract (YEAR FROM to_timestamp(created))=:year
                    AND extract (MONTH FROM to_timestamp(created))>=:month
                    GROUP BY month, year";
            break;
        default :
            $sql = "SELECT MONTH(FROM_UNIXTIME(created)) AS month,
                    YEAR(FROM_UNIXTIME(created)) AS year
                    FROM {forum_posts}
                    JOIN {forum_discussions}
                    ON {forum_discussions}.id={forum_posts}.discussion
                    WHERE course=:course and YEAR(FROM_UNIXTIME(created))=:year
                    AND MONTH(FROM_UNIXTIME(created))>=:month
                    GROUP BY month, year";
    }
    $periodmonthsparam = array (
                'course' => $courseid,
                'year' => $year,
                'month' => $month
        );
    return $DB->get_records_sql($sql, $periodmonthsparam);
}

/**
 * get period years
 * @param string $courseid
 * @param string $lastperiodyear
 * @return string
 */
function get_period_years_ranks($courseid, $lastperiodyear) {
    global $DB, $CFG;

    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT extract (YEAR FROM to_timestamp(created)) AS year
                FROM {forum_posts}
                JOIN {forum_discussions}
                ON {forum_discussions}.id={forum_posts}.discussion
                join {user}
                on {user}.id={forum_posts}.userid
                WHERE course=:course and extract (YEAR FROM to_timestamp(created))=:year
                and {user}.trackforums=1
                GROUP BY year";
            break;
        default :
            $sql = "SELECT YEAR(FROM_UNIXTIME(created)) AS year
                FROM {forum_posts}
                JOIN {forum_discussions}
                ON {forum_discussions}.id={forum_posts}.discussion
                join {user}
                on {user}.id={forum_posts}.userid
                WHERE course=:course and YEAR(FROM_UNIXTIME(created))=:year
                and {user}.trackforums=1
                GROUP BY year";
    }
    $params = array (
        'course' => $courseid,
        'year' => $lastperiodyear
    );
    return $DB->get_records_sql($sql, $params);
}