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
 * This displays the award text
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/blocks/mt/includes/constants.php');

/**
 * get award text
 * @param string $id
 * @return string
 */
function get_award_text($id) {
    switch ($id) {
        case GOLD_AWARD_ID :
            return get_string('mt_goals:award_gold', 'block_mt');
        case SILVER_AWARD_ID :
            return get_string('mt_goals:award_silver', 'block_mt');
        case BRONZE_AWARD_ID :
            return get_string('mt_goals:award_bronze', 'block_mt');
        default :
            return "";
    }
}