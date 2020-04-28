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

global $CFG;

require_once($CFG->dirroot . '/blocks/mt/includes/constants.php');
require_once($CFG->dirroot . '/blocks/mt/includes/determine_active.php');
require_once($CFG->dirroot . "/blocks/mt/includes/get_period.php");
require_once($CFG->dirroot . '/blocks/mt/includes/display_table_header_months.php');
require_once($CFG->dirroot . '/blocks/mt/includes/get_user_name.php');
require_once($CFG->dirroot . '/blocks/mt/includes/display_no_records.php');