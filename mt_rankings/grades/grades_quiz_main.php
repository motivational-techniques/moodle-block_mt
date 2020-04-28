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
 * This displays a list of quizes.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
require(__DIR__ . '/../../../../config.php');

defined('MOODLE_INTERNAL') || die();

$pagename = get_string ( 'mt_rankings:rank_name_grades_quiz_main', 'block_mt' );
$pageurl = '/blocks/mt/mt_rankings/grades/grades_quiz_main.php';

$logmtpage = true;

require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/includes.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/display_menu_items.php');
require($CFG->dirroot . '/blocks/mt/mt_rankings/includes/buttons/buttons_mainmenu.php');
global $DB, $OUTPUT, $PAGE;

$parameters = array (
    'courseid' => $courseid,
    'itemmodule' => 'quiz'
);
$quizdata = $DB->get_records ( 'grade_items', $parameters, 'iteminstance', '*');

$menuheader = array (
    'class' => 'menu-header'
);
echo html_writer::start_tag ('div', $menuheader );
echo html_writer::tag ('p', $pagename, $menuheader );

$menuitem = new stdClass();
$menuitem->url = '';
$menuitem->text = '';
$menuitem->rank = '';

if (count ( ( array ) $quizdata ) > 0) {
    foreach ($quizdata as $quiz) {
        $urlparams = array (
            'courseid' => $courseid,
            'quizid' => $quiz->id
        );
        $urltext = get_string ( 'mt_rankings:grade_quiz_url_text', 'block_mt', $quiz->itemname );
        if ($DB->record_exists ( 'block_mt_ranks_user', array (
            'userid' => $userid,
            'courseid' => $courseid,
            'gradeid' => $quiz->id
        ) )) {
            $rank = $DB->get_record ( 'block_mt_ranks_user', array (
                'userid' => $userid,
                'courseid' => $courseid,
                'gradeid' => $quiz->id
            ) )->rank;
        } else {
            $rank = get_string ( 'mt_rankings:no_current_ranking', 'block_mt' );
        }
        $ranktext = get_string ( 'mt_rankings:grade_quiz_main_current_rank', 'block_mt', $rank );

        $menuitem->url = new moodle_url ( 'grades_quiz.php', $urlparams );
        $menuitem->text = get_string ( 'mt_rankings:grade_quiz_url_text', 'block_mt', $quiz->itemname );
        $menuitem->rank = $ranktext;

        display_menu_item($menuitem);
    }
} else {
    $menuitem->url = "";
    $menuitem->text = get_string ( 'mt_rankings:no_quizzes', 'block_mt' );
    $menuitem->rank = "";

    display_menu_item($menuitem);
}
if (isset ( $urlparams ['quizid'] )) {
    unset ( $urlparams ['quizid'] );
}

echo html_writer::end_tag ( 'div' );
echo $OUTPUT->footer();