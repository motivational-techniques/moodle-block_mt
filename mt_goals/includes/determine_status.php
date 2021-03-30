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
 * This determines the goal status for a user
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * achieved goal
 * @param string $goal
 * @param string $grade
 * @return boolean
 */
function block_mt_goals_achieved_goal($goal, $grade) {
    if ($goal) {
        if (intval ( $goal ) <= intval ( $grade )) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

/**
 * determine grade status
 * @param string $goal
 * @param string $grade
 * @return string
 */
function block_mt_goals_determine_grade_status($goal, $grade) {
    $gradestatus = get_string ( 'mt_goals:not_achieved', 'block_mt' );
    if ($goal) {
        if (intval ( $goal ) <= intval ( $grade )) {
            $gradestatus = get_string ( 'mt_goals:achieved', 'block_mt' );
        }
    } else {
        if ($grade) {
            $gradestatus = get_string ( 'mt_goals:no_goal_set', 'block_mt' );
        } else {
            $gradestatus = '';
        }
    }
    return $gradestatus;
}

/**
 * determine grade status achieved
 * @param string $goal
 * @param string $grade
 * @return boolean
 */
function block_mt_goals_determine_grade_status_achieved($goal, $grade) {
    $achieved = false;
    if ($goal) {
        if (intval ( $goal ) <= intval ( $grade )) {
            $achieved = true;
        } else {
            $achieved = false;
        }
    }
    return $achieved;
}

/**
 * determine ranks status
 * @param string $goal
 * @param string $rank
 * @return string
 */
function block_mt_goals_determine_ranks_status($goal, $rank) {
    if ($goal <= $rank) {
        return get_string ( 'mt_goals:achieved', 'block_mt' );
    } else {
        return get_string ( 'mt_goals:not_achieved', 'block_mt' );
    }
}
/**
 * determine award status
 * @param string $award
 * @param string $goal
 * @return string
 */
function block_mt_goals_determine_awards_status($award, $goal) {
    if ($goal >= $award) {
        return get_string ( 'mt_goals:achieved', 'block_mt' );
    } else {
        return get_string ( 'mt_goals:not_achieved', 'block_mt' );
    }
}
/**
 * display status
 * @param string $achieved
 * @return boolean
 */
function block_mt_goals_display_status($achieved) {
    if ($achieved == '1') {
        return get_string ( 'mt_goals:achieved', 'block_mt' );
    } else {
        return get_string ( 'mt_goals:not_achieved', 'block_mt' );
    }
}