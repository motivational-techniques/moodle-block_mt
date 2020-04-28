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
 * This is to define constants.
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('DB_TYPE_POSTGRES', 'pgsql');
define('DB_TYPE_MYSQL', 'mysqli');
define('DB_TYPE_MARIA', 'mariadb');
define('DB_TYPE_SQLSRV', 'sqlsrv');

define('GOLD_AWARD_ID', '1');
define('SILVER_AWARD_ID', '2');
define('BRONZE_AWARD_ID', '3');
define('NO_AWARD_ID', '0');

define('RANK_PERIOD_OVERALL', 'overall');
define('RANK_PERIOD_MONTHLY', 'monthly');
define('RANK_PERIOD_INDIVIDUAL', 'individual');

define('ITEM_MODULE_QUIZ', 'quiz');
define('ITEM_MODULE_ASSIGN', 'assign');
define('ITEM_TYPE_COURSE', 'course');

define('RANK_TYPE_GRADES', '1');
define('RANK_TYPE_ONLINE_TIME', '3');
define('RANK_TYPE_NUMBER_POSTS', '4');
define('RANK_TYPE_WEEKLY_POSTS', '5');
define('RANK_TYPE_POST_RATING', '6');
define('RANK_TYPE_MILESTONE', '7');
define('RANK_TYPE_ACHIEVEMENT', '10');

define('GOAL_TYPE_GRADES', '1');
define('GOAL_TYPE_TIME', '2');
define('GOAL_TYPE_RANKS', '3');
define('GOAL_TYPE_AWARDS', '4');