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
 * This is the main includes for all pages
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined ( 'MOODLE_INTERNAL' ) || die ();

$PAGE->requires->jquery ();
$PAGE->requires->jquery_plugin ( 'ui' );
$PAGE->requires->jquery_plugin ( 'ui-css' );
$PAGE->requires->js ( '/blocks/mt/includes/js/jquery.tablesorter.js', true );
$PAGE->requires->js ( '/blocks/mt/includes/js/jquery.tablesorter.widgets.js', true );
$PAGE->requires->js ( '/blocks/mt/includes/js/jquery.tablesorter.pager.js', true );
$PAGE->requires->js ( '/blocks/mt/includes/js/main.js', true );
if (isset ( $buttonjs )) {
    if ($buttonjs) {
        $PAGE->requires->js ( '/blocks/mt/includes/js/button-ui.js', true );
        $buttonjs = false;
    }
}

$PAGE->requires->css ( '/blocks/mt/includes/css/theme.blue.css', true );
$PAGE->requires->css ( '/blocks/mt/includes/css/jquery.tablesorter.pager.css', true );
$PAGE->requires->css ( '/blocks/mt/includes/css/main.css', true );
$PAGE->requires->css ( '/blocks/mt/includes/css/ui.css', true );