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
 * This displays the a list of all the quizzes
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

$pagename = get_string ( 'mt_awards:grade_quiz_main_page_name', 'block_mt' );
$pageurl = '/blocks/mt/mt_awards/grades/grades_quiz_main.php';

global $OUTPUT;

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_awards/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/display_menu_items.php');
require($CFG->dirroot . '/blocks/mt/includes/get_quiz_list.php');
require($CFG->dirroot . "/blocks/mt/mt_awards/includes/get_grade_award_name.php");
require_once($CFG->dirroot . '/blocks/mt/includes/configuration_settings.php');
require($CFG->dirroot . '/blocks/mt/mt_awards/includes/buttons/buttons_mainmenu.php');

$counts = new stdClass ();
$counts->gold = get_awards_settings ( 'mt_awards:grades_gold_count_value', $courseid );
$counts->silver = get_awards_settings ( 'mt_awards:grades_silver_count_value', $courseid );
$counts->bronze = get_awards_settings ( 'mt_awards:grades_bronze_count_value', $courseid );

echo html_writer::tag ( 'h2', get_string ( 'mt_awards:grade_quiz_calc', 'block_mt', $counts ) );

$quizlist = block_mt_get_quiz_list($courseid);

$menuitem = new stdClass();
$menuitem->url = '';
$menuitem->text = '';
$menuitem->rank = '';

if (count ( ( array ) $quizlist ) > 0) {
    foreach ($quizlist as $quiz) {
        $urlparams = array (
                'courseid' => $courseid,
                'quizid' => $quiz->id
        );
        $urltext = get_string ( 'mt_awards:grade_quiz_main_current_award', 'block_mt', $quiz->itemname );
        $awardtext = get_current_award ( $userid, $courseid, get_grade_award_name($quiz->itemname));
        $urltext = $urltext . "<br/>" . $awardtext;

        $menuitem->url = new moodle_url ( 'grades_quiz.php', $urlparams );
        $menuitem->text = get_string ( 'mt_awards:grade_quiz_main_current_award', 'block_mt', $quiz->itemname );
        $menuitem->award = $awardtext;

        display_menu_item($menuitem);
    }
} else {
    $menuitem->url = "";
    $menuitem->text = get_string ( 'mt_awards:no_quizzes', 'block_mt' );
    $menuitem->award = "";

    display_menu_item($menuitem);
}
if (isset ( $urlparams ['quizid'] )) {
    unset ( $urlparams ['quizid'] );
}
echo html_writer::end_tag ( 'div' );
echo $OUTPUT->footer();