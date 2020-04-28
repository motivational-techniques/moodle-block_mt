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
 * This determines if a student is active
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * display active
 * @param array $paramactive
 * @return string
 */
function display_active_flag($paramactive) {
    if ($paramactive == '1') {
        return 'x';
    } else {
        return '';
    }
}

/**
 * determine if a user is active
 * @param string $userid
 * @param string $courseid
 * @return boolean
 */
function is_active($userid, $courseid) {
    global $DB;

    $params = array(
        'courseid' => $courseid,
        'userid' => $userid
    );
    if ($DB->record_exists('block_mt_active_users', $params)) {
        $active = $DB->get_record('block_mt_active_users', $params);
        if ($active->active == '1') {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}