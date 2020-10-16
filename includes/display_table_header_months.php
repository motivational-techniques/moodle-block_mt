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
 * This displays the table header
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate table header months
 *
 * @param string $tableheader
 * @return string
 */
function block_mt_generate_table_header_months($tableheader) {
    $previousmonths = block_mt_get_current_date();
    for ($monthcounter = 12; $monthcounter > 0; $monthcounter --) {
        $tableheader[] = $previousmonths->format('F Y');
        $previousmonths->modify('previous month');
    }
    return $tableheader;
}