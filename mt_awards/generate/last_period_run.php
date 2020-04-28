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
 * This updates the last period run flag for the awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * update last period run for awards generation
 *
 * @param string $courseid
 * @param string $ranktypeid
 * @param string $period
 * @return null
 */
function update_last_period_run_awards($courseid, $ranktypeid, $period) {
    global $DB;

    if (! isset($period)) {
        $period = get_current_period();
    }
    $recordcount = $DB->count_records('block_mt_awards_last_period',
        array(
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid));

    if ($recordcount < 1) {
        $DB->insert_record('block_mt_awards_last_period',
            array(
                'courseid' => $courseid,
                'rank_type_id' => $ranktypeid,
                'period' => $period));
    } else {
        $id = $DB->get_field('block_mt_awards_last_period', 'id',
            array(
                'courseid' => $courseid,
                'rank_type_id' => $ranktypeid));
            $DB->update_record('block_mt_awards_last_period',
                array(
                    'id' => $id,
                    'courseid' => $courseid,
                    'rank_type_id' => $ranktypeid,
                    'period' => $period));
    }
}

/**
 * get last period run for awards generation
 *
 * @param string $courseid
 * @param string $ranktypeid
 * @return DateTime
 */
function get_last_period_run_awards($courseid, $ranktypeid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid
    );
    return (new DateTime($DB->get_record('block_mt_awards_last_period', $parameters)->period))->format("Y-n-d");
}

/**
 * get last period year run for awards generation
 *
 * @param string $courseid
 * @param string $ranktypeid
 * @return DateTime
 */
function get_last_period_run_awards_year($courseid, $ranktypeid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid
    );
    return (new DateTime($DB->get_record('block_mt_awards_last_period', $parameters)->period))->format("Y");
}

/**
 * get last period month run for awards generation
 *
 * @param string $courseid
 * @param string $ranktypeid
 * @return DateTime
 */
function get_last_period_run_awards_month($courseid, $ranktypeid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid
    );
    return (new DateTime($DB->get_record('block_mt_awards_last_period', $parameters)->period))->format("m");
}

/**
 * returns whether has a last period run for awards generation
 *
 * @param string $courseid
 * @param string $ranktypeid
 * @return boolean
 */
function has_no_last_period_run_awards($courseid, $ranktypeid) {
    global $DB;
    $parameters = array(
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid
    );
    $recordcount = $DB->count_records('block_mt_awards_last_period', $parameters);
    return ($recordcount < 1);
}