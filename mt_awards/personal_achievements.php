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
 * This displays all the awards a student has achieved
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../config.php');

defined('MOODLE_INTERNAL') || die();

$pagename = get_string('mt_awards:personal_achievements', 'block_mt');
$pageurl = '/blocks/mt/mt_awards/personal_achievements.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/includes.php');

echo html_writer::tag('h2', get_string('mt_awards:personal_achievements_desc', 'block_mt'));

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_mainmenu.php');
echo html_writer::tag('h2', get_string('mt_awards:personal_achievements_summary', 'block_mt'));

$table = new html_table();
$table->width = '70%';

$table->head = array(
    get_string('mt_awards:personal_achievements_award', 'block_mt'),
    get_string('mt_awards:personal_achievements_total', 'block_mt')
);

$table->size = array(
    '200px',
    '50px'
);
$table->id = "myTable";
$table->attributes['class'] = 'tablesorter-blue';

$sql = "SELECT awardid, count(*) AS count
          FROM {block_mt_awards_user}
         WHERE userid = :userid AND courseid=:courseid
      GROUP BY awardid
      ORDER BY awardid";
$parameters = array(
    'userid' => $userid,
    'courseid' => $courseid
);
$awards = $DB->get_records_sql($sql, $parameters);

$awardscount = 0;
foreach ($awards as $id => $awards) {
    if (!$awards->awardid) {
        continue;
    }
    $tablerow = new html_table_row(array(
        get_award_name($awards->awardid),
        $awards->count
    ));
    $tablerow->cells[1]->attributes['class'] = 'gradeColumn';
    $table->data[] = $tablerow;
    $awardscount += $awards->count;
}

$tablerow = new html_table_row(array(
    get_string('mt_awards:personal_achievements_total', 'block_mt'),
    $awardscount
));
$tablerow->cells[1]->attributes['class'] = 'gradeColumn';
$table->data[] = $tablerow;
echo html_writer::table($table);

echo html_writer::tag('h2', get_string('mt_awards:personal_achievements_details', 'block_mt'));

$table = new html_table();
$table->width = '70%';

$table->head = array(
    get_string('mt_awards:personal_achievements_achievement', 'block_mt'),
    get_string('mt_awards:personal_achievements_award', 'block_mt')
);

$table->size = array(
    '200px',
    '50px'
);
$table->id = "myTable";
$table->attributes['class'] = 'tablesorter-blue';

$params = array(
    'userid' => $userid,
    'courseid' => $courseid
);
if ($DB->record_exists_sql($sql, $params)) {
    $studentdata = $DB->get_records('block_mt_awards_user', $params, 'awardid');
    foreach ($studentdata as $id => $studentdata) {
        if (!$studentdata->awardid) {
            continue;
        }
        $tablerow = new html_table_row(array(
            $studentdata->award_name,
            get_award_name($studentdata->awardid)
        ));
        $tablerow->cells[1]->attributes['class'] = 'gradeColumn';
        $table->data[] = $tablerow;
    }
} else {
    $tablerow = new html_table_row(array(
        get_string('mt_awards:no_student_records', 'block_mt')
    ));
    $tablerow->attributes['class'] = 'highlight';
    $tablerow->cells[0]->colspan = '100%';
    $table->data[] = $tablerow;
}

echo html_writer::table($table);
echo $OUTPUT->footer();