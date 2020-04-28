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
 * This is the form for student settings.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once("{$CFG->libdir}/formslib.php");

/**
 * Form definition for the student options.
 *
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mt_p_timeline_options extends moodleform {

    /**
     * Function to make the form.
     */
    public function definition() {
        $mform = & $this->_form;

        // Hidden elements.
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        // Add group for text areas.
        $mform->addElement('header', 'displayinfo', get_string('mt:options_ptimeline_text_fields', 'block_mt'));

        // Add display anonymous yes / no option.
        $mform->addElement('static', 'description', get_string('mt:options_ptimeline_no_options', 'block_mt'));

        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }
}