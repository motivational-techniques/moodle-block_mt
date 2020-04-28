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
 * Simple event class to log when progress annotation has been viewed.
 *
 * @package block_mt
 * @copyright 2019 Ted Krahn
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mt\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Simple event class to log when progress annotation has been viewed.
 *
 * @copyright 2019 Ted Krahn
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mt_progress_annotation_viewed extends \core\event\base {

    /**
     * @var string The URL of the page viewed.
     */
    private $url;

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud']     = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('mt_p_annotation:view', 'block_mt');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return get_string('mt_p_annotation:viewed', 'block_mt', array(
            'userid'   => $this->userid,
            'courseid' => $this->courseid
        ));
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url($this->url, array('id' => $this->courseid));
    }

    /**
     * Sets the URL.
     *
     * @param string $url The URL to set for this event.
     * @return void
     */
    public function set_url($url) {
        $this->url = $url;
    }
}
