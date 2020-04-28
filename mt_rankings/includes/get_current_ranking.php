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
 * This returns the current ranking
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined ( 'MOODLE_INTERNAL' ) || die ();

/**
 * get current ranking
 * @param array $param
 * @return string
 */
function get_current_ranking($param) {
    global $DB;

    $parameters = array(
        'userid' => $param->userid,
        'courseid' => $param->courseid,
        'rank_type_id' => $param->ranktype,
        'period' => $param->period,
        'period_type' => $param->period_type,
        'active' => 1
    );

    if ($param->period_type == RANK_PERIOD_OVERALL) {
        $parameters['period'] = null;
    }
    if ($param->gradeid != '') {
        $parameters['gradeid'] = $param->gradeid;
    }
    if ($DB->record_exists('block_mt_ranks_user', $parameters)) {
        $rankingrank = $DB->get_record('block_mt_ranks_user', $parameters)->rank_active;
        return get_string('mt_rankings:get_current_ranking', 'block_mt') . $rankingrank;
    } else {
        return get_string('mt_rankings:get_current_ranking_not', 'block_mt');
    }
}