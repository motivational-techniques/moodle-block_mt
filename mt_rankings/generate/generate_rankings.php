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
 * This generates the rankings
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
global $CFG;
// @codingStandardsIgnoreLine
require_once($CFG->dirroot . "/config.php");

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/blocks/mt/mt_rankings/generate/grades.php");
require_once($CFG->dirroot . "/blocks/mt/mt_rankings/generate/milestones.php");
require_once($CFG->dirroot . "/blocks/mt/mt_rankings/generate/online_time.php");
require_once($CFG->dirroot . "/blocks/mt/mt_rankings/generate/participation.php");
require_once($CFG->dirroot . "/blocks/mt/mt_rankings/generate/rankings_process.php");
require_once($CFG->dirroot . "/blocks/mt/mt_rankings/generate/student_list.php");
require_once($CFG->dirroot . "/blocks/mt/mt_rankings/generate/last_period_run.php");
require_once($CFG->dirroot . "/blocks/mt/includes/helper_functions.php");
require_once($CFG->dirroot . "/blocks/mt/includes/determine_active.php");
require_once($CFG->dirroot . "/blocks/mt/includes/get_milestones.php");
require_once($CFG->dirroot . "/blocks/mt/includes/get_assignment_list.php");
require_once($CFG->dirroot . "/blocks/mt/includes/get_quiz_list.php");
require_once($CFG->dirroot . "/blocks/mt/includes/get_period.php");
require_once($CFG->dirroot . "/blocks/mt/includes/constants.php");

/**
 * Generate active users based on last access time and whether users are enrolled in the course.
 */
function generate_active_users() {
    global $DB, $CFG;

    $sql = "TRUNCATE TABLE {block_mt_active_users}";
    $DB->execute ( $sql, array () );

    if ($CFG->dbtype == DB_TYPE_POSTGRES) {
        $sql = "select distinct {user_lastaccess}.id, {user_lastaccess}.courseid, {user_lastaccess}.userid,
            cast((DATE_PART('day', current_timestamp - to_timestamp(timeaccess)) < :inactive_time) as int) as active
            from {user_lastaccess}
            join {user_enrolments}
            on {user_enrolments}.userid = {user_lastaccess}.userid
            join {enrol}
            on {user_enrolments}.enrolid = {enrol}.id
            order by userid";
    } else {
        $sql = "select distinct {user_lastaccess}.id, {user_lastaccess}.courseid, {user_lastaccess}.userid,
            DATEDIFF(CURDATE(), FROM_UNIXTIME(timeaccess)) < :inactive_time as active
            from {user_lastaccess}
            join {user_enrolments}
            on {user_enrolments}.userid = {user_lastaccess}.userid
            join {enrol}
            on {user_enrolments}.enrolid = {enrol}.id
            order by userid";
    }
    if (isset ( $CFG->block_mt_ranks_inactive_time )) {
        $inactivetime = $CFG->block_mt_ranks_inactive_time;
    } else {
        $inactivetime = intval ( get_string ( 'mt_rankings:settings_inactive_time_value', 'block_mt' ) );
    }
    $activeusersparams = array(
        'inactive_time' => $inactivetime
    );
    $activeusers = $DB->get_records_sql ( $sql, $activeusersparams );
    foreach ($activeusers as $activeuser) {
        $activeuserparams = array (
            'id' => null,
            'userid' => $activeuser->userid,
            'courseid' => $activeuser->courseid,
            'active' => $activeuser->active
        );
        $DB->insert_record ('block_mt_active_users', $activeuserparams);
    }
}

/**
 * Clear ranks
 */
function clear_ranks() {
    global $DB, $CFG;

    if (isset ( $CFG->block_mt_ranks_regenerate_all )) {
        if ($CFG->block_mt_ranks_regenerate_all == 1) {
            mtrace(get_string('mt:cron_rankings_clearing', 'block_mt'));
            $sql = 'TRUNCATE TABLE {block_mt_ranks_last_per}';
            $DB->execute ( $sql, null );

            $sql = 'TRUNCATE TABLE {block_mt_ranks_user}';
            $DB->execute ( $sql, null );

            $sql = 'TRUNCATE TABLE {block_mt_ranks_num_posts}';
            $DB->execute ( $sql, null );

            $sql = 'TRUNCATE TABLE {block_mt_ranks_onl_time}';
            $DB->execute ( $sql, null );

            $sql = 'TRUNCATE TABLE {block_mt_ranks_read_posts}';
            $DB->execute ( $sql, null );

            $sql = 'TRUNCATE TABLE {block_mt_ranks_rating_posts}';
            $DB->execute ( $sql, null );

            $sql = 'TRUNCATE TABLE {block_mt_ranks_milestones}';
            $DB->execute ( $sql, null );
        }
    }
}

/**
 * generate ranks
 */
function generate_ranks() {
    $userranking = new stdClass ();

    clear_ranks ();

    $coursedata = get_courses();

    foreach ($coursedata as &$coursedata) {
        if (block_mt_is_enabled_for_course($coursedata->id, 'rankings')) {
            $userranking->courseid = $coursedata->id;

            mtrace(get_string('mt:cron_rankings_main', 'block_mt', $userranking->courseid));

            mtrace(get_string('mt:cron_rankings_overall', 'block_mt'));
            generate_ranks_overall_grade ( $coursedata->id, $userranking );

            mtrace(get_string('mt:cron_rankings_assign', 'block_mt'));
            generate_ranks_assignments ( $coursedata->id, $userranking );

            mtrace(get_string('mt:cron_rankings_quiz', 'block_mt'));
            generate_ranks_quizzes ( $coursedata->id, $userranking );

            mtrace(get_string('mt:cron_rankings_time_online', 'block_mt'));
            generate_ranks_online_time_all ( $coursedata->id, $userranking );
            generate_ranks_online_time_overall ( $coursedata->id, $userranking );
            generate_ranks_online_time_overall_active( $coursedata->id, $userranking );

            mtrace(get_string('mt:cron_rankings_num_posts', 'block_mt'));
            generate_ranks_number_posts_all ( $coursedata->id, $userranking );
            generate_ranks_number_posts_overall($coursedata->id, $userranking);
            generate_ranks_number_posts_overall_active($coursedata->id, $userranking);

            mtrace(get_string('mt:cron_rankings_posts_read', 'block_mt'));
            generate_ranks_read_posts_all ( $coursedata->id, $userranking );
            generate_ranks_read_posts_overall($coursedata->id, $userranking);
            generate_ranks_read_posts_overall_active($coursedata->id, $userranking);

            mtrace(get_string('mt:cron_rankings_rating_posts', 'block_mt'));
            generate_ranks_ratings_posts_all ( $coursedata->id, $userranking );
            generate_ranks_ratings_posts_overall($coursedata->id, $userranking);
            generate_ranks_ratings_posts_overall_active($coursedata->id, $userranking);

            mtrace(get_string('mt:cron_rankings_milestones', 'block_mt'));
            generate_ranks_milestones_all ( $coursedata->id, $userranking );
        }
    }
}

$starttime = microtime ();
mtrace(get_string('mt:cron_rankings_start', 'block_mt'));

generate_active_users ();

generate_ranks ();

mtrace(get_string('mt:cron_rankings_end', 'block_mt', microtime_diff($starttime, microtime ())));