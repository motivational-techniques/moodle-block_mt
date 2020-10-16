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
 * get current period
 * @return string
 */
function block_mt_get_current_period() {
    $period = new stdClass();
    $period->year = date("Y");
    $period->month = date("n");

    return get_string('mt_rankings:generate_rank_period', 'block_mt', $period);
}

/**
 * get current period year month
 * @return string
 */
function block_mt_get_current_period_year_month() {
    $period = new stdClass();
    $period->year = date("Y");
    $period->month = date("n");

    return get_string('mt_rankings:generate_rank_period_year_month', 'block_mt', $period);
}

/**
 * get current date
 * @return DateTime
 */
function block_mt_get_current_date() {
    return date_create(date("Y") . '-' . date("n") . '-01');
}

/**
 * get current start date
 * @return DateTime
 */
function block_mt_get_start_date() {
    return date_create((date("Y") - 1) . '-' . date("n") . '-01');
}