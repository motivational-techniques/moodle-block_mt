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
 * This gets or sets the awards settings.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * get awards settings
 * @param integer $fieldvalue
 * @param integer $courseid
 * @return integer
 */
function block_mt_get_awards_settings($fieldvalue, $courseid) {
    global $DB;

    $parameters = array(
        'courseid' => $courseid,
        'setting' => $fieldvalue
    );
    $recordcount = $DB->count_records('block_mt_awards_config', $parameters);
    if ($recordcount > 0) {
        $returnvalue = $DB->get_record('block_mt_awards_config', $parameters)->value;
    } else {
        $returnvalue = get_string($fieldvalue, 'block_mt');
    }
    return $returnvalue;
}

/**
 * set awards settings
 * @param integer $fieldvalue
 * @param integer $courseid
 * @param integer $value
 * @return null
 */
function block_mt_update_awards_settings($fieldvalue, $courseid, $value) {
    global $DB;

    $parameters = array(
        'courseid' => $courseid,
        'setting' => $fieldvalue
    );
    $recordcount = $DB->count_records('block_mt_awards_config', $parameters);
    if ($recordcount > 0) {
        $recordid = $DB->get_record('block_mt_awards_config', $parameters)->id;
        $parameters = array(
            'id' => $recordid,
            'courseid' => $courseid,
            'setting' => $fieldvalue,
            'value' => $value
        );
        $DB->update_record('block_mt_awards_config', $parameters);
    } else {
        $parameters = array(
            'courseid' => $courseid,
            'setting' => $fieldvalue,
            'value' => $value
        );
        $DB->insert_record('block_mt_awards_config', $parameters);
    }
}