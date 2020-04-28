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
 * This is the page for student options
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require(__DIR__ . '/../../config.php');

require_once($CFG->dirroot . '/blocks/mt/mt_options.php');
require_once($CFG->dirroot . '/blocks/mt/includes/admin_settings.php');

global $DB, $OUTPUT, $PAGE, $USER;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT );
require_login ( $courseid );
$userid = $USER->id;

require_capability('block/mt:options', context_course::instance ( $courseid ) );

$coursename = get_course($courseid)->fullname;

$PAGE->set_url ( '/blocks/mt/options.php', array (
    'courseid' => $courseid
) );
$PAGE->set_pagelayout ( 'standard' );
$PAGE->set_heading ( get_string ( 'mt:options_heading', 'block_mt', $coursename ) );

$mtconfig = new mt_options ();

$toform ['courseid'] = $courseid;
$toform ['awards'] = get_module_display ( $courseid, 'awards' );
$toform ['goals'] = get_module_display ( $courseid, 'goals' );
$toform ['p_annotation'] = get_module_display ( $courseid, 'p_annotation' );
$toform ['p_timeline'] = get_module_display ( $courseid, 'p_timeline' );
$toform ['rankings'] = get_module_display ( $courseid, 'rankings' );

$mtconfig->set_data ( $toform );

if ($mtconfig->is_cancelled ()) {
    // Cancelled forms redirect to the course main page.
    $courseurl = new moodle_url ( '/course/view.php', array (
        'id' => $courseid
    ) );
    redirect ( $courseurl );
} else if ($fromform = $mtconfig->get_data ()) {
    // If submit was clicked insert or update the selection and redirect to course main page.

    $courseurl = new moodle_url ( '/course/view.php', array (
        'id' => $courseid
    ) );

    redirect ( $courseurl );
} else {
    // Form didn't validate or this is the first display.
    $site = get_site ();
    echo $OUTPUT->header ();
    $mtconfig->display ();
    echo $OUTPUT->footer ();
}

$settingsnode = $PAGE->settingsnav->add ( get_string ( 'mt:options_settings', 'block_mt' ) );
$editurl = new moodle_url ( '/blocks/mt/view.php', array (
    'courseid' => $courseid
) );
$editnode = $settingsnode->add ( get_string ( 'mt:options_editpage', 'block_mt' ), $editurl );
$editnode->make_active ();