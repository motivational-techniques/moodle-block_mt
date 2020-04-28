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
 * This displays the form for student settings
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/formslib.php");

/**
 * class mt_rankings options for form
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class mt_rankings_options extends moodleform {

    /**
     * definitions for page
     */
    public function definition() {
        $mform = & $this->_form;

        // Hidden elements.
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);

        // Add group for text areas.
        $mform->addElement('header', 'displayinfo',
            get_string('mt:options_rankings_text_fields', 'block_mt'));

        // Add display anonymous yes / no option.
        $mform->addElement('selectyesno', 'displayanonymous',
            get_string('mt:options_rankings_display_anonymous', 'block_mt'));
        $mform->setDefault('displayanonymous', 0);

        $this->add_action_buttons();
    }
}