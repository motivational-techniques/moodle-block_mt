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
 * This generates the achievement awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate achievement awards
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_achievements($paramcourseid) {
    mtrace(get_string('mt:cron_awards_achievements', 'block_mt'));

    generate_awards_achievements_overall($paramcourseid);
    generate_awards_achievements_overall_active($paramcourseid);
}

/**
 * generate achievement awards overall
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_achievements_overall($paramcourseid) {
    global $DB, $CFG;

    $goldawardweight = get_awards_settingS('mt_awards:achievements_gold_weight_value', $paramcourseid);
    $silverawardweight = get_awards_settingS('mt_awards:achievements_silver_weight_value', $paramcourseid);
    $bronzeawardweight = get_awards_settingS('mt_awards:achievements_bronze_weight_value', $paramcourseid);

    $sql = "SELECT DISTINCT userid
        FROM {block_mt_awards_user}
        WHERE courseid = :courseid;";
    $students = $DB->get_records_sql($sql, array(
        'courseid' => $paramcourseid
    ));
    $gold = 0;
    $silver = 0;
    $bronze = 0;
    $total = 0;
    foreach ($students as $students) {
        $parameters = array(
            'courseid' => $paramcourseid,
            'userid' => $students->userid
        );

        $parameters['awardid'] = GOLD_AWARD_ID;
        $gold = $DB->count_records('block_mt_awards_user', $parameters);
        $parameters['awardid'] = SILVER_AWARD_ID;
        $silver = $DB->count_records('block_mt_awards_user', $parameters);
        $parameters['awardid'] = BRONZE_AWARD_ID;
        $bronze = $DB->count_records('block_mt_awards_user', $parameters);

        $total = $gold * $goldawardweight + $silver * $silverawardweight + $bronze * $bronzeawardweight;

        $parameters = array(
            'userid' => $students->userid,
            'courseid' => $paramcourseid
        );
        $recordcount = $DB->count_records('block_mt_ranks_achiev', $parameters);
        if ($recordcount < 1) {
            $parameters['gold'] = $gold;
            $parameters['silver'] = $silver;
            $parameters['bronze'] = $bronze;
            $parameters['total'] = $total;
            $DB->insert_record('block_mt_ranks_achiev', $parameters);
        } else {
            $updateid = $DB->get_field('block_mt_ranks_achiev', 'id', $parameters);
            $parameters['id'] = $updateid;
            $parameters['gold'] = $gold;
            $parameters['silver'] = $silver;
            $parameters['bronze'] = $bronze;
            $parameters['total'] = $total;
            $DB->update_record('block_mt_ranks_achiev', $parameters);
        }
    }

    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM (SELECT userid, total
                FROM {block_mt_ranks_achiev}
                WHERE courseid=:courseid
                ORDER BY total desc) ra, (SELECT @curRank := 0) r";
            break;
        default :
            $sql = "select userid, rank() over (order by total desc) as rank
                FROM (SELECT userid, total
                FROM {block_mt_ranks_achiev}
                WHERE courseid=:courseid
                ORDER BY total desc) ra";
            break;
    }

    $userranking = new stdClass();

    $rankings = $DB->get_records_sql($sql, array(
        'courseid' => $paramcourseid
    ));
    foreach ($rankings as &$rankings) {
        $userranking->rank = $rankings->rank;
        $userranking->userid = $rankings->userid;
        $userranking->gradeid = null;
        $userranking->courseid = $paramcourseid;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_ACHIEVEMENT;
        $userranking->rankname = get_string('mt_rankings:generate_rank_achievements_rank_name', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        ranks_process_entry($userranking);
    }
}

/**
 * generate active achievement awards overall
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_achievements_overall_active($paramcourseid) {
    global $DB, $CFG;

    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM (SELECT userid, total
                FROM {block_mt_ranks_achiev}
                WHERE courseid=:courseid
                AND active=1
                ORDER BY total desc) ra, (SELECT @curRank := 0) r";
            break;
        default :
            $sql = "select userid, rank() over (order by total desc) as rank
                FROM (SELECT userid, total
                FROM {block_mt_ranks_achiev}
                WHERE courseid=:courseid
                AND active=1
                ORDER BY total desc) ra";
            break;
    }

    $userranking = new stdClass();

    $rankings = $DB->get_records_sql($sql, array(
            'courseid' => $paramcourseid
    ));
    foreach ($rankings as &$rankings) {
        $userranking->rank = $rankings->rank;
        $userranking->userid = $rankings->userid;
        $userranking->gradeid = null;
        $userranking->courseid = $paramcourseid;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_ACHIEVEMENT;
        $userranking->rankname = get_string('mt_rankings:generate_rank_achievements_rank_name', 'block_mt');
        $userranking->period_type = RANK_PERIOD_OVERALL;
        $userranking->rankactive = $rankings->rank;
        active_ranks_process_entry($userranking);
    }
}