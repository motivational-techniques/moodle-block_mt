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
 * This updates the last period that the rankings were run
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * if there is an entry period run for ranks
 * @param string $courseid
 * @param string $ranktypeid
 * @return string
 */
function has_no_last_period_run_ranks($courseid, $ranktypeid) {
    global $DB;

    $recordcountparams = array (
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid
    );
    $recordcount = $DB->count_records ( 'block_mt_ranks_last_per', $recordcountparams );
    return ($recordcount < 1);
}

/**
 * get last an entry period run for ranks
 * @param string $courseid
 * @param string $ranktypeid
 * @return string
 */
function get_last_period_run_ranks($courseid, $ranktypeid) {
    global $DB;

    $lastperiodparams = array (
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid
    );
    return new DateTime ($DB->get_record('block_mt_ranks_last_per', $lastperiodparams)->period);
}

/**
 * get last period year run for ranks generation
 *
 * @param string $courseid
 * @param string $ranktypeid
 * @return DateTime
 */
function get_last_period_run_ranks_year($courseid, $ranktypeid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid
    );
    return (new DateTime($DB->get_record('block_mt_ranks_last_per', $parameters)->period))->format("Y");
}

/**
 * get last period month run for ranks generation
 *
 * @param string $courseid
 * @param string $ranktypeid
 * @return DateTime
 */
function get_last_period_run_ranks_month($courseid, $ranktypeid) {
    global $DB;
    $parameters = array (
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid
    );
    return (new DateTime($DB->get_record('block_mt_ranks_last_per', $parameters)->period))->format("m");
}

/**
 * update last period run for ranks
 * @param string $courseid
 * @param string $ranktypeid
 */
function update_last_period_run_ranks($courseid, $ranktypeid) {
    global $DB;

    if (! isset($period)) {
        $period = new stdClass();
        $period->year = date("Y");
        $period->month = date("n");
        $period->period = get_string('mt_rankings:generate_rank_period', 'block_mt', $period);
    }
    $recordcount = $DB->count_records('block_mt_ranks_last_per',
        array(
            'courseid' => $courseid,
            'rank_type_id' => $ranktypeid));

    if ($recordcount < 1) {
        $DB->insert_record('block_mt_ranks_last_per',
            array(
                'courseid' => $courseid,
                'rank_type_id' => $ranktypeid,
                'period' => $period->period));
    } else {
        $id = $DB->get_field('block_mt_ranks_last_per', 'id',
            array(
                'courseid' => $courseid,
                'rank_type_id' => $ranktypeid));
            $DB->update_record('block_mt_ranks_last_per',
                array(
                    'id' => $id,
                    'courseid' => $courseid,
                    'rank_type_id' => $ranktypeid,
                    'period' => $period->period));
    }
}