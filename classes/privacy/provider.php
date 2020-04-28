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
 * Privacy subsystem for Motivational Techniques block.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mt\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy subsystem for Motivational Techniques block.
 *
 * @copyright 2019 Ted Krahn
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns metadata about this system.
     *
     * @param  collection $collection The initialized collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        // Metadata for the annotation table.
        $collection->add_database_table(
            'block_mt_annotation',
            [
                'course' => 'privacy:metadata:annotation:course',
                'userid' => 'privacy:metadata:annotation:userid',
                'object' => 'privacy:metadata:annotation:object',
                'value'  => 'privacy:metadata:annotation:value'
            ],
            'privacy:metadata:annotation'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param  int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {

        $contextlist = new \core_privacy\local\request\contextlist();

        // Get a list of context ids for the annotation table.
        $sql = "SELECT c.id
                 FROM {context} c, {block_mt_annotation} a
                WHERE c.instanceid = a.object
                  AND c.contextlevel = :contextlevel
                  AND a.userid = :userid";

        $params = array(
            'contextlevel' => CONTEXT_MODULE,
            'userid'       => $userid
        );

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist List of users who have data in this context.
     */
    public static function get_users_in_context(userlist $userlist) {

        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        // Get a list of user ids for the annotation table.
        $sql = "SELECT userid FROM {block_mt_annotation}
                 WHERE object = :instanceid";

        $params = array('instanceid' => $context->instanceid);

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        // Export data from the annotation table.
        $sql = "SELECT * FROM {block_mt_annotation}
                 WHERE object = :instanceid
                   AND userid = :userid";

        foreach ($contextlist->get_contexts() as $context) {

            $params = array(
                'instanceid' => $context->instanceid,
                'userid'     => $userid
            );
            $data = (object) $DB->get_records_sql($sql, $params);

            writer::with_context($context)->export_data(array(), $data);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        // Delete records from annotation table for this context.
        $DB->delete_records('block_mt_annotation', array('object' => $context->instanceid));
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The contexts and user id to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        // Delete records for this user for these contexts.
        foreach ($contextlist->get_contexts() as $context) {
            $params = array(
                'userid' => $userid,
                'object' => $context->instanceid
            );
            $DB->delete_records('block_mt_annotation', $params);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The context and user ids to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $instanceid = $userlist->get_context()->instanceid;
        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);

        // Delete all the user data for this context and these users.
        $params = array_merge(array('object' => $instanceid), $userinparams);
        $sql = "object = :object AND userid {$userinsql}";

        $DB->delete_records_select('block_mt_annotation', $sql, $params);
    }
}