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
 * This generates the milestone rankings
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate active ranks for milestones pace
 * @param integer $courseid
 * @param integer $userranking
 */
function generate_ranks_milestones_pace_active($courseid, $userranking) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @rank:=@rank+1 as rank, userid
                FROM (SELECT avg(milestone_time) as avg_milestone_time, userid, @rank:=0
                FROM {block_mt_ranks_milestones}
                WHERE courseid=:courseid
                AND period_type=:period_type
                AND active=1
                GROUP BY userid
                ORDER BY avg(milestone_time) ASC) as mt";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by avg_milestone_time ASC) as rank
                FROM (SELECT avg(milestone_time) as avg_milestone_time, userid
                FROM {block_mt_ranks_milestones}
                WHERE courseid=:courseid
                AND period_type=:period_type
                AND active=1
                GROUP BY userid
                ORDER BY avg(milestone_time) ASC) as mt";
            break;
    }

    $rankingsparams = array (
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    $rankings = $DB->get_records_sql ( $sql, $rankingsparams );
    foreach ($rankings as $ranking) {
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = 0;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_MILESTONE;
        $userranking->rankname = get_string('mt_rankings:generate_rank_milestone_pace', 'block_mt', $courseid);
        $userranking->period_type = RANK_PERIOD_OVERALL;
        $userranking->rankactive = $ranking->rank;
        active_ranks_process_entry ($userranking);
    }
}

/**
 * generate all ranks for milestones pace
 * @param integer $courseid
 * @param integer $userranking
 */
function generate_ranks_milestones_pace($courseid, $userranking) {
    global $DB, $CFG;
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @rank:=@rank+1 as rank, userid
                FROM (SELECT avg(milestone_time) as avg_milestone_time, userid, @rank:=0
                FROM {block_mt_ranks_milestones}
                WHERE courseid=:courseid
                AND period_type=:period_type
                GROUP BY userid
                ORDER BY avg(milestone_time) ASC) as mt";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by avg_milestone_time ASC) as rank
                FROM (SELECT avg(milestone_time) as avg_milestone_time, userid
                FROM {block_mt_ranks_milestones}
                WHERE courseid=:courseid
                AND period_type=:period_type
                GROUP BY userid
                ORDER BY avg(milestone_time) ASC) as mt";
            break;
    }

    $rankingsparams = array (
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    $rankings = $DB->get_records_sql ( $sql, $rankingsparams );
    foreach ($rankings as $ranking) {
        $userranking->rank = $ranking->rank;
        $userranking->userid = $ranking->userid;
        $userranking->gradeid = 0;
        $userranking->period = null;
        $userranking->rank_type_id = RANK_TYPE_MILESTONE;
        $userranking->rankname = get_string('mt_rankings:generate_rank_milestone_pace', 'block_mt', $courseid);
        $userranking->period_type = RANK_PERIOD_OVERALL;
        ranks_process_entry ( $userranking );
    }
}

/**
 * Get the start times for users in the course
 * @param integer $courseid
 * @return array
 */
function get_user_course_start_times($courseid) {
    global $DB;

    $parameters = array (
            'course' => $courseid
    );
    $sql = "SELECT A.userid,
            A.timestart
            FROM {user_enrolments} A,
             {enrol} B
            WHERE A.enrolid = B.id
            AND B.courseid = :course";
    $users = $DB->get_records_sql ( $sql, $parameters);
    $usertimes = array ();
    foreach ($users as $user) {
        $usertimes[$user->userid] = $user->timestart;
    }
    return $usertimes;
}

/**
 * generate all ranks milestones time
 * @param integer $courseid
 * @param integer $userranking
 */
function generate_ranks_milestones_time($courseid, $userranking) {
    $usertimes = get_user_course_start_times($courseid);
    $milestones = get_milestones_for_course($courseid);

    foreach ($milestones as $milestone) {
        foreach ($usertimes as $userid => $starttime) {
            $finishtimes = get_finish_times($milestone->module, $milestone, $userid);
            if ($finishtimes != null) {
                $milestone->time = abs($finishtimes->timefinish - $starttime);
                if ($milestone->time <= 0) {
                    $milestone->time = 0;
                } else {
                    add_update_milestone_ranking($milestone, $userid, $courseid);
                }
                $usertimes [$userid] = $finishtimes->timefinish;
            }
            $rankings = get_milestone_rankings($milestone, $courseid);
            foreach ($rankings as $ranking) {
                $userranking->rank = $ranking->rank;
                $userranking->userid = $ranking->userid;
                $userranking->gradeid = $milestone->module;
                $userranking->rank_type_id = RANK_TYPE_MILESTONE;
                $userranking->rankname = get_string('mt_rankings:generate_rank_milestone_rank_name', 'block_mt', $milestone);
                $userranking->period_type = $milestone->instance;
                ranks_process_entry ( $userranking );
            }
        }
    }
}

/**
 * generate active ranks milestones time
 * @param integer $courseid
 * @param integer $userranking
 */
function generate_ranks_milestones_time_active($courseid, $userranking) {
    $usertimes = get_user_course_start_times($courseid);
    $milestones = get_milestones_for_course($courseid);

    foreach ($milestones as $milestone) {
        foreach ($usertimes as $userid => $starttime) {
            $finishtimes = get_finish_times($milestone->module, $milestone, $userid);
            if ($finishtimes != null) {
                $milestone->time = abs($finishtimes->timefinish - $starttime);
                if ($milestone->time <= 0) {
                    $milestone->time = 0;
                } else {
                    add_update_milestone_ranking($milestone, $userid, $courseid);
                }
                $usertimes [$userid] = $finishtimes->timefinish;
            }
            $rankings = get_milestone_rankings_active($milestone, $courseid);
            foreach ($rankings as $ranking) {
                $userranking->userid = $ranking->userid;
                $userranking->gradeid = $milestone->module;
                $userranking->rank_type_id = RANK_TYPE_MILESTONE;
                $userranking->rankname = get_string('mt_rankings:generate_rank_milestone_rank_name', 'block_mt', $milestone);
                $userranking->period_type = $milestone->instance;
                $userranking->rankactive = $ranking->rank;
                active_ranks_process_entry($userranking);
            }
        }
    }
}

/**
 * get milestones for course
 * @param string $courseid
 * @return array
 */
function get_milestones_for_course($courseid) {
    global $DB;
    $parameters = array (
            'course' => $courseid
    );
    return  $DB->get_records('block_mt_p_timeline', $parameters, 'week', '*');
}

/**
 * add or update milestone ranking
 * @param array $milestone
 * @param string $userid
 * @param string $courseid
 */
function add_update_milestone_ranking($milestone, $userid, $courseid) {
    global $DB;
    $parameters = array (
            'userid' => $userid,
            'milestone' => $milestone->id,
            'module' => $milestone->module,
            'instance' => $milestone->instance,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    $recordcount = $DB->count_records ('block_mt_ranks_milestones', $parameters);
    if ($recordcount < 1) {
        $parameters ['milestone_time'] = $milestone->time;
        $parameters ['active'] = is_active($userid, $courseid);
        $DB->insert_record ( 'block_mt_ranks_milestones', $parameters );
    } else {
        $parameters ['id'] = $DB->get_field ('block_mt_ranks_milestones', 'id', $parameters);
        $parameters ['milestone_time'] = $milestone->time;
        $parameters ['active'] = is_active($userid, $courseid);
        $DB->update_record ( 'block_mt_ranks_milestones', $parameters );
    }
}

/**
 * get milestone rankings
 * @param array $milestone
 * @param string $courseid
 * @return array
 */
function get_milestone_rankings($milestone, $courseid) {
    global $CFG, $DB;

    $milestone->name = block_mt_get_milestone_name($milestone->id, $milestone->instance, $courseid);
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM {block_mt_ranks_milestones} np, (SELECT @curRank := 0) r
                WHERE milestone=:milestone AND courseid=:courseid
                AND period_type=:period_type
                AND milestone_time > 0
                ORDER BY milestone_time ASC";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by milestone_time) as rank
                FROM {block_mt_ranks_milestones}
                WHERE milestone=:milestone AND courseid=:courseid
                AND period_type=:period_type
                AND milestone_time > 0
                ORDER BY milestone_time ASC";
            break;
    }
    $rankingsparams = array (
            'milestone' => $milestone->id,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    return $DB->get_records_sql($sql, $rankingsparams);
}

/**
 * get active milestone rankings
 * @param array $milestone
 * @param string $courseid
 * @return array
 */
function get_milestone_rankings_active($milestone, $courseid) {
    global $CFG, $DB;

    $milestone->name = block_mt_get_milestone_name($milestone->id, $milestone->instance, $courseid);
    switch($CFG->dbtype) {
        case DB_TYPE_MARIA :
        case DB_TYPE_MYSQL :
            $sql = "SELECT @curRank := @curRank + 1 AS rank, userid
                FROM {block_mt_ranks_milestones} np, (SELECT @curRank := 0) r
                WHERE milestone=:milestone AND courseid=:courseid
                AND period_type=:period_type
                AND milestone_time > 0
                AND active=1
                ORDER BY milestone_time ASC";
            break;
        default :
            $sql = "SELECT userid, rank() over (order by milestone_time) as rank
                FROM {block_mt_ranks_milestones}
                WHERE milestone=:milestone AND courseid=:courseid
                AND period_type=:period_type
                AND milestone_time > 0
                AND active=1
                ORDER BY milestone_time ASC";
            break;
    }
    $rankingsparams = array (
            'milestone' => $milestone->id,
            'courseid' => $courseid,
            'period_type' => RANK_PERIOD_MONTHLY
    );
    return $DB->get_records_sql($sql, $rankingsparams);
}

/**
 * get finish times
 * @param string $module
 * @param array $milestone
 * @param string $userid
 * @return array
 */
function get_finish_times($module, $milestone, $userid) {
    $quizid = block_mt_get_quiz_id();
    $assignmentid = block_mt_get_assign_id();
    $finishtimes = null;

    if ($quizid == $module) {
        $finishtimes = get_quiz_finish_times($milestone, $userid);
    }
    if ($assignmentid == $module) {
        $finishtimes = get_assignment_finish_times($milestone, $userid);
    }
    return $finishtimes;
}

/**
 * get assignment finish times
 * @param array $milestone
 * @param string $userid
 * @return array
 */
function get_assignment_finish_times($milestone, $userid) {
    global $DB;
    $sql = "SELECT {assign_submission}.id, {assign_submission}.timecreated as timefinish, grade
        FROM {assign_submission}
        join {assign_grades}
        on ({assign_submission}.userid={assign_grades}.userid)
        and ({assign_submission}.assignment = {assign_grades}.assignment)
        WHERE {assign_submission}.assignment = :assignment
        AND {assign_submission}.userid = :userid";
    $finishtimesparams = array (
            'assignment' => $milestone->instance,
            'userid' => $userid
    );
    return $DB->get_record_sql ( $sql, $finishtimesparams );
}

/**
 * get quiz finish times
 * @param array $milestone
 * @param string $userid
 * @return array
 */
function get_quiz_finish_times($milestone, $userid) {
    global $DB;
    $sql = "SELECT timefinish
        FROM {quiz_attempts}
        WHERE quiz = :quiz
        AND userid = :userid
        AND sumgrades > 0
        ORDER BY attempt DESC
        LIMIT 1";
    $quizfinishtimeparams = array(
            'quiz' => $milestone->instance,
            'userid' => $userid
    );
    return $DB->get_record_sql($sql, $quizfinishtimeparams );
}

/**
 * generate ranks milestones all
 * @param integer $courseid
 * @param integer $userranking
 */
function generate_ranks_milestones_all($courseid, $userranking) {
    generate_ranks_milestones_time($courseid, $userranking);
    generate_ranks_milestones_time_active($courseid, $userranking);
    generate_ranks_milestones_pace($courseid, $userranking);
    generate_ranks_milestones_pace_active($courseid, $userranking);
}