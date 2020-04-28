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
 * This is the admin page form for teacher options.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Biswajeet Mishra
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once("{$CFG->libdir}/formslib.php");

/**
 * Form definition for the admin options.
 *
 * @copyright 2019 Ted Krahn
 * @copyright based on work by 2017 Biswajeet Mishra
 * @copyright based on work by 2017 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mt_ptimeline_form extends moodleform {

    /**
     * Function to make the form.
     */
    public function definition() {
        global $DB;

        $courseid = required_param('courseid', PARAM_INT);

        $mform = & $this->_form;

        // Hidden elements.
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        // Add group for text areas.
        $mform->addElement('header', 'displayinfo',
            get_string('mt_ptimeline:admin_heading', 'block_mt'));

        $mform->addElement('static', 'description',
            get_string('mt_ptimeline:admin_static', 'block_mt'),
            get_string('mt_ptimeline:admin_value', 'block_mt'));

        // Get assignment and quiz ids.
        $assignid = $DB->get_field('modules', 'id', array('name' => 'assign'));
        $quizid   = $DB->get_field('modules', 'id', array('name' => 'quiz'));

        // Get assignment and quiz modules.
        $sql = "SELECT id, course, module, instance
                  FROM {course_modules}
                 WHERE course = :courseid
                   AND (module = :quizid OR module = :assignid)
              ORDER BY id";

        $params = array(
            'courseid' => $courseid,
            'quizid'   => $quizid,
            'assignid' => $assignid
        );
        $milestones = $DB->get_records_sql($sql, $params);

        // Add modules to the form.
        foreach ($milestones as &$milestone) {

            // Get the module name.
            $table = 'quiz';
            if ($milestone->module == $assignid) {
                $table = 'assign';
            }
            $name = $DB->get_record($table, array('id' => $milestone->instance), 'name');

            // Add to form.
            $mform->addElement('text', 'm'.$milestone->id, $name->name);
            $mform->setType('m'.$milestone->id, PARAM_INT);

            // Restrict entered value to between 0 and 26 inclusive.
            $mform->addRule('m'.$milestone->id,
                get_string('mt_ptimeline:admin_numeric_error', 'block_mt'),
                'regex', '/^[0-9]$|^[1][0-9]$|^[2][0-6]$/', 'client', true, false);
        }

        $this->add_action_buttons();
    }
}