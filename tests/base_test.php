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
 * Base testscase
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Base testcase class.
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */
class block_mt_base_testcase extends advanced_testcase {

    /**
     * setup
     */
    public function setUp() {
        $this->resetAfterTest(true);
        $this->assertEquals(2, 2);
    }

    /**
     * test block is installed
     */
    public function test_block_is_installed() {
        global $CFG;

        $course = $this->getDataGenerator()->create_course();

        $generator = $this->getDataGenerator()->get_plugin_generator('block_mt');
        $generator->create_instance(array('course' => $course->id));

        $users = $this->helper_get_users(4);
        $this->helper_enrol_users($users, $course->id);

        require_once($CFG->dirroot.'/blocks/mt/includes/block_is_installed.php');
        $this->assertFalse(block_is_installed($course->id));
    }

    /**
     * helper get users
     * @param string $num
     * @return string
     */
    private function helper_get_users($num) {
        $users = [];
        for ($i = 0; $i < $num; $i++) {
            $users[] = $this->getDataGenerator()->create_user();
        }
        return $users;
    }

    /**
     * helper enrol users
     * @param array $users
     * @param string $cid
     */
    private function helper_enrol_users(&$users, $cid) {
        for ($i = 0; $i < count($users); $i++) {
            $this->getDataGenerator()->enrol_user($users[$i]->id, $cid);
        }
    }
}