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
 * This gets the rank information for a user
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/blocks/mt/includes/determine_rank_name.php");

/**
 * return the rank name
 *
 * @param array $param
 * @return string
 */
function block_mt_get_rank_name($param) {

    switch ($param->rank_type_id) {
        case '1':
            $rankname = block_mt_format_rank_name(get_string('mt_rankings:rank_name_grades', 'block_mt'), $param->rankname);
            break;
        case '3':
            $rankname = get_string('mt_rankings:rank_name_online_time', 'block_mt');
            $rankname = block_mt_determine_overall_or_period_rankname($param, $rankname, 'rank');
            break;
        case '4':
            $rankname = get_string('mt_rankings:rank_name_num_posts', 'block_mt');
            $rankname = block_mt_determine_overall_or_period_rankname($param, $rankname, 'rank');
            break;
        case '5':
            $rankname = get_string('mt_rankings:rank_name_weekly_posts', 'block_mt');
            $rankname = block_mt_determine_overall_or_period_rankname($param, $rankname, 'rank');
            break;
        case '6':
            $rankname = get_string('mt_rankings:rank_name_average_post_rating', 'block_mt');
            $rankname = block_mt_determine_overall_or_period_rankname($param, $rankname, 'rank');
            break;
        case '7':
            $rankname = get_string('mt_rankings:rank_name_time_milestone', 'block_mt');
            if ($param->period_type == RANK_PERIOD_OVERALL) {
                $rankname = block_mt_format_rank_name($rankname, get_string('mt_rankings:rank_name_overall', 'block_mt'));
            } else {
                $milestoneid = block_mt_get_milestone_id($param->gradeid, $param->period_type, $param->courseid);
                $milestonename = block_mt_get_milestone_name($milestoneid, $param->period_type, $param->courseid);
                $rankname = block_mt_format_rank_name($rankname, $milestonename);
            }
            break;
        case '10':
            $rankname = get_string('mt_rankings:rank_name_time_achievements', 'block_mt');
            $rankname = block_mt_determine_overall_or_period_rankname($param, $rankname, 'rank');
            break;
        default:
            break;
    }
    return $rankname;
}