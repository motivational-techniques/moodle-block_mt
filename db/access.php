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
 *  This defines the access
 *
 * @package block_mt
 * @author phil.lachance
 * @copyright 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

 $capabilities = array (
        'block/mt:options' => array (
                'riskbitmask' => RISK_CONFIG,
                'captype' => 'write',
                'contextlevel' => CONTEXT_COURSE,
                'archetypes' => array (
                        'student' => CAP_ALLOW,
                        'teacher' => CAP_ALLOW,
                        'editingteacher' => CAP_ALLOW,
                        'manager' => CAP_ALLOW
                )
        ),
        'block/mt:admin' => array (
                'riskbitmask' => RISK_CONFIG,
                'captype' => 'write',
                'contextlevel' => CONTEXT_COURSE,
                'archetypes' => array (
                        'student' => CAP_PROHIBIT,
                        'teacher' => CAP_ALLOW,
                        'editingteacher' => CAP_ALLOW,
                        'manager' => CAP_ALLOW
                )
        ),
        'block/mt:myaddinstance' => array (
                'captype' => 'write',
                'contextlevel' => CONTEXT_SYSTEM,
                'archetypes' => array (
                        'user' => CAP_ALLOW
                ),
                'clonepermissionsfrom' => 'moodle/my:manageblocks'
        ),

        'block/mt:addinstance' => array (
                'riskbitmask' => RISK_SPAM | RISK_XSS,
                'captype' => 'write',
                'contextlevel' => CONTEXT_BLOCK,
                'archetypes' => array (
                        'editingteacher' => CAP_ALLOW,
                        'manager' => CAP_ALLOW
                ),
                'clonepermissionsfrom' => 'moodle/site:manageblocks'
        ),
        'block/mt_p_timeline:admin' => array (
                'riskbitmask' => RISK_CONFIG,
                'captype' => 'write',
                'contextlevel' => CONTEXT_COURSE,
                'archetypes' => array (
                        'student' => CAP_PROHIBIT,
                        'teacher' => CAP_ALLOW,
                        'editingteacher' => CAP_ALLOW,
                        'manager' => CAP_ALLOW
                )
        ),
        'block/mt_rankings:admin' => array (
                'riskbitmask' => RISK_CONFIG,
                'captype' => 'write',
                'contextlevel' => CONTEXT_COURSE,
                'archetypes' => array (
                        'student' => CAP_PROHIBIT,
                        'teacher' => CAP_ALLOW,
                        'editingteacher' => CAP_ALLOW,
                        'manager' => CAP_ALLOW
                )
        ),
        'block/mt_awards:admin' => array (
                'riskbitmask' => RISK_CONFIG,
                'captype' => 'write',
                'contextlevel' => CONTEXT_COURSE,
                'archetypes' => array (
                        'student' => CAP_PROHIBIT,
                        'teacher' => CAP_ALLOW,
                        'editingteacher' => CAP_ALLOW,
                        'manager' => CAP_ALLOW
                )
        ),
        'block/mt_p_annotation:admin' => array (
                'riskbitmask' => RISK_CONFIG,
                'captype' => 'write',
                'contextlevel' => CONTEXT_COURSE,
                'archetypes' => array (
                        'student' => CAP_PROHIBIT,
                        'teacher' => CAP_ALLOW,
                        'editingteacher' => CAP_ALLOW,
                        'manager' => CAP_ALLOW
                )
        ),
        'block/mt_goals:admin' => array (
                'riskbitmask' => RISK_CONFIG,
                'captype' => 'write',
                'contextlevel' => CONTEXT_COURSE,
                'archetypes' => array (
                        'student' => CAP_PROHIBIT,
                        'teacher' => CAP_ALLOW,
                        'editingteacher' => CAP_ALLOW,
                        'manager' => CAP_ALLOW
                )
        )
    );