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
 * This displays the list of awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * display link
 *
 * @param array $awardparam
 * @param array $linkparam
 * @param boolean $display
 * @return null
 */
function display_link($awardparam, $linkparam, $display) {
    $urlparams = array (
            'courseid' => $awardparam->courseid
    );
    $querystring = http_build_query ( $urlparams );
    echo "<a class='menu-item' href='" . $linkparam->url . "?" . $querystring . "'>";
    echo "<div class='menu-item'>";
    echo $linkparam->text;
    if ($display) {
        echo '<br />';
        echo get_current_award ( $awardparam->userid, $awardparam->courseid, $awardparam->awardname );
    }
    echo "</div>";
    echo "</a>";
}

/**
 * display with award
 *
 * @param array $linkparam
 * @param array $awardparam
 * @return null
 */
function display_with_award($linkparam, $awardparam) {
    display_link($awardparam, $linkparam,  true);
}

/**
 * display without award
 *
 * @param array $linkparam
 * @param array $awardparam
 * @return null
 */
function display_without_award($linkparam, $awardparam) {
    display_link($awardparam, $linkparam, false);
}