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
 * This gets the current award for a student
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * get the current award
 *
 * @param string $paramuserid
 * @param string $paramcourseid
 * @param string $paramawardname
 * @return string
 */
function get_current_award($paramuserid, $paramcourseid, $paramawardname) {
    global $DB;

    $currentaward = "";
    $parameters = array (
            'userid' => $paramuserid,
            'courseid' => $paramcourseid,
            'award_name' => $paramawardname
    );
    if ($DB->record_exists ( 'block_mt_awards_user', $parameters )) {
        $awardrank = $DB->get_record ( 'block_mt_awards_user', $parameters )->awardid;
        switch ($awardrank) {
            case GOLD_AWARD_ID :
                $currentaward = get_string('mt_awards:gold', 'block_mt');
                break;
            case SILVER_AWARD_ID :
                $currentaward = get_string('mt_awards:silver', 'block_mt');
                break;
            case BRONZE_AWARD_ID :
                $currentaward = get_string('mt_awards:bronze', 'block_mt');
                break;
            default :
                $currentaward = get_string ('mt_awards:no_award_achieved', 'block_mt');
                break;
        }
    } else {
        $currentaward = get_string ( 'mt_awards:no_award_achieved', 'block_mt' );
    }
    return get_string('mt_awards:get_current_award_markup', 'block_mt', $currentaward);
}