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
 * This displays the rankings for the time online for all months.
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

global $DB, $CFG;

$pagename = get_string ( 'mt_rankings:time_online', 'block_mt' );
$pageurl = '/blocks/mt/mt_rankings/time_online/time_online_overall_average.php';
$pageurlparams = array(
    'active' => $active
);
$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');

echo html_writer::tag ( 'h2', get_string ( 'mt_rankings:time_online_average_desc', 'block_mt' ) );

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_active.php');
$table = new html_table ();
$table->width = '70%';

$tableheader = array (
        get_string ( 'mt_rankings:time_online_average_rank', 'block_mt' ),
        get_string ( 'mt_rankings:time_online_average_name', 'block_mt' ),
        get_string ( 'mt_rankings:time_online_average_active', 'block_mt' ),
        get_string ( 'mt_rankings:time_online_average_average_time', 'block_mt' )
);

$table->head = generate_table_header_months ( $tableheader );

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
            $sql = "SELECT  o_t.userid as userid, SUM(onlinetime) as total_online_time,
        m_e.num_months, SUM(onlinetime)/m_e.num_months as average_time_hours, m_e.active
        FROM {block_mt_ranks_onl_time} o_t
        JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, extract (year from CURRENT_DATE)
            - extract (year from to_timestamp(timestart))
            + extract (month from CURRENT_DATE)
            - extract (month from to_timestamp(timestart)) + 1 AS num_months,
            {block_mt_active_users}.active
            FROM {user_enrolments}
            JOIN {enrol}
            ON {enrol}.id={user_enrolments}.enrolid
            JOIN {block_mt_active_users}
            ON {user_enrolments}.userid={block_mt_active_users}.userid
            AND {enrol}.courseid={block_mt_active_users}.courseid
        ) m_e
        ON o_t.userid=m_e.userid AND o_t.courseid=m_e.courseid
        WHERE period>:period AND o_t.courseid=:courseid AND onlinetime<>0
        GROUP BY o_t.userid, m_e.num_months, m_e.active
        ORDER BY average_time_hours DESC";
            break;
        default :
            $sql = "SELECT  o_t.userid as userid, SUM(onlinetime) as total_online_time,
        m_e.num_months, SUM(onlinetime)/m_e.num_months as average_time_hours, m_e.active
        FROM {block_mt_ranks_onl_time} o_t
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
        ON o_t.userid=m_e.userid AND o_t.courseid=m_e.courseid
        WHERE period>:period AND o_t.courseid=:courseid AND onlinetime<>0
        GROUP BY userid
        ORDER BY average_time_hours DESC";
    }
} else {
    switch ($CFG->dbtype) {
        case DB_TYPE_POSTGRES :
            $sql = "SELECT  o_t.userid as userid, SUM(onlinetime) as total_online_time,
        m_e.num_months, SUM(onlinetime)/m_e.num_months as average_time_hours, m_e.active
        FROM {block_mt_ranks_onl_time} o_t
        JOIN (SELECT {block_mt_active_users}.userid, {enrol}.courseid, extract (YEAR from CURRENT_DATE)
            - extract (YEAR from to_timestamp(timestart))
            + extract (MONTH from CURRENT_DATE)
            - extract (month from to_timestamp(timestart)) + 1 AS num_months,
            {block_mt_active_users}.active
            FROM {user_enrolments}
            JOIN {enrol}
            ON {enrol}.id={user_enrolments}.enrolid
            JOIN {block_mt_active_users}
            ON {user_enrolments}.userid={block_mt_active_users}.userid
            AND {enrol}.courseid={block_mt_active_users}.courseid
            WHERE {block_mt_active_users}.active=1
            ) m_e
        ON o_t.userid=m_e.userid AND o_t.courseid=m_e.courseid
        WHERE period>:period AND o_t.courseid=:courseid AND onlinetime<>0
        GROUP BY o_t.userid, m_e.num_months, m_e.active
        ORDER BY average_time_hours DESC";
            break;
        default :
            $sql = "SELECT  o_t.userid as userid, SUM(onlinetime) as total_online_time,
        m_e.num_months, SUM(onlinetime)/m_e.num_months as average_time_hours, m_e.active
        FROM {block_mt_ranks_onl_time} o_t
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
        ON o_t.userid=m_e.userid AND o_t.courseid=m_e.courseid
        WHERE period>:period AND o_t.courseid=:courseid AND onlinetime<>0
        GROUP BY userid
        ORDER BY average_time_hours DESC";
    }
}

$currentperiod = date_format(block_mt_get_current_date(), 'Y-n-d');
$startperiod = date_format(block_mt_get_start_date(), 'Y-n-d');

$params = array (
        'period' => $startperiod,
        'courseid' => $courseid
);
if ($DB->record_exists_sql($sql, $params)) {
    $studentlist = $DB->get_records_sql($sql, $params);
    $i = 0;
    foreach ($studentlist as $student) {
        $i ++;
        if (display_anonymous ( $student->userid, $courseid )) {
            $studentname = get_string ( 'mt_rankings:grade_quiz_anonymous', 'block_mt' );
        } else {
            $studentname = get_string ( 'mt_rankings:grade_quiz_student_name', 'block_mt', block_mt_get_user_name($student->userid));
        }

        $averagetimehours = $student->total_online_time / 3600 / $student->num_months;
        $row = array (
            $i,
            $studentname,
            display_active_flag($student->active),
            number_format($averagetimehours, 2)
        );

        $averagetime = new stdClass ();
        $averagetime->online_time = number_format ( $student->total_online_time / 3600, 2 );
        $averagetime->num_months = $student->num_months;
        $titletext = get_string ( 'mt_rankings:time_online_average_title_average', 'block_mt', $averagetime );

        $previousmonths = block_mt_get_current_date();
        $parameters = array (
            'userid' => $student->userid,
            'courseid' => $courseid,
            'period' => $currentperiod,
            'period_type' => RANK_PERIOD_MONTHLY
        );
        $monthdata = $DB->get_record ( 'block_mt_ranks_onl_time', $parameters );

        $cell = new html_table_cell ();
        $cell->attributes ['title'] = get_string ( 'mt_rankings:time_online_average_title_month',
            'block_mt', $previousmonths->format ( 'F Y' ) );
        $cell->attributes ['class'] = 'gradeColumn';

        if ($monthdata && $monthdata->onlinetime > 0) {
            $cell->text = number_format ( $monthdata->onlinetime / 3600, 2 );
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
                'userid' => $student->userid,
                'courseid' => $courseid,
                'period' => $period,
                'period_type' => RANK_PERIOD_MONTHLY
            );
            $monthdata = $DB->get_record ( 'block_mt_ranks_onl_time', $monthlyparams );
            $cell->attributes ['title'] = get_string ( 'mt_rankings:time_online_average_title_month',
                'block_mt', $previousmonths->format ( 'F Y' ) );

            if ($monthdata && $monthdata->onlinetime > 0) {
                    $cell->text = number_format ( $monthdata->onlinetime / 3600, 2 );
            } else {
                $cell->text = null;
            }
            $row [] = $cell;
        }
        $tablerow = new html_table_row ( $row );

        $tablerow->cells [2]->attributes ['class'] = 'activeColumn';
        $tablerow->cells [3]->attributes ['class'] = 'gradeColumn';
        $tablerow->cells [3]->attributes ['title'] = $titletext;
        if ($student->userid == $userid) {
            $tablerow->attributes ['class'] = 'highlight';
        }
        $table->data [] = $tablerow;
    }
} else {
    $table->data[] = block_mt_get_no_records_row('mt_rankings:no_records');
}
echo html_writer::table ( $table );
echo $OUTPUT->footer();