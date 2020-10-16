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
 * This generates the milestone awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate milestones awards
 *
 * @param string $courseid
 * @return null
 */
function generate_awards_milestones($courseid) {
    global $DB;

    mtrace(get_string('mt:cron_awards_milestones', 'block_mt'));

    $milestones = get_all_milestones($courseid);
    foreach ($milestones as $milestone) {
        $rankings = get_milestone_rankings_by_milestone($courseid, $milestone->milestone);
        foreach ($rankings as $ranking) {
            $completeddays = $ranking->milestone_time / (60 * 60 * 24);

            $milestone->id = $milestone->milestone;
            $milestone->instance = block_mt_get_milestone_instance($milestone->id);
            $milestone->name = block_mt_get_milestone_name($milestone->id, $milestone->instance, $courseid);

            $awardname = get_string('mt_awards:generate_award_milestone_award_name', 'block_mt', $milestone);
            $awardid = determine_award($courseid, $completeddays);

            if ($awardid != '0') {
                $params = array(
                    'userid' => $ranking->userid,
                    'courseid' => $courseid,
                    'award_name' => $awardname,
                    'awardid' => $awardid,
                    'period' => null,
                    'period_type' => null
                );

                $recordcount = $DB->count_records('block_mt_awards_user', $params);
                if ($recordcount < 1) {
                    $DB->insert_record('block_mt_awards_user', $params);
                } else {
                    $params['id'] = $DB->get_field('block_mt_awards_user', 'id', $params);
                    $DB->update_record('block_mt_awards_user', $params);
                }
            }
        }
    }
}

/**
 * get all milestones
 *
 * @param string $courseid
 * @return array
 */
function get_all_milestones($courseid) {
    global $DB;
    $sql = "SELECT milestone
        FROM {block_mt_ranks_milestones}
        WHERE courseid=:courseid
        GROUP BY milestone";
    $parameters = array(
            'courseid' => $courseid
    );
    return $DB->get_records_sql($sql, $parameters);
}

/**
 * get milestones rankings
 * @param string $courseid
 * @param string $milestoneid
 * @return array
 */
function get_milestone_rankings_by_milestone($courseid, $milestoneid) {
    global $DB;
    $parameters = array(
            'courseid' => $courseid,
            'milestone' => $milestoneid
    );
    return $DB->get_records('block_mt_ranks_milestones', $parameters);
}

/**
 * get determine award
 * @param string $courseid
 * @param string $completeddays
 * @return string
 */
function determine_award($courseid, $completeddays) {
    $goldaward = get_awards_settings('mt_awards:milestones_gold_days_value', $courseid);
    $silveraward = get_awards_settings('mt_awards:milestones_silver_days_value', $courseid);
    $bronzeaward = get_awards_settings('mt_awards:milestones_bronze_days_value', $courseid);
    $awardid = NO_AWARD_ID;

    if ($completeddays < 0) {
        $awardid = GOLD_AWARD_ID;
    } else {
        if ($completeddays <= $goldaward) {
            $awardid = GOLD_AWARD_ID;
        } else if ($completeddays <= $silveraward) {
            $awardid = SILVER_AWARD_ID;
        } else if ($completeddays <= $bronzeaward) {
            $awardid = BRONZE_AWARD_ID;
        }
    }
    return $awardid;
}