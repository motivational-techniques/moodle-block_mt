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
 * This is the settings page for the Assignment grade
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/formslib.php");

/**
 * mt_goals class for assignment grade form
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class mt_goals_form_grade extends moodleform {
    /**
     * defintions
     */
    public function definition() {
        $mform = & $this->_form;

        // Hidden elements.
        $mform->addElement ( 'hidden', 'courseid' );
        $mform->setType ( 'courseid', PARAM_INT );
        $mform->addElement ( 'hidden', 'userid' );
        $mform->setType ( 'userid', PARAM_INT );
        $mform->addElement ( 'hidden', 'assignid' );
        $mform->setType ( 'assignid', PARAM_INT );

        $mform->addElement ( 'text', 'goal', get_string ( 'mt_goals:assign_grade_goal', 'block_mt' ) );
        $mform->setType ( 'goal', PARAM_INT );
        $mform->addRule('goal', get_string ( 'mt_goals:grade_goal_numeric_error', 'block_mt' ),
            'regex', '/^([5-9][0-9]|100)$/', 'client', false, false);

        $mform->addElement('html', '<div id="slider"></div>');

        $this->add_action_buttons ();
    }
}