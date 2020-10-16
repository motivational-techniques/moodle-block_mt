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
 * This generates the ranking
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * processs an entry for the rank
 * @param array $param
 */
function ranks_process_entry($param) {
    global $DB;

    $parameters = array (
        'userid' => $param->userid,
        'rank_type_id' => $param->rank_type_id,
        'period' => $param->period,
        'courseid' => $param->courseid,
        'period_type' => $param->period_type
    );
    if ($param->period_type == RANK_PERIOD_OVERALL) {
        $parameters['period'] = null;
    }
    if ($param->gradeid != null) {
        $parameters ['gradeid'] = $param->gradeid;
    }
    $recordcound = $DB->count_records ( 'block_mt_ranks_user', $parameters );
    if ($recordcound < 1) {
        $parameters ['rank'] = $param->rank;
        $parameters ['active'] = block_mt_is_active($param->userid, $param->courseid);
        $DB->insert_record ( 'block_mt_ranks_user', $param, true, false );
    } else {
        $parameters ['id'] = $DB->get_field ( 'block_mt_ranks_user', 'id', $parameters );
        $parameters ['rank'] = $param->rank;
        $parameters ['active'] = block_mt_is_active($param->userid, $param->courseid);
        $DB->update_record('block_mt_ranks_user', $parameters, false);
    }
}

/**
 * processs an entry for the active rank
 * @param array $param
 */
function active_ranks_process_entry($param) {
    global $DB;

    $parameters = array (
            'userid' => $param->userid,
            'rank_type_id' => $param->rank_type_id,
            'period' => $param->period,
            'courseid' => $param->courseid,
            'period_type' => $param->period_type
    );
    if ($param->period_type == RANK_PERIOD_OVERALL) {
        $parameters['period'] = null;
    }
    if ($param->gradeid != null) {
        $parameters ['gradeid'] = $param->gradeid;
    }
    $recordcound = $DB->count_records('block_mt_ranks_user', $parameters);
    if ($recordcound < 1) {
        $parameters ['rank_active'] = $param->rankactive;
        $parameters ['active'] = block_mt_is_active($param->userid, $param->courseid);
        $DB->insert_record ( 'block_mt_ranks_user', $param, true, false );
    } else {
        $parameters ['id'] = $DB->get_field('block_mt_ranks_user', 'id', $parameters);
        $parameters ['rank_active'] = $param->rankactive;
        $parameters ['active'] = block_mt_is_active($param->userid, $param->courseid);
        $DB->update_record('block_mt_ranks_user', $parameters, false);
    }
}

/**
 * process all entries for a course
 * @param integer $paramuserranking
 * @param integer $paramitemid
 * @param string $paramranktype
 * @param string $paramperiodtype
 */
function ranks_process_course($paramuserranking, $paramitemid, $paramranktype, $paramperiodtype) {
    global $DB, $CFG;

    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "select @curRank := @curRank + 1 as 'rank', userid, itemid, finalgrade from (
                select {grade_grades}.userid, itemid, (finalgrade/rawgrademax)*100 as finalgrade
                from (select t.userid from (select userid from {role_assignments} join {context} on
                {context}.id = {role_assignments}.contextid where roleid = 5 and instanceid =:instanceid) t
                join {block_mt_active_users} on t.userid = {block_mt_active_users}.userid
                where {block_mt_active_users}.active = 1
                and {block_mt_active_users}.courseid =:courseid) a
                join {grade_grades} on {grade_grades}.userid = a.userid
                where {grade_grades}.itemid =:itemid and {grade_grades}.finalgrade is not null
                ) as grades,
                (SELECT @curRank := 0) r
                ORDER BY finalgrade DESC;";
            break;
        default :
            $sql = "select {grade_grades}.userid, itemid, (finalgrade/rawgrademax)*100 as finalgrade,
                rank() over (order by finalgrade desc) as rank
                from (select t.userid from (select userid from {role_assignments} join {context} on
                {context}.id = {role_assignments}.contextid where roleid = 5 and instanceid =:instanceid) t
                join {block_mt_active_users} on t.userid = {block_mt_active_users}.userid
                where {block_mt_active_users}.active = 1
                and {block_mt_active_users}.courseid =:courseid) a
                join {grade_grades} on {grade_grades}.userid = a.userid
                where {grade_grades}.itemid =:itemid and {grade_grades}.finalgrade is not null";
            break;
    }

    $parameters = array (
            'instanceid' => $paramuserranking->courseid,
            'courseid' => $paramuserranking->courseid,
            'itemid' => $paramitemid
        );
    $rankings = $DB->get_records_sql($sql, $parameters);
    foreach ($rankings as $ranking) {
        if (($paramperiodtype == RANK_PERIOD_OVERALL) || ($paramuserranking->rank_type_id == RANK_TYPE_GRADES)) {
            $paramuserranking->period = null;
        } else {
            $paramuserranking->period = block_mt_get_current_period();
        }
        $paramuserranking->rank = $ranking->rank;
        $paramuserranking->userid = $ranking->userid;
        $paramuserranking->gradeid = $ranking->itemid;
        $paramuserranking->rank_type_id = $paramranktype;
        $paramuserranking->period_type = $paramperiodtype;

        ranks_process_entry($paramuserranking);
        $paramuserranking->rankactive = $ranking->rank;
        active_ranks_process_entry($paramuserranking);
    }
}