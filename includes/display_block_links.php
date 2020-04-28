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
 * This displays the links for the block
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Diplay a link for the given module, with icon prepended.
 *
 * @param string $courseid
 * @param string $module
 * @param string $url
 * @param string $textstring
 * @param string $iconurl
 * @return string
 */
function display_item_and_icon($courseid, $module, $url, $textstring, $iconurl) {
    $querystring = array (
            'courseid' => $courseid
    );
    $newwindow = array ();
    if (get_module_display($courseid, $module)) {
        $icon = html_writer::empty_tag('img', array(
                'src' => $iconurl,
                'class' => 'icon'
        ));
        return $icon . html_writer::link(
                new moodle_url($url, $querystring),
                get_string($textstring, 'block_mt'), $newwindow);
    }
}


/**
 * Diplay an administration/option link with icon prepended.
 *
 * @param string $courseid
 * @param string $userid
 * @return string
 */
function display_option_item_and_icon($courseid, $userid) {
    $querystring = array (
            'courseid' => $courseid
    );
    $icon = html_writer::empty_tag('img', array(
            'src' => '../pix/i/settings.svg',
            'class' => 'icon'
    ));
    if (user_has_role_assignment($userid, 5)) {
        $url = new moodle_url('/blocks/mt/options.php', $querystring);
        return $icon . html_writer::link($url, get_string('mt:optionspage', 'block_mt'));
    } else {
        $url = new moodle_url('/blocks/mt/admin.php', $querystring);
        return $icon . html_writer::link($url, get_string('mt:adminpage', 'block_mt'));
    }
}

