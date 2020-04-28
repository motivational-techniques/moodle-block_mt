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
 * This is the admin page for teacher options
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
 * This is the definition for the administration
 *
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class mt_admin extends moodleform
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

        // Add group for text areas.
        $mform->addElement('header', 'displayinfo', get_string('mt:admin_block_enable_heading', 'block_mt'));
        $mform->addElement('static', 'description', '', get_string('mt:admin_block_enable_text', 'block_mt'));

        $mform->addElement('advcheckbox', 'awards', get_string('mt:awards', 'block_mt'), '', array(
            'group' => 1
        ), array(
            0,
            1
        ));
        $mform->addElement('advcheckbox', 'goals', get_string('mt:goals', 'block_mt'), '', array(
            'group' => 1
        ), array(
            0,
            1
        ));
        $mform->addElement('advcheckbox', 'p_annotation', get_string('mt:p_annotation', 'block_mt'), '', array(
            'group' => 1
        ), array(
            0,
            1
        ));
        $mform->addElement('advcheckbox', 'p_timeline', get_string('mt:p_timeline', 'block_mt'), '', array(
            'group' => 1
        ), array(
            0,
            1
        ));
        $mform->addElement('advcheckbox', 'rankings', get_string('mt:rankings', 'block_mt'), '', array(
            'group' => 1
        ), array(
            0,
            1
        ));

        // Add group for text areas.
        $mform->addElement('header', 'displayinfo', get_string('mt:admin_admin_pages_heading', 'block_mt'));
        $mform->addElement('static', 'description', '', get_string('mt:admin_admin_pages_text', 'block_mt'));

        $querystring = array(
            'courseid' => $courseid
        );
        $link = html_writer::link(
            new moodle_url('/blocks/mt/mt_awards/admin.php', $querystring),
            get_string('mt:admin_awards', 'block_mt'));
        $mform->addElement('static', 'awards_admin', '', $link);
        $link = html_writer::link(
            new moodle_url('/blocks/mt/mt_goals/admin.php', $querystring),
            get_string('mt:admin_goals', 'block_mt'));
        $mform->addElement('static', 'goals_admin', '', $link);
        $link = html_writer::link(
            new moodle_url('/blocks/mt/mt_p_annotation/admin.php', $querystring),
            get_string('mt:admin_p_annotation', 'block_mt'));
        $mform->addElement('static', 'pa_admin', '', $link);
        $link = html_writer::link(
            new moodle_url('/blocks/mt/mt_p_timeline/admin.php', $querystring),
            get_string('mt:admin_p_timeline', 'block_mt'));
        $mform->addElement('static', 'pt_admin', '', $link);
        $link = html_writer::link(
            new moodle_url('/blocks/mt/mt_rankings/admin.php', $querystring),
            get_string('mt:admin_rankings', 'block_mt'));
        $mform->addElement('static', 'rankings_admin', '', $link);

        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }
}