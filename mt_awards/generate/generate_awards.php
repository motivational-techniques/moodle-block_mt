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
 * This generates all the awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

// @codingStandardsIgnoreLine
require_once($CFG->dirroot . "/config.php");
require_once($CFG->dirroot . "/blocks/mt/mt_awards/generate/mt_awards_tables.php");
require_once($CFG->dirroot . "/blocks/mt/mt_awards/generate/last_period_run.php");
require_once($CFG->dirroot . "/blocks/mt/mt_awards/generate/grades.php");
require_once($CFG->dirroot . "/blocks/mt/mt_awards/generate/online_time.php");
require_once($CFG->dirroot . "/blocks/mt/mt_awards/generate/participation.php");
require_once($CFG->dirroot . "/blocks/mt/mt_awards/generate/milestones.php");
require_once($CFG->dirroot . "/blocks/mt/mt_awards/includes/get_grade_award_name.php");
require_once($CFG->dirroot . "/blocks/mt/mt_awards/includes/get_period_participation.php");
require_once($CFG->dirroot . "/blocks/mt/mt_awards/generate/achievements.php");
require_once($CFG->dirroot . "/blocks/mt/includes/configuration_settings.php");
require_once($CFG->dirroot . "/blocks/mt/includes/get_award_name.php");
require_once($CFG->dirroot . "/blocks/mt/includes/get_milestones.php");
require_once($CFG->dirroot . "/blocks/mt/includes/helper_functions.php");
require_once($CFG->dirroot . "/blocks/mt/includes/get_period.php");
require_once($CFG->dirroot . '/blocks/mt/includes/constants.php');

/**
 * process an award entry
 *
 * @param array $param
 * @return null
 */
function awards_process_entry($param) {
    global $DB;

    $parameters = array(
        'userid' => $param->userid,
        'courseid' => $param->courseid,
        'itemid' => $param->itemid,
        'award_name' => $param->award_name,
        'period' => $param->period,
        'period_type' => $param->period_type
    );
    if (($param->period_type == RANK_PERIOD_OVERALL) || ($param->period_type == RANK_PERIOD_INDIVIDUAL)) {
        $parameters['period'] = null;
    }

    $recordcount = $DB->count_records('block_mt_awards_user', $parameters);
    if ($recordcount < 1) {
        $parameters['awardid'] = $param->awardid;
        $DB->insert_record('block_mt_awards_user', $parameters, false);
    } else {
        $updateid = $DB->get_field('block_mt_awards_user', 'id', $parameters);
        $parameters['awardid'] = $param->awardid;
        $parameters['id'] = $updateid;
        $DB->update_record('block_mt_awards_user', $parameters);
    }
}
/**
 * process a course for awards
 *
 * @param string $paramcourseid
 * @param string $paramitemid
 * @param string $paramawardname
 * @param string $paramperiod
 * @param string $paramperiodtype
 * @return null
 */
function awards_process_course($paramcourseid, $paramitemid, $paramawardname, $paramperiod, $paramperiodtype) {
    global $DB;

    $goldaward = block_mt_get_awards_settings('mt_awards:grades_gold_count_value', $paramcourseid);
    $silveraward = block_mt_get_awards_settings('mt_awards:grades_silver_count_value', $paramcourseid);
    $bronzeaward = block_mt_get_awards_settings('mt_awards:grades_bronze_count_value', $paramcourseid);

    $sql = 'SELECT * from {grade_grades} where itemid=:itemid';

    $awards = $DB->get_records_sql($sql, array(
        'itemid' => $paramitemid
    ));
    foreach ($awards as &$awards) {
        $awardid = NO_AWARD_ID;
        $finalgrade = ($awards->finalgrade / $awards->rawgrademax) * 100;

        if ($finalgrade >= $bronzeaward) {
            $awardid = BRONZE_AWARD_ID;
        }
        if ($finalgrade >= $silveraward) {
            $awardid = SILVER_AWARD_ID;
        }
        if ($finalgrade >= $goldaward) {
            $awardid = GOLD_AWARD_ID;
        }
        if ($awardid != '0') {
            $params = new stdClass();
            $params->userid = $awards->userid;
            $params->courseid = $paramcourseid;
            $params->awardid = $awardid;
            $params->itemid = $paramitemid;
            $params->award_name = $paramawardname;
            $params->period = $paramperiod;
            $params->period_type = $paramperiodtype;

            awards_process_entry($params);
        }
    }
}

/**
 * clear awards
 *
 * @return null
 */
function clear_awards() {
    global $DB;

    if (null !== get_config("block_mt", "awards_regenerate_all")) {
        if (get_config("block_mt", "awards_regenerate_all") == 1) {
            mtrace(get_string('mt:cron_awards_clearing', 'block_mt'));
            $sql = 'TRUNCATE TABLE {block_mt_awards_last_period}';
            $DB->execute($sql, null);

            $sql = 'TRUNCATE TABLE {block_mt_awards_user}';
            $DB->execute($sql, null);

            $sql = 'TRUNCATE TABLE {block_mt_awards_count_all}';
            $DB->execute($sql, null);

            $sql = 'TRUNCATE TABLE {block_mt_ranks_achiev}';
            $DB->execute($sql, null);
        }
    }
}

/**
 * generate awards
 *
 * @return null
 */
function generate_awards() {
    $userawards = new stdClass();

    $coursedata = get_courses();

    clear_awards();

    foreach ($coursedata as &$coursedata) {
        if (block_mt_is_enabled_for_course($coursedata->id, 'awards')) {
            $userawards->courseid = $coursedata->id;
            mtrace(get_string('mt:cron_awards_main', 'block_mt', $userawards->courseid));

            generate_awards_overall_grade($userawards->courseid);

            generate_awards_assignment($userawards->courseid);

            generate_awards_quiz($userawards->courseid);

            generate_awards_online_time($userawards->courseid);

            generate_online_time_overall_count($userawards->courseid);

            generate_awards_number_posts($userawards->courseid);

            generate_awards_read_posts($userawards->courseid);

            generate_awards_rating_posts($userawards->courseid);

            generate_awards_milestones($userawards->courseid);

            generate_awards_achievements($userawards->courseid);
        }
    }
}

$starttime = microtime();
mtrace(get_string('mt:cron_awards_start', 'block_mt'));

generate_awards();

mtrace(get_string('mt:cron_awards_end', 'block_mt', microtime_diff($starttime, microtime())));