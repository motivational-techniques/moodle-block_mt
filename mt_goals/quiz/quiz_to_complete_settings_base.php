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
 * This is the settings page for the quiz to complete
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
// @codingStandardsIgnoreLine
require_once("{$CFG->libdir}/formslib.php");

/**
 * mt_goals class for quiz complete form
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class mt_goals_quiz_complete_form extends moodleform {
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
        $mform->addElement ( 'hidden', 'quizid' );
        $mform->setType ( 'quizid', PARAM_INT );

        $mform->addElement ( 'text', 'datepicker', get_string ( 'mt_goals:quiz_complete_complete_label', 'block_mt' ) );
        $mform->setType ( 'datepicker', PARAM_TEXT );

        $this->add_action_buttons ();
    }
}