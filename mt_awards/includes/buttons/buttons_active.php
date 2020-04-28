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
 * This is for the active/inactive button
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$active = optional_param ( 'active', 'true', PARAM_STRINGID );
$assignmentid = optional_param ( 'assignmentid', null, PARAM_INT );
$quizid = optional_param ( 'quizid', null, PARAM_INT );
$instanceid = optional_param('instanceid', null, PARAM_INT);

$urlparameters = array (
        'courseid' => $courseid,
        'active' => $active
);
if (! $assignmentid == null) {
    $urlparameters ['assignmentid'] = $assignmentid;
}
if (! $quizid == null) {
    $urlparameters ['quizid'] = $quizid;
}
if (! $instanceid == null) {
    $urlparameters ['instanceid'] = $instanceid;
}

if ($active == 'true') {
    $urlparameters ['active'] = 'false';
    echo html_writer::link ( new moodle_url ( '', $urlparameters ),
        get_string ( 'mt_awards:button_active_inactive', 'block_mt' ), array (
            'id' => 'checkActive'
    ) );
} else {
    $urlparameters ['active'] = 'true';
    echo html_writer::link ( new moodle_url ( '', $urlparameters ),
        get_string ( 'mt_awards:button_active', 'block_mt' ), array (
            'id' => 'checkActive'
    ) );
}