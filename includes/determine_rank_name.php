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
 * This determines the rank name
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Formats the rank name and parameter
 *
 * @param string $param
 * @param string $paramrankname
 * @param string $stringname
 * @return string
 */
function determine_overall_or_period_rankname($param, $paramrankname, $stringname) {
    if ($stringname == 'rank') {
        $overallrankname = 'mt_rankings:rank_name_overall';
    } else if ($stringname == 'award') {
        $overallrankname = 'mt_awards:award_name_overall';
    } else if ($stringname == 'goal') {
        $overallrankname = 'mt_goals:overall';
    } else {
        $overallrankname = 'mt_rankings:rank_name_overall';
    }

    if ($param->period_type == RANK_PERIOD_OVERALL) {
        $returnrankname = format_rank_name($paramrankname, get_string($overallrankname, 'block_mt'));
    } else {
        $returnrankname = format_rank_name($paramrankname, DateTime::createFromFormat('Y-n-d', $param->period)->format('F Y'));
    }
    return $returnrankname;
}

/**
 * Formats the rank name and parameter
 *
 * @param string $rankname
 * @param array $parameter
 * @return string
 */
function format_rank_name($rankname, $parameter) {
    $ranknameandparam = new stdClass ();
    $ranknameandparam->rankname = $rankname;
    $ranknameandparam->parameter = $parameter;

    return get_string('mt_rankings:get_rank_format', 'block_mt', $ranknameandparam);
}