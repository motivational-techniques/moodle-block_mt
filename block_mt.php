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
 * This displays the MotTEC Motivational_technique block.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/mt/includes/admin_settings.php');
require_once($CFG->dirroot . '/blocks/mt/includes/display_block_links.php');

/**
 * Block definition for MT
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mt extends block_list {

    /**
     * Initialize the block.
     */
    public function init() {
        $this->title = get_string ( 'mt:title', 'block_mt' );
    }

    /**
     * Get the content.
     *
     * @return stdClass
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        if (! empty ( $this->config->title )) {
            $this->title = $this->config->title;
        }
        global $COURSE, $USER, $DB;

        $courseid = $COURSE->id;
        $userid = $USER->id;

        $this->content = new stdClass ();
        $this->content->items = array ();
        $this->content->icons = array (); // Not used, but needed to prevent error from blocks/moodleblock.class.php line 712.

        $this->content->items[] = block_mt_display_item_and_icon($courseid, 'awards', '/blocks/mt/mt_awards/main_awards.php',
            'mt:awards', '../pix/t/go.svg');

        $this->content->items[] = block_mt_display_item_and_icon($courseid, 'goals', '/blocks/mt/mt_goals/main_goals.php',
            'mt:goals', '../pix/i/hide.svg');

        $this->content->items[] = block_mt_display_item_and_icon($courseid, 'p_annotation', '/blocks/mt/mt_p_annotation/draw_chart.php',
            'mt:p_annotation', '../pix/t/markasread.svg');

        if (get_module_display($courseid, 'p_annotation')) {
            // Get the user selections for the activities for this course.
            $params = array(
                'course' => $courseid,
                'userid' => $userid
            );
            $values = $DB->get_records('block_mt_annotation', $params);
            $params['selects'] = json_encode($values);
            $params['sesskey'] = sesskey();
            $params['strings'] = array(
                'done'  => get_string('mt_p_annotation:tip_done', 'block_mt'),
                'not'   => get_string('mt_p_annotation:tip_not', 'block_mt'),
                'ip'    => get_string('mt_p_annotation:tip_ip', 'block_mt'),
                'click' => get_string('mt_p_annotation:tip_click', 'block_mt'),
            );

            // Pass them to the page to get rendered with each activity.
            $this->page->requires->jquery();
            $this->page->requires->jquery_plugin('ui');
            $this->page->requires->jquery_plugin('ui-css');
            $this->page->requires->js(new moodle_url('/blocks/mt/mt_p_annotation/display_pannotation_images.js'));
            $this->page->requires->js_init_call('getSessionInfo', $params);
        }

        $this->content->items[] = block_mt_display_item_and_icon($courseid, 'p_timeline', '/blocks/mt/mt_p_timeline/draw_chart.php',
            'mt:p_timeline', '../pix/i/siteevent.svg');

        $this->content->items[] = block_mt_display_item_and_icon($courseid, 'rankings', '/blocks/mt/mt_rankings/main_rankings.php',
            'mt:rankings', '../pix/i/scales.svg');

        // Insert blank line to separate the admin/options link.
        $this->content->items[] = html_writer::empty_tag('br');

        $this->content->items[] = block_mt_display_option_item_and_icon($courseid, $USER->id);

        return $this->content;
    }

    /**
     * Return if can be docked.
     *
     * @return boolean
     */
    public function instance_can_be_docked() {
        return (parent::instance_can_be_docked () && (empty ( $this->config->enabledock ) || $this->config->enabledock == 'yes'));
    }

    /**
     * Return applicable formats.
     *
     * @return array
     */
    public function applicable_formats() {
        return array('course-view' => true);
    }

    /**
     * Return whether allow multiple instances.
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Return whether can be hidden.
     *
     * @return boolean
     */
    public function instance_can_be_hidden() {
        return true;
    }

    /**
     * Return whether has config.
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Return if has cron.
     *
     * @return boolean
     */
    public function cron() {
        // If MT is installed include in the cron process.
        include_once('generate/generate.php');

        return true;
    }
}