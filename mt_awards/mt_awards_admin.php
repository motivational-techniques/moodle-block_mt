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
 * This is the form for teach options
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/formslib.php");

/**
 * mt_awards class for admin form
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class mt_awards_form extends moodleform {
    /**
     * defintions
     */
    public function definition() {
        $mform = & $this->_form;

        // Hidden elements.
        $mform->addElement ( 'hidden', 'courseid' );
        $mform->setType ( 'courseid', PARAM_INT );

        // Group for Grades.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_grades_count',
            'block_mt' ) );

        $mform->addElement ( 'text', 'grades_gold_count', get_string ( 'mt_awards:grades_gold_count_label',
            'block_mt' ) );
        $mform->setType ( 'grades_gold_count', PARAM_INT );
        $mform->addRule('grades_gold_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'grades_silver_count', get_string ( 'mt_awards:grades_silver_count_label',
            'block_mt' ) );
        $mform->setType ( 'grades_silver_count', PARAM_INT );
        $mform->addRule('grades_silver_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'grades_bronze_count', get_string ( 'mt_awards:grades_bronze_count_label',
            'block_mt' ) );
        $mform->setType ( 'grades_bronze_count', PARAM_INT );
        $mform->addRule('grades_bronze_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        // Group for Online Time.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_time_online',
            'block_mt' ) );

        $mform->addElement ( 'text', 'online_time_gold_weight', get_string ( 'mt_awards:time_online_gold_weight_label',
            'block_mt' ) );
        $mform->setType ( 'online_time_gold_weight', PARAM_INT );
        $mform->addRule('online_time_gold_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'online_time_silver_weight', get_string ( 'mt_awards:time_online_silver_weight_label',
            'block_mt' ) );
        $mform->setType ( 'online_time_silver_weight', PARAM_INT );
        $mform->addRule('online_time_silver_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'online_time_bronze_weight', get_string ( 'mt_awards:time_online_bronze_weight_label',
            'block_mt' ) );
        $mform->setType ( 'online_time_bronze_weight', PARAM_INT );
        $mform->addRule('online_time_bronze_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        // Group for Number of Posts counts.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_num_posts_count',
            'block_mt' ) );

        $mform->addElement ( 'text', 'num_posts_gold_count', get_string ( 'mt_awards:num_posts_gold_count_label',
            'block_mt' ) );
        $mform->setType ( 'num_posts_gold_count', PARAM_INT );
        $mform->addRule('num_posts_gold_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'num_posts_silver_count', get_string ( 'mt_awards:num_posts_silver_count_label',
            'block_mt' ) );
        $mform->setType ( 'num_posts_silver_count', PARAM_INT );
        $mform->addRule('num_posts_silver_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'num_posts_bronze_count', get_string ( 'mt_awards:num_posts_bronze_count_label',
            'block_mt' ) );
        $mform->setType ( 'num_posts_bronze_count', PARAM_INT );
        $mform->addRule('num_posts_bronze_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        // Group for Number of Posts weight.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_num_posts_weight',
            'block_mt' ) );

        $mform->addElement ( 'text', 'num_posts_gold_weight', get_string ( 'mt_awards:num_posts_gold_weight_label',
            'block_mt' ) );
        $mform->setType ( 'num_posts_gold_weight', PARAM_INT );
        $mform->addRule('num_posts_gold_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'num_posts_silver_weight', get_string ( 'mt_awards:num_posts_silver_weight_label',
            'block_mt' ) );
        $mform->setType ( 'num_posts_silver_weight', PARAM_INT );
        $mform->addRule('num_posts_silver_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'num_posts_bronze_weight', get_string ( 'mt_awards:num_posts_bronze_weight_label',
            'block_mt' ) );
        $mform->setType ( 'num_posts_bronze_weight', PARAM_INT );
        $mform->addRule('num_posts_bronze_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        // Group for Percentage of Read Posts.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_read_posts_count',
            'block_mt' ) );

        $mform->addElement ( 'text', 'read_posts_gold_count', get_string ( 'mt_awards:read_posts_gold_count_label',
            'block_mt' ) );
        $mform->setType ( 'read_posts_gold_count', PARAM_INT );
        $mform->addRule('read_posts_gold_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'read_posts_silver_count', get_string ( 'mt_awards:read_posts_silver_count_label',
            'block_mt' ) );
        $mform->setType ( 'read_posts_silver_count', PARAM_INT );
        $mform->addRule('read_posts_silver_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'read_posts_bronze_count', get_string ( 'mt_awards:read_posts_bronze_count_label',
            'block_mt' ) );
        $mform->setType ( 'read_posts_bronze_count', PARAM_INT );
        $mform->addRule('read_posts_bronze_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        // Group for Number of Posts weight.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_read_posts_weight',
            'block_mt' ) );

        $mform->addElement ( 'text', 'read_posts_gold_weight', get_string ( 'mt_awards:read_posts_gold_weight_label',
            'block_mt' ) );
        $mform->setType ( 'read_posts_gold_weight', PARAM_INT );
        $mform->addRule('read_posts_gold_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'read_posts_silver_weight', get_string ( 'mt_awards:read_posts_silver_weight_label',
            'block_mt' ) );
        $mform->setType ( 'read_posts_silver_weight', PARAM_INT );
        $mform->addRule('read_posts_silver_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'read_posts_bronze_weight', get_string ( 'mt_awards:read_posts_bronze_weight_label',
            'block_mt' ) );
        $mform->setType ( 'read_posts_bronze_weight', PARAM_INT );
        $mform->addRule('read_posts_bronze_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        // Group for Percentage of Ranking Posts.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_rating_posts_count',
            'block_mt' ) );

        $mform->addElement ( 'text', 'rating_posts_gold_count', get_string ( 'mt_awards:rating_posts_gold_count_label',
            'block_mt' ) );
        $mform->setType ( 'rating_posts_gold_count', PARAM_INT );
        $mform->addRule('rating_posts_gold_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'rating_posts_silver_count', get_string ( 'mt_awards:rating_posts_silver_count_label',
            'block_mt' ) );
        $mform->setType ( 'rating_posts_silver_count', PARAM_INT );
        $mform->addRule('rating_posts_silver_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'rating_posts_bronze_count', get_string ( 'mt_awards:rating_posts_bronze_count_label',
            'block_mt' ) );
        $mform->setType ( 'rating_posts_bronze_count', PARAM_INT );
        $mform->addRule('rating_posts_bronze_count', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        // Group for Ranking of Posts weight.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_rating_posts_weight',
            'block_mt' ) );

        $mform->addElement ( 'text', 'rating_posts_gold_weight', get_string ( 'mt_awards:rating_posts_gold_weight_label',
            'block_mt' ) );
        $mform->setType ( 'rating_posts_gold_weight', PARAM_INT );
        $mform->addRule('rating_posts_gold_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'rating_posts_silver_weight', get_string ( 'mt_awards:rating_posts_silver_weight_label',
            'block_mt' ) );
        $mform->setType ( 'rating_posts_silver_weight', PARAM_INT );
        $mform->addRule('rating_posts_silver_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'rating_posts_bronze_weight', get_string ( 'mt_awards:rating_posts_bronze_weight_label',
            'block_mt' ) );
        $mform->setType ( 'rating_posts_bronze_weight', PARAM_INT );
        $mform->addRule('rating_posts_bronze_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        // Group for awards milestones.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_milestones_weight',
            'block_mt' ) );

        $mform->addElement ( 'text', 'milestones_gold_weight', get_string ( 'mt_awards:milestones_gold_days_label',
            'block_mt' ) );
        $mform->setType ( 'milestones_gold_weight', PARAM_INT );
        $mform->addRule('milestones_gold_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'milestones_silver_weight', get_string ( 'mt_awards:milestones_silver_days_label',
            'block_mt' ) );
        $mform->setType ( 'milestones_silver_weight', PARAM_INT );
        $mform->addRule('milestones_silver_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'milestones_bronze_weight', get_string ( 'mt_awards:milestones_bronze_days_label',
            'block_mt' ) );
        $mform->setType ( 'milestones_bronze_weight', PARAM_INT );
        $mform->addRule('milestones_bronze_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        // Group for Ranking of Achievements.
        $mform->addElement ( 'header', 'displayinfo', get_string ( 'mt_awards:header_achievements_weight',
            'block_mt' ) );

        $mform->addElement ( 'text', 'achievements_gold_weight', get_string ( 'mt_awards:achievements_gold_weight_label',
            'block_mt' ) );
        $mform->setType ( 'achievements_gold_weight', PARAM_INT );
        $mform->addRule('achievements_gold_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'achievements_silver_weight', get_string ( 'mt_awards:achievements_silver_weight_label',
            'block_mt' ) );
        $mform->setType ( 'achievements_silver_weight', PARAM_INT );
        $mform->addRule('achievements_silver_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $mform->addElement ( 'text', 'achievements_bronze_weight', get_string ( 'mt_awards:achievements_bronze_weight_label',
            'block_mt' ) );
        $mform->setType ( 'achievements_bronze_weight', PARAM_INT );
        $mform->addRule('achievements_bronze_weight', '[0-100]', 'regex', '/^([0-9]{1,2}|100)$/', 'client', true);

        $this->add_action_buttons ();
    }
}