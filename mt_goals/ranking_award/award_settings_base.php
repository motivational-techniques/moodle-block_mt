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
 * This is the goal settings page for the awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
// @codingStandardsIgnoreLine
require_once("{$CFG->libdir}/formslib.php");

/**
 * mt_goals class for award settings form
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class mt_goals_award_form extends moodleform {
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
        $mform->addElement ( 'hidden', 'awardid' );
        $mform->setType ( 'awardid', PARAM_INT );

        $radioarray = array();
        $radioarray[] = $mform->createElement('radio', 'goal', '', get_string('mt_goals:award_gold', 'block_mt'), 1);
        $radioarray[] = $mform->createElement('radio', 'goal', '', get_string('mt_goals:award_silver', 'block_mt'), 2);
        $radioarray[] = $mform->createElement('radio', 'goal', '', get_string('mt_goals:award_bronze', 'block_mt'), 3);
        $mform->addGroup($radioarray, 'radioar', '', array(' '), false);
        $mform->setDefault('radio', 0);

        $this->add_action_buttons ();
    }
}