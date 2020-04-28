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
 * Tasks for generating the data for the Motivational Techniques.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace block_mt\task;

defined('MOODLE_INTERNAL') || die();

/**
 * This is the cron tasks
 *
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class mt_cron_task extends \core\task\scheduled_task {
    /**
     * get the name of the cron task
     *
     * @return string
     */
    public function get_name() {
        return get_string('mt:cron_task', 'block_mt');
    }

    /**
     * execute the cron tasks
     */
    public function execute() {
        global $CFG;
        // @codingStandardsIgnoreLine
        require_once($CFG->dirroot . "/config.php");

        $starttime = microtime();
        mtrace(get_string('mt:cron_task_start', 'block_mt'));

        require($CFG->dirroot . "/blocks/mt/mt_rankings/generate/generate_rankings.php");
        require($CFG->dirroot . "/blocks/mt/mt_awards/generate/generate_awards.php");
        require($CFG->dirroot . "/blocks/mt/mt_goals/generate/generate_goals.php");

        mtrace(get_string('mt:cron_task_end', 'block_mt', microtime_diff($starttime, microtime())));
    }
}