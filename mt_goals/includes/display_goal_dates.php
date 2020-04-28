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
 * This displays the goal grade in the proper format
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * display goal date
 * @param DateTime $date
 * @return DateTime
 */
function display_goal_date($date) {
    if ($date != "") {
        $date = userdate($date, get_string('strftimedate'));
    }

    return $date;
}

/**
 * days remaining
 * @param string $paramgoaltostart
 * @param DateTime $paramcurrentday
 * @param DateTime $paramstarted
 * @return string
 */
function days_remaining($paramgoaltostart, $paramcurrentday, $paramstarted) {
    $remainingdays = get_string('mt_goals:not_achieved', 'block_mt');
    $startday = new DateTime();
    $goalday = new DateTime();
    $currentday = new DateTime();

    if ($paramstarted) {
        $startday->setTimestamp($paramstarted);
    }
    if ($paramgoaltostart) {
        $goalday->setTimestamp($paramgoaltostart);
    }
    $currentday->setTimestamp($paramcurrentday);

    if ($paramgoaltostart) {
        if ($paramstarted) {
            if ($startday < $goalday) {
                $remainingdays = get_string('mt_goals:achieved', 'block_mt');
            }
        } else {
            if ($currentday <= $goalday) {
                $remainingdays = $currentday->diff($goalday);
                $remainingdays = get_string('mt_goals:days_remaining', 'block_mt', $remainingdays->format('%a'));
            }
        }
    } else {
        $remainingdays = get_string('mt_goals:no_goal_set', 'block_mt');
    }
    return $remainingdays;
}