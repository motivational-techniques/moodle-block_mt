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
 * This generates the goals
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
// @codingStandardsIgnoreLine
global $CFG;

// @codingStandardsIgnoreLine
require_once($CFG->dirroot . "/config.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/includes/get_name.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/includes/get_time_achieved.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/includes/get_grades.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/includes/determine_achieved.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/includes/update_grades.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/includes/determine_status.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/includes/determine_submitted.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/includes/get_quiz_start_time.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/includes/get_quiz_completed_time.php");

require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/quiz_grade.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/quiz_start.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/quiz_complete.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/assign_grade.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/assign_complete.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/overall_grade.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/rankings.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/awards.php");

require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/all_goals.php");
require_once($CFG->dirroot . "/blocks/mt/mt_goals/generate/achieved.php");
require_once($CFG->dirroot . "/blocks/mt/includes/helper_functions.php");
require_once($CFG->dirroot . "/blocks/mt/includes/get_quiz_list.php");
require_once($CFG->dirroot . "/blocks/mt/includes/constants.php");

/**
 * Generate goals
 * @return null
 */
function generate_goals() {
    $coursedata = get_courses();
    foreach ($coursedata as $course) {
        if (block_mt_is_enabled_for_course($course->id, 'rankings')) {
            mtrace(get_string('mt:cron_goals_main', 'block_mt', $course->id));

            mtrace(get_string('mt:cron_goals_achieved_all', 'block_mt'));
            generate_achieved_all($course->id);

            mtrace(get_string('mt:cron_goals_overall_grade', 'block_mt'));
            generate_goal_overall_grade($course->id);

            mtrace(get_string('mt:cron_goals_quiz_start', 'block_mt'));
            generate_goal_quiz_start($course->id);
            mtrace(get_string('mt:cron_goals_quiz_complete', 'block_mt'));
            generate_goal_quiz_complete($course->id);
            mtrace(get_string('mt:cron_goals_quiz_grade', 'block_mt'));
            generate_goal_quiz_grade($course->id);

            mtrace(get_string('mt:cron_goals_assign_complete', 'block_mt'));
            generate_goal_assign_complete($course->id);
            mtrace(get_string('mt:cron_goals_assign_grade', 'block_mt'));
            generate_goal_assign_grade($course->id);

            mtrace(get_string('mt:cron_goals_ranking', 'block_mt'));
            generate_goal_rankings($course->id);
            mtrace(get_string('mt:cron_goals_award', 'block_mt'));
            generate_goal_awards($course->id);

            mtrace(get_string('mt:cron_goals_all', 'block_mt'));
            generate_goal_all($course->id);
        }
    }
}

$starttime = microtime();

mtrace(get_string('mt:cron_goals_start', 'block_mt'));

generate_goals();

mtrace(get_string('mt:cron_goals_end', 'block_mt', microtime_diff($starttime, microtime())));