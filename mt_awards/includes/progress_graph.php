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
 * This displays a progress graph
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * get the current award
 *
 * @param array $param
 * @return null
 */
function display_progress_graph($param) {
    echo "<div id='container'>";
    echo "<div id='topLoader'>";
    echo get_string ( 'mt_awards:progress_graph_award_achieved', 'block_mt', $param->currentaward );
    echo "<br />";

    echo get_string ( 'mt_awards:progress_graph_current', 'block_mt', $param);
    echo "<br />";

    echo get_string ( 'mt_awards:progress_graph_next', 'block_mt', $param );
    echo "<br />";

    echo get_string ( 'mt_awards:progress_graph_next_level', 'block_mt', $param->nextaward );
    echo "</div></div>";

    echo "<script type='text/javascript'>";
    echo "$(function () {";
    echo "var $";
    echo "topLoader = $('#topLoader').percentageLoader({ width: 256, height: 256, controllable: false, progress: ";
    echo get_string ( 'mt_awards:progress_graph_value', 'block_mt', $param );
    echo "});";
    echo "});";
    echo "</script>";
}