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
 * This is the library of common functions for progress annotation.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Called to populate the mt_instancenames table with module names and ids or
 * retrieve an array of module names keyed on module id.
 *
 * @param int $courseid The course id value
 * @param boolean $updatedb Flag to update the DB table
 * @return array
 */
function populate_instancenames($courseid, $updatedb = false) {
    global $DB, $PAGE, $USER;

    // Build the instancenames records for this course and insert into DB.
    // Get the course and renderer.
    $course = $DB->get_record('course', array('id' => $courseid), "*", MUST_EXIST);
    $renderer = $PAGE->get_renderer('core', 'course');

    // Get the course module information.
    $modinfo = get_fast_modinfo($course);
    $modnames = []; $mods = [];

    // Extract the module links.
    foreach ($modinfo->sections as $section) {

        foreach ($section as $cmid) {
            $cm = $modinfo->cms[$cmid];

            // Ignore non-visible modules.
            if (!$cm->has_view() || !$cm->uservisible) {
                continue;
            }
            // Some modules are not activityinstance classes, but will not
            // have that class with it yet, test for stealth class instead.
            $name = $renderer->course_section_cm_name($cm);
            if (strpos($name, 'stealth') !== false) {
                continue;
            } else if (strpos($name, 'inplaceeditable') !== false) {
                // Editing mode is on, activities will have edit icon in name
                // which encloses the name in a wrapper. Remove the wrapper.
                $opentag = strpos($name, '<a ');
                $name = substr($name, $opentag);
                $closetag = strpos($name, '</a>') + 4;
                $name = substr($name, 0, $closetag);
            }

            $modnames[$cmid] = $name;
            $mods[$cmid] = $cmid;
        }
    }

    if (!$updatedb) {
        return $modnames;
    }

    // Get any records from the DB.
    $records = $DB->get_records('block_mt_instancenames', array('courseid' => $courseid), "instanceid");

    // Ensure records in table match those on course page.
    foreach ($records as $record) {
        // Delete record from table if activity has been removed from course.
        if (! isset($modnames[$record->instanceid])) {
            $DB->delete_records('block_mt_instancenames', array('id' => $record->id));
            $DB->delete_records('block_mt_annotation', array(
                'course' => $courseid,
                'userid' => $USER->id,
                'object' => $record->instanceid
            ));
            continue;
        }
        // Remove this activity, it is already in the DB.
        unset($mods[$record->instanceid]);
    }

    // Any leftover activities are not in the DB table.
    $courseinfo = [];
    foreach ($mods as $key => $value) {
        $courseinfo[] = (object) array(
            'courseid'   => $courseid,
            'instanceid' => $key
        );
    }
    // ...put them there.
    if (count($courseinfo) > 0) {
        $DB->insert_records('block_mt_instancenames', $courseinfo);
    }

    return $modnames;
}
