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
 * This determines if an assignment or quiz was submitted
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * assignment submitted
 * @param string $userid
 * @param string $assignmentid
 * @return boolean
 */
function assignment_submitted($userid, $assignmentid) {
    global $DB;
    $returnvalue = false;
    $sql = "SELECT {assign_submission}.*
        FROM {assign_submission}
        join {assign_grades}
        on ({assign_submission}.userid={assign_grades}.userid)
        and ({assign_submission}.assignment = {assign_grades}.assignment)
        where {assign_submission}.userid=:userid
        and {assign_submission}.assignment=:assignmentid";
    $parameters = array(
            'userid' => $userid,
            'assignmentid' => $assignmentid
    );
    $submitted = $DB->get_record_sql($sql, $parameters);
    if ($submitted) {
        if ($submitted->status == 'submitted') {
            $returnvalue = true;
        }
    }
    return $returnvalue;
}