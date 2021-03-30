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
 * This gets the assignment final grade
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * get assign final grade
 * @param string $assignid
 * @param string $userid
 * @return integer
 */
function block_mt_goals_get_assign_final_grade($assignid, $userid) {
    global $DB;

    $finalgradeparams = array (
            'itemid' => $assignid,
            'userid'  => $userid
    );
    $assignfinalgrade = null;
    if ($DB->record_exists ( 'grade_grades', $finalgradeparams)) {
        $assigngrade = $DB->get_record ( 'grade_grades', $finalgradeparams );
        if ($assigngrade->finalgrade != null) {
            $assignfinalgrade = ($assigngrade->finalgrade / $assigngrade->rawgrademax ) * 100;
        }
    }
    return $assignfinalgrade;
}