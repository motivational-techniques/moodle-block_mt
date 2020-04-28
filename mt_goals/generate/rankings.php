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
 * This generates the goals for rankings
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Rankings process entry
 * @param array $param
 * @return null
 */
function ranking_process_entry($param) {
    global $DB;

    $parameters = array (
        'courseid' => $param["courseid"],
        'userid' => $param["userid"],
        'ranktypeid' => $param["rankid"]
    );
    $recordcount = $DB->count_records ( 'block_mt_goals_ranks', $parameters );
    if ($recordcount > 0) {
        $parameters ['id'] = $DB->get_field ( 'block_mt_goals_ranks', 'id', $parameters );
        $parameters ['goal'] = $DB->get_field ( 'block_mt_goals_ranks', 'goal', $parameters );
        $parameters ['rank'] = $param["rank"];
        if ($parameters ['goal'] >= $param["rank"]) {
            $parameters ['achieved'] = true;
        } else {
            $parameters ['achieved'] = false;
        }
        $DB->update_record ( 'block_mt_goals_ranks', $parameters, false );
    }
}

/**
 * Rankings generate goals
 * @param string $courseid
 * @return null
 */
function generate_goal_rankings($courseid) {
    global $DB;

    $parameters = array(
        'courseid' => $courseid
    );
    $rankinglist = $DB->get_records ( 'block_mt_ranks_user', $parameters);
    foreach ($rankinglist as $ranking) {
        $params = array (
            'courseid' => $courseid,
            'rankid' => $ranking->id,
            'userid' => $ranking->userid,
            'rank' => $ranking->rank
        );
        ranking_process_entry($params);
    }
}