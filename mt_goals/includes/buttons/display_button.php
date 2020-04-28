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
 * This is to display a button link
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * display button
 * @param array $urlparams
 * @param array $linkparams
 * @return string
 */
function display_button($urlparams, $linkparams) {
    $querystring = http_build_query ( $urlparams );
    $button = "<a class='add-update-item' href='" . $linkparams->url . "?" . $querystring . "'>";
    $button .= "<div class='add-update-item'>";
    $button .= $linkparams->text;
    $button .= "</div>";
    $button .= "</a>";

    return $button;
}