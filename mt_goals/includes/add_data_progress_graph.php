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
 * This adds data for the course progress
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * add data progress graph
 * @param array $params
 * @return string
 */
function add_data_progress_graph($params) {
    $temp = array();
    $temp[] = array (
        'v' => get_string('mt_goals:course_progress_name_goal', 'block_mt', $params),
        'f' => null
    );
    $temp[] = array(
        'v' => $params->goal,
        'f' => null
    );
    $temp[] = array(
        'v' => $params->progress,
        'f' => null
    );
    return array (
        'c' => $temp
    );
}