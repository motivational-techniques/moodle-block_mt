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
 * This is the page for student options
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/formslib.php");

/**
 * This is the class for the options form
 *
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class mt_options extends moodleform
{
    /**
     * Definitions for the form
     */
    public function definition() {
        $mform = & $this->_form;

        global $COURSE;

        $courseid = $COURSE->id;

        // Hidden elements.
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);

        // Add group for text areas.
        $mform->addElement('header', 'displayinfo', get_string('mt:options_pages_heading', 'block_mt'));
        $mform->addElement('static', 'description', '', get_string('mt:options_pages_text', 'block_mt'));

        $querystring = array(
            'courseid' => $courseid
        );

        if (block_mt_get_module_display($courseid, 'awards')) {
            $link = html_writer::link(
                new moodle_url('/blocks/mt/mt_awards/options.php', $querystring),
                get_string('mt:options_awards', 'block_mt'));
            $mform->addElement('static', 'awards_option', '', $link);
        }
        if (block_mt_get_module_display($courseid, 'goals')) {
            $link = html_writer::link(
                new moodle_url('/blocks/mt/mt_goals/options.php', $querystring),
                get_string('mt:options_goals', 'block_mt'));
            $mform->addElement('static', 'goals_option', '', $link);
        }
        if (block_mt_get_module_display($courseid, 'p_annotation')) {
            $link = html_writer::link(
                new moodle_url('/blocks/mt/mt_p_annotation/options.php', $querystring),
                get_string('mt:options_p_annotation', 'block_mt'));
            $mform->addElement('static', 'pa_option', '', $link);
        }
        if (block_mt_get_module_display($courseid, 'p_timeline')) {
            $link = html_writer::link(
                new moodle_url('/blocks/mt/mt_p_timeline/options.php', $querystring),
                get_string('mt:options_p_timeline', 'block_mt'));
            $mform->addElement('static', 'pt_option', '', $link);
        }
        if (block_mt_get_module_display($courseid, 'rankings')) {
            $link = html_writer::link(
                new moodle_url('/blocks/mt/mt_rankings/options.php', $querystring),
                get_string('mt:options_rankings', 'block_mt'));
            $mform->addElement('static', 'rankings_option', '', $link);
        }
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }
}