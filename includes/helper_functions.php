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
 * These are helper functions
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Checks if MT is enabled for a course.
 * @param integer $courseid
 * @param string $module
 * @return boolean
 */
function block_mt_is_enabled_for_course($courseid, $module) {
    global $DB;
    $isenabled = false;
    // Check to see if module is enabled for course.
    $rankingsenabled = $DB->get_record('block_mt_config', array(
        'courseid' => $courseid,
        'module' => $module
    ));
    if (isset($rankingsenabled->enabled)) {
        if ($rankingsenabled->enabled == '1') {
            $isenabled = true;
        }
    }
    return $isenabled;
}