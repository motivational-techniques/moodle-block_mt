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
 * block_mt data generator
 *
 * @package    block_mt
 * @category   test
 * @copyright  2019 Ted Krahn
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * MT block data generator class
 *
 * @package    block_mt
 * @category   test
 * @copyright  2019 Ted Krahn
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mt_generator extends testing_block_generator {

    /**
     * Create instance of MT block.
     *
     * @param string $record
     * @param array $options
     * @return string
     */
    public function create_instance($record = null, $options = []) {
        $record = (object)(array)$record;

        return parent::create_instance($record, (array)$options);
    }
}
