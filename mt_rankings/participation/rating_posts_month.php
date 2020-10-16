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
 * This displays the rankings for the ratings posts for the current month.
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
$pagename = get_string ( 'mt_rankings:rating_posts_month_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_rankings/participation/rating_posts_month.php';
$pageurlparams = array(
    'active' => $active
);
$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');

echo html_writer::tag ( 'h2', get_string ( 'mt_rankings:rating_posts_month_desc', 'block_mt' ) );

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_active.php');

$table = new html_table ();
$table->width = '70%';

$table->head = array (
    get_string ( 'mt_rankings:rating_posts_month_rank', 'block_mt' ),
    get_string ( 'mt_rankings:rating_posts_month_name', 'block_mt' ),
    get_string ( 'mt_rankings:rating_posts_month_active', 'block_mt' ),
    get_string ( 'mt_rankings:rating_posts_month_percent', 'block_mt' )
);
$table->size = array (
    '10px',
    '200px',
    '50px',
    '50px'
);
$table->id = "myTable";
$table->attributes ['class'] = 'tablesorter-blue';

$sql = "SELECT userid, period, courseid, rating_percent
        FROM {block_mt_ranks_rating_posts}
        WHERE period=:period AND courseid=:courseid AND rating_percent > 0 AND period_type=:period_type
        ORDER BY rating_percent DESC";
$params = array (
        'period' => block_mt_get_current_period(),
        'courseid' => $courseid,
        'period_type' => RANK_PERIOD_MONTHLY
);
if ($DB->record_exists_sql ( $sql, $params )) {
    $studentdata = $DB->get_records_sql ( $sql, $params );
    $i = 1;
    foreach ($studentdata as $student) {
        $student->active = is_active($student->userid, $courseid);
        if (display_anonymous ( $student->userid, $courseid )) {
            $studentname = get_string ( 'mt_rankings:rating_posts_month_anonymous', 'block_mt' );
        } else {
            $studentname = get_string ('mt_rankings:rating_posts_month_student_name', 'block_mt',
                block_mt_get_user_name($student->userid));
        }
        $tablerow = new html_table_row ( array (
            $i,
            $studentname,
            display_active_flag($student->active),
            number_format($student->rating_percent, 2)
        ) );
        $tablerow->cells [2]->attributes ['class'] = 'gradeColumn';
        $tablerow->cells [2]->attributes ['title'] = get_string ( 'mt_rankings:rating_posts_month_title', 'block_mt' );
        if ($student->userid == $userid) {
            $tablerow->attributes ['class'] = 'highlight';
        }
        if ($active == 'true') {
            if ($student->active) {
                // If active flag only display if student is active.
                $table->data[] = $tablerow;
                $i ++;
            }
        } else {
            // Display all students if no active flag.
            $table->data[] = $tablerow;
            $i ++;
        }
    }
} else {
    $table->data[] = get_no_records_row('mt_rankings:no_records');
}
echo html_writer::table ( $table );
echo $OUTPUT->footer();