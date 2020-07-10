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
 * This is for the admin settings.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_heading('block_mt/settings_header_config',
    get_string('mt:settings_header', 'block_mt'),
    get_string('mt:settings_description', 'block_mt')));

$settings->add(new admin_setting_configtext('block_mt/ranks_onl_time',
        get_string('mt_rankings:settings_online_time_label', 'block_mt'),
        get_string('mt_rankings:settings_online_time_desc', 'block_mt'),
        get_string('mt_rankings:settings_online_time_value', 'block_mt'),
        '/^[1-9][0-9]*$/'));

$settings->add(new admin_setting_configtext('block_mt/ranks_inactive_time',
        get_string('mt_rankings:settings_inactive_time_label', 'block_mt'),
        get_string('mt_rankings:settings_inactive_time_desc', 'block_mt'),
        get_string('mt_rankings:settings_inactive_time_value', 'block_mt'),
        '/^[1-9][0-9]*$/'));

$settings->add(new admin_setting_configcheckbox('block_mt/ranks_regenerate_all',
    get_string('mt_rankings:settings_regenerate_all', 'block_mt'),
    get_string('mt_rankings:settings_regenerate_all_desc', 'block_mt'), 0));

$settings->add(new admin_setting_configcheckbox('block_mt/awards_regenerate_all',
    get_string('mt_awards:settings_regenerate_all', 'block_mt'),
    get_string('mt_awards:settings_regenerate_all_desc', 'block_mt'), 0));