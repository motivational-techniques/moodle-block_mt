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
 * This generates the mt_awards tables
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * generate awards tables
 *
 * @return null
 */
function generate_mt_awards_tables() {
    global $DB;

    $sql = "CREATE TABLE IF NOT EXISTS {block_mt_awards} (
        id bigint(10) NOT NULL AUTO_INCREMENT,
        type varchar(45) DEFAULT NULL,
        name varchar(100) DEFAULT NULL,
        url varchar(100) DEFAULT NULL,
        upper_criteria varchar(100) DEFAULT NULL,
        lower_criteria varchar(100) NOT NULL,
        rank int(11) DEFAULT NULL,
        PRIMARY KEY (id)
        )";
    $DB->execute($sql, null);

    $sql = "CREATE TABLE IF NOT EXISTS {block_mt_awards_config} (
        id bigint(10) unsigned NOT NULL AUTO_INCREMENT,
        courseid bigint(10) unsigned DEFAULT NULL,
        setting varchar(50) DEFAULT NULL,
        value bigint(20) DEFAULT NULL,
        PRIMARY KEY (id)
        )";
    $DB->execute($sql, null);

    $sql = "CREATE TABLE IF NOT EXISTS {block_mt_awards_count_all} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        courseid bigint(10) NOT NULL,
        userid bigint(10) NOT NULL,
        awardtotal bigint(10) NOT NULL,
        awardtype bigint(10) NOT NULL,
        PRIMARY KEY (id)
        )";
    $DB->execute($sql, null);

    $sql = "CREATE TABLE IF NOT EXISTS {block_mt_awards_last_period} (
        id int(11) NOT NULL AUTO_INCREMENT,
        courseid bigint(10) NOT NULL,
        period date NOT NULL,
        rank_type_id int(11) NOT NULL,
        PRIMARY KEY (id)
        )";
    $DB->execute($sql, null);

    $sql = "CREATE TABLE IF NOT EXISTS {block_mt_awards_pref} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        userid bigint(10) NOT NULL,
        courseid bigint(10) NOT NULL,
        anonymous tinyint(1) NOT NULL,
        PRIMARY KEY (id),
        KEY KEY3 (courseid) COMMENT 'courseid',
        KEY KEY2 (userid) COMMENT 'userid'
        )";
    $DB->execute($sql, null);

    $sql = "CREATE TABLE IF NOT EXISTS {block_mt_awards_support} (
        id bigint(10) NOT NULL AUTO_INCREMENT,
        itemmodule varchar(30) DEFAULT NULL,
        type varchar(45) DEFAULT NULL,
        PRIMARY KEY (id)
        )";
    $DB->execute($sql, null);

    $sql = "CREATE TABLE IF NOT EXISTS {block_mt_awards_user} (
        id bigint(10) unsigned NOT NULL AUTO_INCREMENT,
        userid bigint(10) unsigned NOT NULL,
        courseid bigint(10) unsigned NOT NULL,
        itemid bigint(10) unsigned DEFAULT NULL,
        awardid bigint(10) unsigned DEFAULT NULL,
        award_name varchar(100) NOT NULL,
        period date DEFAULT NULL,
        period_type varchar(10) DEFAULT NULL,
        PRIMARY KEY (id)
        )";
    $DB->execute($sql, null);
}