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
 * This contains the version information
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2020
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_mt';
$plugin->version = 2020071008; // YYYYMMDDHH (year, month, day, 24-hr time).
$plugin->requires = 2017111300; // YYYYMMDDHH (This is a Moodle 3.4, 13 November 2017 release).

$plugin->cron = 60;

$plugin->maturity = MATURITY_BETA;

$plugin->released = 'v3.4-r1.0.0-450';
