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
 * This generates the online time rankings
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This calculates the online time given a set of times
 * @param array $onlinetimedata
 * @return integer
 */
function calculate_online_time($onlinetimedata) {
    global $CFG;

    if (null !== get_config("block_mt", "ranks_onl_time")) {
        $onlinetimecutoff = get_config("block_mt", "ranks_onl_time") * 60;
    } else {
        $onlinetimecutoff = intval (get_string('mt_rankings:online_time_value', 'block_mt')) * 60;
    }
    $onlinetime = 0;
    $previoustime = 0;
    $timedifference = 0;
    foreach ($onlinetimedata as $onlinetimerecord) {
        if ($previoustime == 0) {
            $previoustime = $onlinetimerecord->timecreated;
        }
        $timedifference = $onlinetimerecord->timecreated - $previoustime;
        if ($timedifference > $onlinetimecutoff) {
            $onlinetime = $onlinetime + $onlinetimecutoff;
        } else {
            $onlinetime = $onlinetime + $timedifference;
        }
        $previoustime = $onlinetimerecord->timecreated;
    }
    return $onlinetime;
}

/**
 * This gets the online time data
 * @param string $courseid
 * @param string $userid
 * @param string $periodyear
 * @param string $periodmonth
 * @return array
 */
function get_online_time_data($courseid, $userid, $periodyear, $periodmonth) {
    global $CFG, $DB;
    if ($CFG->dbtype == DB_TYPE_POSTGRES) {
        $sql = "SELECT id, timecreated
            FROM {logstore_standard_log}
            WHERE courseid=:course and userid=:userid
            AND EXTRACT (YEAR FROM to_timestamp(timecreated))=:year
            AND EXTRACT (MONTH FROM to_timestamp(timecreated))=:month
            AND component NOT LIKE 'mt_%'
            ORDER BY timecreated";
    } else {
        $sql = "SELECT id, timecreated
            FROM {logstore_standard_log}
            WHERE courseid=:course and userid=:userid
            AND YEAR(FROM_UNIXTIME(timecreated))=:year
            AND MONTH(FROM_UNIXTIME(timecreated))=:month
            AND component NOT LIKE 'mt_%'
            ORDER BY timecreated";
    }
    $onlinetimedataparams = array (
            'course' => $courseid,
            'userid' => $userid,
            'year' => $periodyear,
            'month' => $periodmonth
    );
    return $DB->get_records_sql($sql, $onlinetimedataparams);
}

/**
 * This generates the ranks active for online time for a specific period
 * @param string $courseid
 * @param string $userranking
 * @param string $periodparam
 */
function generate_ranks_online_time_active($courseid, $userranking, $periodparam) {
    $rankings = get_active_online_rankings_by_period($courseid, $periodparam->period);
    foreach ($rankings as $ranking) {
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = $periodparam->period;
        $userranking->rank_type_id = RANK_TYPE_ONLINE_TIME;
        $userranking->rankname = get_string('mt_rankings:generate_rank_online_time', 'block_mt', $periodparam);
        $userranking->period_type = RANK_PERIOD_MONTHLY;
        $userranking->rankactive = $ranking->rank;
        active_ranks_process_entry($userranking);
    }
}

/**
 * This generates the ranks for online time for a specific period
 * @param string $courseid
 * @param string $userranking
 * @param string $periodparam
 */
function generate_ranks_online_time($courseid, $userranking, $periodparam) {
    $onlineusers = block_mt_students_in_course($courseid);
    foreach ($onlineusers as $onlineuser) {
        $onlinetimedata = get_online_time_data($courseid, $onlineuser->userid, $periodparam->year, $periodparam->month);
        $onlinetime = calculate_online_time($onlinetimedata);
        add_update_online_time_by_user($onlineuser->userid, $periodparam->period, $courseid, $onlinetime);
    }
    $rankings = get_online_rankings_by_period($courseid, $periodparam->period);
    foreach ($rankings as $ranking) {
        $userranking->rank = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = $periodparam->period;
        $userranking->rank_type_id = RANK_TYPE_ONLINE_TIME;
        $userranking->rankname = get_string('mt_rankings:generate_rank_online_time', 'block_mt', $periodparam);
        $userranking->period_type = RANK_PERIOD_MONTHLY;
        ranks_process_entry ( $userranking );
    }
}

/**
 * This gets the  online time rankings for active users by period for a course
 * @param string $courseid
 * @param string $period
 * @return array
 */
function get_active_online_rankings_by_period($courseid, $period) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM {block_mt_ranks_onl_time} t, (SELECT @curRank := 0) r
                WHERE period=:period
                AND courseid=:courseid
                AND onlinetime > 0
                AND active=1
                ORDER BY onlinetime DESC";
            break;
        default :
            $sql = "SELECT  userid, rank() over (order by onlinetime DESC) as rank
                FROM {block_mt_ranks_onl_time}
                WHERE period=:period
                AND courseid=:courseid
                AND onlinetime > 0
                AND active=1
                ORDER BY onlinetime DESC";
            break;
    }
    $rankingsparams = array (
            'period' => $period,
            'courseid' => $courseid
    );
    return $DB->get_records_sql ( $sql, $rankingsparams );
}

/**
 * This gets the active overall online time rankings for a course
 * @param string $courseid
 * @return array
 */
function get_active_online_rankings_overall($courseid) {
    global $DB, $CFG;

    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = 'SELECT  userid, average_time, rank() over (order by average_time desc) as rank
            FROM (
            SELECT  {block_mt_ranks_onl_time}.userid, SUM(onlinetime/3600::float) AS total_online_time,
            m_e.months_enrolled,
            SUM(onlinetime/3600::float)/m_e.months_enrolled::float AS average_time
            FROM {block_mt_ranks_onl_time}
            JOIN (SELECT {user_enrolments}.userid, courseid,
                12 * (extract (year from (CURRENT_DATE)) -extract (year from to_timestamp(timestart)))
                + extract (month from current_date) - extract(month from to_timestamp(timestart)) + 1 as months_enrolled
                FROM {user_enrolments}
                 JOIN {enrol}
                ON {enrol}.id={user_enrolments}.enrolid
            ) m_e
            ON {block_mt_ranks_onl_time}.userid=m_e.userid AND {block_mt_ranks_onl_time}.courseid=m_e.courseid
            WHERE {block_mt_ranks_onl_time}.courseid=:courseid AND onlinetime >0
            AND {block_mt_ranks_onl_time}.active=1
            GROUP BY {block_mt_ranks_onl_time}.userid,  m_e.months_enrolled
            ORDER BY average_time DESC
            ) as t';
            break;
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = 'SELECT  @curRank := @curRank + 1 AS rank, average_time, userid
                FROM (
                    SELECT  o_t.userid, SUM(onlinetime/3600) AS total_online_time, m_e.months_enrolled,
                    SUM(onlinetime/3600)/m_e.months_enrolled AS average_time
                    FROM {block_mt_ranks_onl_time} o_t
                    JOIN (SELECT userid, courseid, 12 * (YEAR(CURRENT_DATE)
                        - YEAR(FROM_UNIXTIME(timestart)))
                        + (MONTH(CURRENT_DATE)
                        - MONTH(FROM_UNIXTIME(timestart))) + 1 AS months_enrolled
                        FROM {user_enrolments}
                         JOIN {enrol}
                        ON {enrol}.id={user_enrolments}.enrolid
                    ) m_e
                    ON o_t.userid=m_e.userid AND o_t.courseid=m_e.courseid
                    WHERE o_t.courseid=:courseid AND onlinetime >0
                    AND o_t.active=1
                    GROUP BY userid
                    ORDER BY average_time DESC
                ) t , (SELECT @curRank := 0) r';
            break;
        default :
            $sql = 'SELECT  userid, average_time, rank() over (order by average_time desc) as rank
            FROM (
            SELECT  o_t.userid, SUM(onlinetime/3600) AS total_online_time, m_e.months_enrolled,
            SUM(onlinetime/3600)/m_e.months_enrolled AS average_time
            FROM {block_mt_ranks_onl_time} o_t
            JOIN (SELECT userid, courseid, 12 * (YEAR(CURRENT_DATE)
                - YEAR(FROM_UNIXTIME(timestart)))
                + (MONTH(CURRENT_DATE)
                - MONTH(FROM_UNIXTIME(timestart))) + 1 AS months_enrolled
                FROM {user_enrolments}
                 JOIN {enrol}
                ON {enrol}.id={user_enrolments}.enrolid
            ) m_e
            ON o_t.userid=m_e.userid AND o_t.courseid=m_e.courseid
            WHERE o_t.courseid=:courseid AND onlinetime >0
            AND {block_mt_ranks_onl_time}.active=1
            GROUP BY userid
            ORDER BY average_time DESC
            ) t ';
    }
    $rankingsparams = array (
            'courseid' => $courseid
    );
    return $DB->get_records_sql($sql, $rankingsparams);
}

/**
 * This gets the active overall online time rankings for a course
 * @param string $courseid
 * @return array
 */
function get_online_rankings_overall($courseid) {
    global $DB, $CFG;
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = 'SELECT  userid, average_time, rank() over (order by average_time desc) as rank
            FROM (
            SELECT  {block_mt_ranks_onl_time}.userid, SUM(onlinetime/3600::float) AS total_online_time,
            m_e.months_enrolled,
            SUM(onlinetime/3600::float)/m_e.months_enrolled::float AS average_time
            FROM {block_mt_ranks_onl_time}
            JOIN (SELECT {user_enrolments}.userid, courseid,
                12 * (extract (year from (CURRENT_DATE)) -extract (year from to_timestamp(timestart)))
                + extract (month from current_date) - extract(month from to_timestamp(timestart)) + 1 as months_enrolled
                FROM {user_enrolments}
                 JOIN {enrol}
                ON {enrol}.id={user_enrolments}.enrolid
            ) m_e
            ON {block_mt_ranks_onl_time}.userid=m_e.userid AND {block_mt_ranks_onl_time}.courseid=m_e.courseid
            WHERE {block_mt_ranks_onl_time}.courseid=:courseid AND onlinetime >0
            GROUP BY {block_mt_ranks_onl_time}.userid,  m_e.months_enrolled
            ORDER BY average_time DESC
            ) as t';
            break;
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = 'SELECT  @curRank := @curRank + 1 AS rank, average_time, userid
                FROM (
                    SELECT  o_t.userid, SUM(onlinetime/3600) AS total_online_time, m_e.months_enrolled,
                    SUM(onlinetime/3600)/m_e.months_enrolled AS average_time
                    FROM {block_mt_ranks_onl_time} o_t
                    JOIN (SELECT userid, courseid, 12 * (YEAR(CURRENT_DATE)
                        - YEAR(FROM_UNIXTIME(timestart)))
                        + (MONTH(CURRENT_DATE)
                        - MONTH(FROM_UNIXTIME(timestart))) + 1 AS months_enrolled
                        FROM {user_enrolments}
                         JOIN {enrol}
                        ON {enrol}.id={user_enrolments}.enrolid
                    ) m_e
                    ON o_t.userid=m_e.userid AND o_t.courseid=m_e.courseid
                    WHERE o_t.courseid=:courseid AND onlinetime >0
                    GROUP BY userid
                    ORDER BY average_time DESC
                ) t , (SELECT @curRank := 0) r';
            break;
        default :
            $sql = 'SELECT  userid, average_time, rank() over (order by average_time desc) as rank
            FROM (
            SELECT  o_t.userid, SUM(onlinetime/3600) AS total_online_time, m_e.months_enrolled,
            SUM(onlinetime/3600)/m_e.months_enrolled AS average_time
            FROM {block_mt_ranks_onl_time} o_t
            JOIN (SELECT userid, courseid, 12 * (YEAR(CURRENT_DATE)
                - YEAR(FROM_UNIXTIME(timestart)))
                + (MONTH(CURRENT_DATE)
                - MONTH(FROM_UNIXTIME(timestart))) + 1 AS months_enrolled
                FROM {user_enrolments}
                 JOIN {enrol}
                ON {enrol}.id={user_enrolments}.enrolid
            ) m_e
            ON o_t.userid=m_e.userid AND o_t.courseid=m_e.courseid
            WHERE o_t.courseid=:courseid AND onlinetime >0
            GROUP BY userid
            ORDER BY average_time DESC
            ) t ';
    }
    $rankingsparams = array (
            'courseid' => $courseid
    );
    return $DB->get_records_sql($sql, $rankingsparams);
}

/**
 * This gets the online time rankings by period for a course
 * @param string $courseid
 * @param string $period
 * @return array
 */
function get_online_rankings_by_period($courseid, $period) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM {block_mt_ranks_onl_time} t, (SELECT @curRank := 0) r
                WHERE period=:period
                AND courseid=:courseid
                AND onlinetime > 0
                ORDER BY onlinetime DESC";
            break;
        default :
            $sql = "SELECT  userid, rank() over (order by onlinetime DESC) as rank
                FROM {block_mt_ranks_onl_time}
                WHERE period=:period
                AND courseid=:courseid
                AND onlinetime > 0
                ORDER BY onlinetime DESC";
            break;
    }
    $rankingsparams = array (
            'period' => $period,
            'courseid' => $courseid
    );
    return $DB->get_records_sql ($sql, $rankingsparams);
}

/**
 * This gets the period years for a course
 * @param string $courseid
 * @return array
 */
function get_period_years_course($courseid) {
    global $DB, $CFG;

    if (has_no_last_period_run_ranks($courseid, RANK_TYPE_ONLINE_TIME)) {
        if ($CFG->dbtype == DB_TYPE_POSTGRES) {
            $sql = "SELECT EXTRACT (YEAR FROM to_timestamp(timecreated)) AS period_year
                FROM {logstore_standard_log}
                WHERE courseid=:course
                AND component NOT LIKE 'mt_%'
                GROUP BY period_year";
        } else {
            $sql = "SELECT YEAR(FROM_UNIXTIME(timecreated)) AS period_year
                FROM {logstore_standard_log}
                WHERE courseid=:course
                AND component NOT LIKE 'mt_%'
                GROUP BY period_year";
        }
        $periodyearsparams = array (
                'course' => $courseid
        );
        $periodyears = $DB->get_records_sql ( $sql, $periodyearsparams );
    } else {
        if ($CFG->dbtype == DB_TYPE_POSTGRES) {
            $sql = "SELECT EXTRACT (YEAR FROM to_timestamp(timecreated)) AS period_year
                FROM {logstore_standard_log}
                WHERE courseid=:course AND EXTRACT (YEAR FROM to_timestamp(timecreated))=:year
                AND component NOT LIKE 'mt_%'
                GROUP BY period_year";
        } else {
            $sql = "SELECT YEAR(FROM_UNIXTIME(timecreated)) AS period_year
                FROM {logstore_standard_log}
                WHERE courseid=:course AND YEAR(FROM_UNIXTIME(timecreated))=:year
                AND component NOT LIKE 'mt_%'
                GROUP BY period_year";
        }
        $lastperiodyear = get_last_period_run_ranks_year($courseid, RANK_TYPE_ONLINE_TIME);
        $periodyears = $DB->get_records_sql ( $sql, array (
                'course' => $courseid,
                'year' => $lastperiodyear
        ));
    }
    return $periodyears;
}

/**
 * This generates the ranks for all online time
 * @param string $courseid
 * @param string $userranking
 */
function generate_ranks_online_time_all($courseid, $userranking) {
    global $DB, $CFG;

    $periodyears = get_period_years_course($courseid);
    foreach ($periodyears as $periodyear) {
        if (has_no_last_period_run_ranks($courseid, RANK_TYPE_ONLINE_TIME)) {
            // Get all period years.
            if ($CFG->dbtype == DB_TYPE_POSTGRES) {
                $sql = "SELECT EXTRACT (MONTH FROM to_timestamp(timecreated)) AS period_month,
                    EXTRACT (YEAR FROM to_timestamp(timecreated)) AS period_year
                    FROM {logstore_standard_log}
                    WHERE courseid=:course and EXTRACT (YEAR FROM to_timestamp(timecreated))=:year
                    AND component NOT LIKE 'mt_%'
                    GROUP BY period_month, period_year";
            } else {
                $sql = "SELECT MONTH(FROM_UNIXTIME(timecreated)) AS period_month,
                    YEAR(FROM_UNIXTIME(timecreated)) AS period_year
                    FROM {logstore_standard_log}
                    WHERE courseid=:course and YEAR(FROM_UNIXTIME(timecreated))=:year
                    AND component NOT LIKE 'mt_%'
                    GROUP BY period_month";
            }
            $periodmonthsparams = array (
                            'course' => $courseid,
                            'year' => $periodyear->period_year
                        );
            $periodmonths = $DB->get_records_sql ( $sql, $periodmonthsparams );
        } else {
            $lastperiodmonth = get_last_period_run_ranks_month($courseid, RANK_TYPE_ONLINE_TIME);
            if ($CFG->dbtype == DB_TYPE_POSTGRES) {
                $sql = "SELECT EXTRACT (MONTH FROM to_timestamp(timecreated)) AS period_month,
                    EXTRACT(YEAR FROM to_timestamp(timecreated)) AS period_year
                    FROM {logstore_standard_log}
                    WHERE courseid=:course and EXTRACT (YEAR FROM to_timestamp(timecreated))=:year
                    AND EXTRACT(MONTH FROM to_timestamp(timecreated))>=:month
                    AND component NOT LIKE 'mt_%'
                    GROUP BY period_month, period_year";
            } else {
                $sql = "SELECT MONTH(FROM_UNIXTIME(timecreated)) AS period_month,
                    YEAR(FROM_UNIXTIME(timecreated)) AS period_year
                    FROM {logstore_standard_log}
                    WHERE courseid=:course and YEAR(FROM_UNIXTIME(timecreated))=:year
                    AND MONTH(FROM_UNIXTIME(timecreated))>=:month
                    AND component NOT LIKE 'mt_%'
                    GROUP BY period_month";
            }
            $periodmonthsparam = array (
                            'course' => $courseid,
                            'year' => $periodyear->period_year,
                            'month' => $lastperiodmonth
                        );
            $periodmonths = $DB->get_records_sql($sql, $periodmonthsparam);
        }
        foreach ($periodmonths as $periodmonth) {
            $period = new stdClass ();
            $period->year = $periodyear->period_year;
            $period->month = $periodmonth->period_month;
            $period->period = get_string('mt_rankings:generate_rank_period', 'block_mt', $period);
            generate_ranks_online_time($courseid, $userranking, $period);
            generate_ranks_online_time_active($courseid, $userranking, $period);
        }
    }
    update_last_period_run_ranks($courseid, RANK_TYPE_ONLINE_TIME);
}

/**
 * This generates the ranks for online time overall
 * @param string $courseid
 * @param string $userranking
 */
function generate_ranks_online_time_overall($courseid, $userranking) {
    $rankings = get_online_rankings_overall($courseid);
    foreach ($rankings as $ranking) {
        $userranking->rank = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_ONLINE_TIME;
        $userranking->rankname = get_string('mt_rankings:generate_rank_online_time_overall', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        ranks_process_entry ( $userranking );
    }
}

/**
 * This generates the active ranks for online time overall
 * @param string $courseid
 * @param string $userranking
 */
function generate_ranks_online_time_overall_active($courseid, $userranking) {
    $rankings = get_active_online_rankings_overall($courseid);
    foreach ($rankings as $ranking) {
        $userranking->rankactive = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = null;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_ONLINE_TIME;
        $userranking->rankname = get_string('mt_rankings:generate_rank_online_time_overall', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        active_ranks_process_entry($userranking);
    }
}

/**
 * This adds or updates the online time by user
 * @param string $userid
 * @param string $period
 * @param string $courseid
 * @param string $onlinetime
 */
function add_update_online_time_by_user($userid, $period, $courseid, $onlinetime) {
    global $DB;
    $parameters = array (
            'userid' => $userid,
            'period' => $period,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    $recordcount = $DB->count_records('block_mt_ranks_onl_time', $parameters);
    if ($recordcount < 1) {
        $parameters ['onlinetime'] = $onlinetime;
        $parameters ['active'] = block_mt_is_active($userid, $courseid);
        $DB->insert_record ( 'block_mt_ranks_onl_time', $parameters );
    } else {
        $parameters ['id'] = $DB->get_field ( 'block_mt_ranks_onl_time', 'id', $parameters );
        $parameters ['onlinetime'] = $onlinetime;
        $parameters ['active'] = block_mt_is_active($userid, $courseid);
        $DB->update_record ( 'block_mt_ranks_onl_time', $parameters );
    }
}