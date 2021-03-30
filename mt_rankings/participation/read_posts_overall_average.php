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
 * This displays the rankings for the read posts overall
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

defined('MOODLE_INTERNAL') || die();

$active = optional_param ( 'active', 'true', PARAM_STRINGID );

global $DB;

$pagename = get_string ( 'mt_rankings:read_posts_overall_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_rankings/participation/read_posts_overall_average.php';
$pageurlparams = array(
    'active' => $active
);
$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');

echo html_writer::tag ( 'h2', get_string ( 'mt_rankings:read_posts_overall_desc', 'block_mt' ) );

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_active.php');

$table = new html_table ();
$table->width = '70%';

$tableheader = array (
    get_string ( 'mt_rankings:read_posts_overall_rank', 'block_mt' ),
    get_string ( 'mt_rankings:read_posts_overall_name', 'block_mt' ),
    get_string ( 'mt_rankings:read_posts_overall_active', 'block_mt' ),
    get_string ( 'mt_rankings:read_posts_overall_percent', 'block_mt' )
);

$table->head = block_mt_generate_table_header_months ( $tableheader );

$table->size = array (
    '10px',
    '100',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px',
    '50px'
);
$table->id = "myTable";
$table->attributes ['class'] = 'tablesorter-blue';

// Overall average.
if ($active == 'false') {
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT  o_t.userid as userid, percent_read, m_e.num_months,
                percent_read/m_e.num_months as average_posts_month, m_e.active, num_read, num_posts
        FROM {block_mt_ranks_read_posts} o_t
        JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, extract (YEAR from CURRENT_DATE)
            - extract (YEAR from to_timestamp(timestart))
            + extract (month from CURRENT_DATE)
            - extract (MONTH from to_timestamp(timestart)) + 1 AS num_months,
            {block_mt_active_users}.active
            FROM {user_enrolments}
            JOIN {enrol}
            ON {enrol}.id={user_enrolments}.enrolid
            JOIN {block_mt_active_users}
            ON {user_enrolments}.userid={block_mt_active_users}.userid
            AND {enrol}.courseid={block_mt_active_users}.courseid
        ) m_e
        ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
        WHERE o_t.courseid=:courseid
        AND period_type=:period_type
        GROUP BY o_t.userid, percent_read, m_e.num_months, m_e.active, num_read, num_posts
        ORDER BY percent_read DESC";
            break;
        default :
            $sql = "SELECT  o_t.userid as userid, percent_read, m_e.num_months,
                percent_read/m_e.num_months as average_posts_month, m_e.active, num_read, num_posts
        FROM {block_mt_ranks_read_posts} o_t
        JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, (YEAR(CURRENT_DATE)
            - YEAR(FROM_UNIXTIME(timestart)))
            + (MONTH(CURRENT_DATE)
            - MONTH(FROM_UNIXTIME(timestart))) + 1 AS num_months,
            {block_mt_active_users}.active
            FROM {user_enrolments}
            JOIN {enrol}
            ON {enrol}.id={user_enrolments}.enrolid
            JOIN {block_mt_active_users}
            ON {user_enrolments}.userid={block_mt_active_users}.userid
            AND {enrol}.courseid={block_mt_active_users}.courseid
        ) m_e
        ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
        WHERE o_t.courseid=:courseid
        AND period_type=:period_type
        GROUP BY userid
        ORDER BY percent_read DESC";
    }
} else {
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT  o_t.userid as userid, percent_read, m_e.num_months,
                percent_read/m_e.num_months as average_posts_month, m_e.active, num_read, num_posts
        FROM {block_mt_ranks_read_posts} o_t
            JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, extract (YEAR from CURRENT_DATE)
            - extract (year from to_timestamp(timestart))
            + extract (month from CURRENT_DATE)
            - extract (MONTH from to_timestamp(timestart)) + 1 AS num_months,
            {block_mt_active_users}.active
            FROM {user_enrolments}
            JOIN {enrol}
            ON {enrol}.id={user_enrolments}.enrolid
            JOIN {block_mt_active_users}
            ON {user_enrolments}.userid={block_mt_active_users}.userid
            AND {enrol}.courseid={block_mt_active_users}.courseid
            WHERE {block_mt_active_users}.active=1
            ) m_e
        ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
        WHERE o_t.courseid=:courseid
        AND period_type=:period_type
        GROUP BY o_t.userid, percent_read, m_e.num_months, m_e.active, num_read, num_posts
        ORDER BY percent_read DESC";
            break;
        default :
            $sql = "SELECT  o_t.userid as userid, percent_read, m_e.num_months,
                percent_read/m_e.num_months as average_posts_month, m_e.active, num_read, num_posts
        FROM {block_mt_ranks_read_posts} o_t
            JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, (YEAR(CURRENT_DATE)
            - YEAR(FROM_UNIXTIME(timestart)))
            + (MONTH(CURRENT_DATE)
            - MONTH(FROM_UNIXTIME(timestart))) + 1 AS num_months,
            {block_mt_active_users}.active
            FROM {user_enrolments}
            JOIN {enrol}
            ON {enrol}.id={user_enrolments}.enrolid
            JOIN {block_mt_active_users}
            ON {user_enrolments}.userid={block_mt_active_users}.userid
            AND {enrol}.courseid={block_mt_active_users}.courseid
            WHERE {block_mt_active_users}.active=1
            ) m_e
        ON o_t.userid=m_e.userid and o_t.courseid=m_e.courseid
        WHERE o_t.courseid=:courseid
        AND period_type=:period_type
        GROUP BY userid
        ORDER BY percent_read DESC";
    }
}

$currentperiod = date_format(block_mt_get_current_date(), 'Y-n-d');
$startperiod = date_format(block_mt_get_start_date(), 'Y-n-d');

$params = array (
        'period' => $startperiod,
        'courseid' => $courseid,
        'period_type' => RANK_PERIOD_OVERALL
);
if ($DB->record_exists_sql ( $sql, $params )) {
    $studentlist = $DB->get_records_sql ( $sql, $params );
    $i = 0;
    foreach ($studentlist as $id => $studentlist) {
        $i ++;

        if (display_anonymous ( $studentlist->userid, $courseid )) {
            $studentname = get_string ( 'mt_rankings:read_posts_overall_anonymous', 'block_mt' );
        } else {
            $studentname = get_string ( 'mt_rankings:read_posts_overall_student_name', 'block_mt',
                block_mt_get_user_name($studentlist->userid)  );
        }

        $row = array (
            $i,
            $studentname,
            block_mt_display_active_flag ( $studentlist->active ),
            number_format ( $studentlist->percent_read, 2 )
        );
        $titletext = get_string ( 'mt_rankings:read_posts_overall_title_percent_calc', 'block_mt', $studentlist );

        $previousmonths = block_mt_get_current_date();
        $parameters = array (
            'userid' => $studentlist->userid,
            'courseid' => $courseid,
            'period' => $currentperiod,
            'period_type' => RANK_PERIOD_MONTHLY
        );
        $monthdata = $DB->get_record ( 'block_mt_ranks_read_posts', $parameters );

        $cell = new html_table_cell ();
        $cell->attributes ['class'] = 'gradeColumn';

        if ($monthdata != null && $monthdata->percent_read != 0) {
            $monthdata->previousmonths = $previousmonths->format ( 'F Y' );
            $cell->text = number_format ( $monthdata->percent_read, 2 );
            $cell->attributes ['title'] = get_string ( 'mt_rankings:read_posts_overall_title_percent',
                'block_mt', $monthdata );
        } else {
            $cell->text = null;
        }
        $row [] = $cell;

        for ($monthcounter = 11; $monthcounter > 0; $monthcounter --) {
            $cell = new html_table_cell ();
            $cell->attributes ['class'] = 'gradeColumn';
            $previousmonths->modify ( 'previous month' );
            $period = $previousmonths->format ( 'Y-n-d' );
            $monthlyparams = array (
                'userid' => $studentlist->userid,
                'courseid' => $courseid,
                'period' => $period,
                'period_type' => RANK_PERIOD_MONTHLY
            );
            $monthdata = $DB->get_record ( 'block_mt_ranks_read_posts', $monthlyparams );

            if ($monthdata != null && $monthdata->percent_read != 0) {
                $monthdata->previousmonths = $previousmonths->format ( 'F Y' );
                $cell->text = number_format ( $monthdata->percent_read, 2 );
                $cell->attributes ['title'] = get_string ( 'mt_rankings:read_posts_overall_title_number',
                    'block_mt', $monthdata );
            } else {
                $cell->text = null;
            }
            $row [] = $cell;
        }
        $tablerow = new html_table_row ( $row );

        $tablerow->cells [2]->attributes ['class'] = 'activeColumn';
        $tablerow->cells [3]->attributes ['class'] = 'gradeColumn';
        $tablerow->cells [3]->attributes ['title'] = $titletext;
        if ($studentlist->userid == $userid) {
            $tablerow->attributes ['class'] = 'highlight';
        }
        $table->data [] = $tablerow;
    }
} else {
    $table->data[] = block_mt_get_no_records_row('mt_rankings:no_records');
}
echo html_writer::table ( $table );
echo $OUTPUT->footer();