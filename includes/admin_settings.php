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
 * Functions to set or get whether to display in a module
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Set whether to display in a module
 * @param integer $courseid
 * @param string $modulename
 * @param boolean $setting
 */
function set_module_display($courseid, $modulename, $setting) {
    global $DB;
    $params = array (
            'courseid' => $courseid,
            'module' => $modulename
    );

    if ($DB->record_exists ( 'block_mt_config', $params )) {
        $record = $DB->get_record ( 'block_mt_config', $params );
        $params = array (
                'id' => $record->id,
                'courseid' => $courseid,
                'module' => $modulename,
                'enabled' => $setting
        );
        $DB->update_record ( 'block_mt_config', $params );
    } else {
        $params = array (
                'courseid' => $courseid,
                'module' => $modulename,
                'enabled' => $setting
        );
        $DB->insert_record ( 'block_mt_config', $params );
    }
}

/**
 * Get whether to display in a module
 * @param integer $courseid
 * @param string $modulename
 * @return number
 */
function get_module_display($courseid, $modulename) {
    global $DB;
    $params = array (
            'courseid' => $courseid,
            'module' => $modulename
    );
    if ($DB->record_exists ( 'block_mt_config', $params )) {
        $enabled = $DB->get_record ( 'block_mt_config', $params )->enabled;
    } else {
        $enabled = 0;
    }
    return $enabled;
}