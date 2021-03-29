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
 * This generates the online time awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate online time awards
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_online_time($paramcourseid) {
    mtrace(get_string('mt:cron_awards_time_online', 'block_mt'));
    generate_awards_online_time_monthly($paramcourseid);

    generate_awards_online_time_overall($paramcourseid);
}

/**
 * generate online time monthly awards
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_online_time_monthly($paramcourseid) {
    global $DB;

    // Get unique period values.
    $parameters = array(
        'courseid' => $paramcourseid,
        'rank_type_id' => RANK_TYPE_ONLINE_TIME,
        'period_type' => RANK_PERIOD_MONTHLY
    );
    $periods = $DB->get_records('block_mt_ranks_user', $parameters, '', 'distinct period');

    $lastperiod = array('year' => 0, 'month' => 0);

    foreach ($periods as $period) {

        // Get user rank data for this period.
        $parameters['period'] = $period->period;
        $ranks = $DB->get_records('block_mt_ranks_user', $parameters, 'userid');

        // Determine rank needed to achieve award.
        $totalrecords = count($ranks);
        $goldrank = 1;
        $silverrank = ceil(0.1 * $totalrecords);
        $bronzerank = ceil(0.2 * $totalrecords);

        foreach ($ranks as $rank) {

            // Get user award record for this period.
            $params = array(
                'userid'      => $rank->userid,
                'courseid'    => $paramcourseid,
                'award_name'  => $rank->rankname,
                'period'      => $rank->period,
                'period_type' => RANK_PERIOD_MONTHLY
            );
            $record = $DB->get_record('block_mt_awards_user', $params);

            // Determine if user has achieved an award.
            if ($rank->rank == $goldrank) {
                $params['awardid'] = GOLD_AWARD_ID;
            } else if ($rank->rank <= $silverrank) {
                $params['awardid'] = SILVER_AWARD_ID;
            } else if ($rank->rank <= $bronzerank) {
                $params['awardid'] = BRONZE_AWARD_ID;
            } else {
                continue;
            }

            if (!$record) {
                $DB->insert_record('block_mt_awards_user', $params);
            } else {
                $params['id'] = $record->id;
                $DB->update_record('block_mt_awards_user', $params);
            }
        }

        // Keep latest period value seen.
        $currentperiod = explode('-', $period->period);
        if ($lastperiod['year'] < intval($currentperiod[0])) {
            $lastperiod['year'] = intval($currentperiod[0]);
        }
        if ($lastperiod['month'] < intval($currentperiod[1])) {
            $lastperiod['month'] = intval($currentperiod[1]);
        }
    }

    $period = $lastperiod['year'] . '-' . $lastperiod['month'] . '-01';
    update_last_period_run_awards($paramcourseid, '3', $period);
}

/**
 * generate online time overall awards
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_online_time_overall($paramcourseid) {
    $parameters = array(
        'rank_type_id' => RANK_TYPE_ONLINE_TIME,
        'courseid' => $paramcourseid,
        'period_type' => RANK_PERIOD_OVERALL
    );

    online_time_overall($parameters, 'bronze');
    online_time_overall($parameters, 'silver');
    online_time_overall($parameters, 'gold');
}

/**
 * generate online time overall count awards
 *
 * @param string $courseid
 * @return null
 */
function generate_online_time_overall_count($courseid) {
    global $DB;

    $goldawardweight = block_mt_get_awards_settings('mt_awards:time_online_gold_weight_value', $courseid);
    $silverawardweight = block_mt_get_awards_settings('mt_awards:time_online_silver_weight_value', $courseid);
    $bronzeawardweight = block_mt_get_awards_settings('mt_awards:time_online_bronze_weight_value', $courseid);

    $sql = "SELECT userid
        FROM {block_mt_awards_user}
        WHERE courseid=:courseid
        AND award_name like 'Online time%'
        GROUP BY userid
        ORDER by userid";
    $studentlist = $DB->get_records_sql($sql, array(
        'courseid' => $courseid
    ));
    foreach ($studentlist as $student) {
        $awardtotal = 0;
        $sql = "SELECT *
            FROM {block_mt_awards_user}
            WHERE courseid=:courseid
            AND award_name like 'Online time%'
            AND userid=:userid";
        $awardlist = $DB->get_records_sql($sql, array(
            'courseid' => $courseid,
            'userid' => $student->userid
        ));

        foreach ($awardlist as $award) {
            $awardperiod = date_create($award->period);
            $currentdate = block_mt_get_current_date();
            $interval = date_diff($currentdate, $awardperiod);
            // Only take the last year.
            if ($interval->format('%y') < 1) {
                switch ($award->awardid) {
                    case GOLD_AWARD_ID:
                        $awardtotal = $awardtotal + $goldawardweight;
                        break;
                    case SILVER_AWARD_ID:
                        $awardtotal = $awardtotal + $silverawardweight;
                        break;
                    case BRONZE_AWARD_ID:
                        $awardtotal = $awardtotal + $bronzeawardweight;
                        break;
                    default:
                        break;
                }
            }
        }
        $recordcountparam = array(
            'courseid' => $courseid,
            'userid' => $student->userid,
            'awardtype' => RANK_TYPE_ONLINE_TIME
        );
        $recordcount = $DB->count_records('block_mt_awards_count_all', $recordcountparam);
        if ($recordcount < 1) {
            $recordcountparam['awardtotal'] = $awardtotal;
            $DB->insert_record('block_mt_awards_count_all', $recordcountparam);
        } else {
            $updateid = $DB->get_field('block_mt_awards_count_all', 'id', $recordcountparam);
            $recordcountparam['awardtotal'] = $awardtotal;
            $recordcountparam['id'] = $updateid;
            $DB->update_record('block_mt_awards_count_all', $recordcountparam);
        }
    }
}

/**
 * generate online time awards overall
 *
 * @param array $parameters
 * @param string $award
 * @return null
 */
function online_time_overall($parameters, $award) {
    global $DB;
    $parameters['period'] = null;
    $totalrecords = $DB->count_records('block_mt_ranks_user', $parameters);
    switch ($award) {
        case 'gold':
            // Gold = top 1.
            $parameters['rank'] = '1';
            break;
        case 'silver':
            // Silver = top 10%.
            $numrecords = ceil(0.1 * $totalrecords);
            $parameters['rank'] = $numrecords;
            break;
        case 'bronze':
            // Bronze = top 20%.
            $numrecords = ceil(0.2 * $totalrecords);
            $parameters['rank'] = $numrecords;
            break;
        default:
            $parameters['rank'] = '1';
            break;
    }
    $recordcount = $DB->count_records('block_mt_ranks_user', $parameters);
    if ($recordcount > 0) {
        $monthlyresult = $DB->get_records('block_mt_ranks_user', $parameters);
        $parameters['awardid'] = get_award_id($award);
        foreach ($monthlyresult as $result) {
            $parameters['award_name'] = $result->rankname;
            $parameters['userid'] = $result->userid;
            $parameters['itemid'] = null;
            $recordcountparam = array(
                'userid' => $parameters['userid'],
                'courseid' => $parameters['courseid'],
                'award_name' => $parameters['award_name'],
                'period' => $parameters['period'],
                'period_type' => RANK_PERIOD_OVERALL
            );
            $recordcount = $DB->count_records('block_mt_awards_user', $recordcountparam);
            if ($recordcount < 1) {
                $DB->insert_record('block_mt_awards_user', $parameters);
            } else {
                $updateid = $DB->get_field('block_mt_awards_user', 'id', $recordcountparam);
                $recordcountparam['id'] = $updateid;
                $recordcountparam['awardid'] = $parameters['awardid'];
                $DB->update_record('block_mt_awards_user', $recordcountparam);
            }
        }
    }
}

/**
 * This gets the period years for a course
 * @param string $courseid
 * @return array
 */
function get_period_years_awards($courseid) {
    global $DB, $CFG;
    if (has_no_last_period_run_awards($courseid, RANK_TYPE_ONLINE_TIME)) {
        if ($CFG->dbtype == DB_TYPE_POSTGRES) {
            $sql = "SELECT extract (year from to_timestamp(timecreated)) AS period_year
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
        $periodyears = $DB->get_records_sql($sql, array(
                'course' => $courseid
        ));
    } else {
        if ($CFG->dbtype == DB_TYPE_POSTGRES) {
            $sql = "SELECT extract (year from to_timestamp(timecreated)) AS period_year
                    FROM {logstore_standard_log}
                    WHERE courseid=:course and extract (year from to_timestamp(timecreated))=:year
                    AND component NOT LIKE 'mt_%'
                    GROUP BY period_year";
        } else {
            $sql = "SELECT YEAR(FROM_UNIXTIME(timecreated)) AS period_year
                    FROM {logstore_standard_log}
                    WHERE courseid=:course and YEAR(FROM_UNIXTIME(timecreated))=:year
                    AND component NOT LIKE 'mt_%'
                    GROUP BY period_year";
        }
        $lastperiodyear = get_last_period_run_awards_year($courseid, RANK_TYPE_ONLINE_TIME);
        $periodyears = $DB->get_records_sql($sql, array(
                'course' => $courseid,
                'year' => $lastperiodyear
        ));
    }
    return $periodyears;
}

/**
 * This returns the ID of the award
 * @param string $award
 * @return int
 */
function get_award_id($award) {
    switch ($award) {
        case 'gold':
            $awardid = GOLD_AWARD_ID;
            break;
        case 'silver':
            $awardid = SILVER_AWARD_ID;
            break;
        case 'bronze':
            $awardid = BRONZE_AWARD_ID;
            break;
        default:
            $awardid = GOLD_AWARD_ID;
            break;
    }
    return $awardid;
}