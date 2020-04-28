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
 * Additional behat steps definition
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Behat\Context\Step\Given as Given,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Additional steps definition.
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_block_mt extends behat_base {

    /**
     * Go to the front page.
     *
     * There are no standard definitions available from 2.7 so we use our own.
     *
     * @Given /^I am on front page$/
     */
    public function i_am_on_front_page() {
        $this->getSession()->visit($this->locate_path('/?redirect=0'));
    }

    /**
     * Step to view a student's rankings.
     *
     * There are no standard definitions available from 2.7 so we use our own.
     *
     * @Given /^I follow edit for "(?P<student>(?:[^"]|\\")*)" in mt rankings$/
     * @param string $studentname
     * @return string
     */
    public function i_follow_edit_for_in_mt_rankings($studentname) {

        return $studentname;
    }

    /**
     * Used to check a checkbox in the block_mt Administration page.
     *
     * @When /^I check checkbox "(?P<checkbox_label>(?:[^"]|\\")*)" in block mt admin$/
     * @param string $box
     */
    public function i_check_checkbox_in_block_mt_admin($box) {
        $xpath = '';
        switch ($box) {
            case "Awards":
                $xpath = "//input[@id='id_awards']";
                break;
            case "Goals":
                $xpath = "//input[@id='id_goals']";
                break;
            case "Progress Annotation":
                $xpath = "//input[@id='id_p_annotation']";
                break;
            case "Progress Timeline":
                $xpath = "//input[@id='id_p_timeline']";
                break;
            case "Rankings":
                $xpath = "//input[@id='id_rankings']";
                break;
        }
        if ($xpath == '') {
            throw new ElementNotFoundException($box);
        }
        if ($this->running_javascript()) {
            $this->getSession()->getPage()->find('xpath', $xpath)->click();
        } else {
            $this->getSession()->getPage()->find('xpath', $xpath)->setValue('1');
        }
    }

    /**
     * Used to click a progress annotation image. NOT WORKING!!
     *
     * @When /^I click image "(?P<image_type>(?:[^"]|\\")*)" in block mt$/
     * @param string $img
     */
    public function i_click_image_in_block_mt($img) {
        $xpath = '';
        switch ($img) {
            case "check":
                $xpath = "//img[@value='1']";
                break;
            case "x":
                $xpath = "//img[@value='2']";
                break;
            case "ip":
                $xpath = "//img[@value='3']";
                break;
        }
        if ($xpath == '') {
            throw new ElementNotFoundException($img);
        }
        $this->getSession()->getDriver()->click($xpath);
    }
}
