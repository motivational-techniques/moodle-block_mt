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
 * This gets the rank and award name
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/blocks/mt/includes/determine_rank_name.php");

/**
 * get award name
 * @param integer $awardid
 * @return string
 */
function get_award_name($awardid) {
    switch ($awardid) {
        case GOLD_AWARD_ID:
            $currentaward = get_string('mt_awards:gold', 'block_mt');
            break;
        case SILVER_AWARD_ID:
            $currentaward = get_string('mt_awards:silver', 'block_mt');
            break;
        case BRONZE_AWARD_ID:
            $currentaward = get_string('mt_awards:bronze', 'block_mt');
            break;
        default:
            $currentaward = get_string('mt_awards:no_award_achieved', 'block_mt');
            break;
    }
    return $currentaward;
}

/**
 * get rank name
 * @param array $param
 * @return string
 */
function block_mt_get_rank_name($param) {

    switch ($param->awardid) {
        case '1':
            $rankname = get_string('mt_awards:award_name_grades', 'block_mt') . ' - ' . $param->rankname;
            break;
        case '3':
            $rankname = get_string('mt_awards:award_name_online_time', 'block_mt');
            $rankname = determine_overall_or_period_rankname($param->period_type, $rankname, 'award');
            break;
        case '4':
            $rankname = get_string('mt_awards:award_name_num_posts', 'block_mt');
            $rankname = determine_overall_or_period_rankname($param->period_type, $rankname, 'award');
            break;
        default:
            break;
    }
    return $rankname;
}