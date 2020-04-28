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
 * This generates the goals for awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Awards process entry
 * @param array $param
 * @return null
 */
function award_process_entry($param) {
    global $DB;

    $parameters = array (
        'courseid' => $param["courseid"],
        'userid' => $param["userid"],
        'awardid' => $param["awardid"]
    );
    $recordcount = $DB->count_records ('block_mt_goals_awards', $parameters);
    if ($recordcount > 0) {
        $parameters ['id'] = $DB->get_field ('block_mt_goals_awards', 'id', $parameters);
        $parameters ['goal'] = $DB->get_field ('block_mt_goals_awards', 'goal', $parameters);
        $parameters ['awardname'] = $param["awardname"];
        $parameters ['award'] = $param["award"];

        if ($parameters ['goal'] <= $param["award"]) {
            $parameters ['achieved'] = true;
        } else {
            $parameters ['achieved'] = false;
        }
        $DB->update_record ('block_mt_goals_awards', $parameters, false);
    }
}

/**
 * Awards generate goals
 * @param string $courseid
 * @return null
 */
function generate_goal_awards($courseid) {
    global $DB;

    $parameters = array(
        'courseid' => $courseid
    );
    $awardlist = $DB->get_records ('block_mt_awards_user', $parameters);
    foreach ($awardlist as &$awardlist) {
        $params = array (
            'courseid' => $courseid,
            'awardid' => $awardlist->id,
            'award' => $awardlist->awardid,
            'userid' => $awardlist->userid,
            'awardname' => $awardlist->award_name
        );
        award_process_entry($params);
    }
}