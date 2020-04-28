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

/** This is the admin page for teacher options
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/formslib.php");

/**
 * mt_ranks class for admin form
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class mt_ranks_form extends moodleform {
    /**
     * defintions
     */
    public function definition() {
        $mform = & $this->_form;

        // Hidden elements.
        $mform->addElement('hidden', 'courseid');

        // Add group for text areas.
        $mform->addElement('header', 'displayinfo',
            get_string('mt_rankings:admin_text_fields', 'block_mt'));

        $mform->addElement('static', 'description',
            get_string('mt_rankings:admin_no_options', 'block_mt'));

        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }
}