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
 * Upgrade file for block_mt.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade file for block_mt.
 *
 * @param int $oldversion The old version of the plugin.
 */
function xmldb_block_mt_upgrade($oldversion) {
    global $DB;

    $currentversion = 2019072000;

    if ($oldversion < $currentversion) {

        $dbman = $DB->get_manager();

        // Define field id to be dropped from block_mt_instancenames.
        $table = new xmldb_table('block_mt_instancenames');
        $field = new xmldb_field('name');

        // Conditionally launch drop field id.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Mt savepoint reached.
        upgrade_block_savepoint(true, $currentversion, 'mt');
    }

    return true;
}