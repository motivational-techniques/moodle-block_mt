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
 * This generates the participation awards
 *
 * @package block_mt
 * @copyright 2019 Phil Lachance
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * generate awards number posts
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_number_posts($paramcourseid) {
    mtrace(get_string('mt:cron_awards_num_posts', 'block_mt'));
    generate_awards_number_posts_monthly($paramcourseid);

    generate_number_posts_overall_count($paramcourseid);
}

/**
 * generate awards number posts month
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_number_posts_monthly($paramcourseid) {
    global $DB;
    $courseid = $paramcourseid;

    $goldaward = get_awards_settings('mt_awards:num_posts_gold_count_value', $courseid);
    $silveraward = get_awards_settings('mt_awards:num_posts_silver_count_value', $courseid);
    $bronzeaward = get_awards_settings('mt_awards:num_posts_bronze_count_value', $courseid);

    $awardcount = new stdClass();
    $awardcount->gold = get_awards_settings('mt_awards:num_posts_gold_count_value', $courseid);
    $awardcount->silver = get_awards_settings('mt_awards:num_posts_silver_count_value', $courseid);
    $awardcount->bronze = get_awards_settings('mt_awards:num_posts_bronze_count_value', $courseid);

    $periods = get_periods_number_posts($courseid);
    foreach ($periods as $period) {
        $counts = $DB->get_records('block_mt_ranks_num_posts', array(
            'courseid' => $courseid,
            'period' => $period->period
        ));
        foreach ($counts as $count) {
            $awardname = get_string('mt_awards:generate_award_number_posts', 'block_mt',
                date_format(new DateTime($period->period), "Y-n"));
            $awardid = null;
            if ($count->num_posts >= $bronzeaward) {
                $awardid = BRONZE_AWARD_ID;
            }
            if ($count->num_posts >= $silveraward) {
                $awardid = SILVER_AWARD_ID;
            }
            if ($count->num_posts >= $goldaward) {
                $awardid = GOLD_AWARD_ID;
            }
            $recordcountparam = array(
                'userid' => $count->userid,
                'courseid' => $count->courseid,
                'award_name' => $awardname,
                'period' => $period->period,
                'period_type' => RANK_PERIOD_MONTHLY
            );
            $recordcount = $DB->count_records('block_mt_awards_user', $recordcountparam);
            if ($recordcount < 1) {
                $recordcountparam['awardid'] = $awardid;
                $DB->insert_record('block_mt_awards_user', $recordcountparam);
            } else {
                $updateid = $DB->get_field('block_mt_awards_user', 'id', $recordcountparam);
                $recordcountparam['awardid'] = $awardid;
                $recordcountparam['id'] = $updateid;
                $DB->update_record('block_mt_awards_user', $recordcountparam);
            }
        }
        update_last_period_run_awards($paramcourseid, RANK_TYPE_NUMBER_POSTS, $period->period);
    }
}

/**
 * generate awards number posts overall count
 *
 * @param string $courseid
 * @return null
 */
function generate_number_posts_overall_count($courseid) {
    global $DB;

    $awardweight = new stdClass();
    $awardweight->gold = get_awards_settings('mt_awards:num_posts_gold_weight_value', $courseid);
    $awardweight->silver = get_awards_settings('mt_awards:num_posts_silver_weight_value', $courseid);
    $awardweight->bronze = get_awards_settings('mt_awards:num_posts_bronze_weight_value', $courseid);

    $sql = "SELECT userid
        FROM {block_mt_awards_user}
        WHERE courseid=:courseid
        AND award_name like 'Number posts%'
        GROUP BY userid
        ORDER by userid";

    $studentlist = $DB->get_records_sql($sql, array(
        'courseid' => $courseid
    ));

    foreach ($studentlist as $student) {
        $awardtotal = 0;
        $sql = "SELECT period, awardid
            FROM {block_mt_awards_user}
            WHERE courseid=:courseid
            AND award_name like 'Number posts%'
            AND userid=:userid";
        $awardlist = $DB->get_records_sql($sql, array(
            'courseid' => $courseid,
            'userid' => $student->userid
        ));
        $awardtotal = calculate_award_total($awardlist, $awardtotal, $awardweight);

        $recordcountparam = array(
            'courseid' => $courseid,
            'userid' => $student->userid,
            'awardtype' => RANK_TYPE_NUMBER_POSTS
        );
        $recordcount = $DB->count_records('block_mt_awards_count_all', $recordcountparam);
        if ($recordcount < 1) {
            $recordcountparam['awardtotal'] = $awardtotal;
            $DB->insert_record('block_mt_awards_count_all', $recordcountparam);
        } else {
            $updateid = $DB->get_field('block_mt_awards_count_all', 'id', $recordcountparam);
            $recordcountparam['awardtotal'] = $awardtotal;
            $recordcountparam['id'] = $updateid;
            $DB->update_record('block_mt_awards_count_all', $recordcountparam);
        }
    }
}

/**
 * generate awards read posts monthly
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_read_posts_monthly($paramcourseid) {
    global $DB;
    $courseid = $paramcourseid;

    $goldaward = get_awards_settings('mt_awards:read_posts_gold_count_value', $courseid);
    $silveraward = get_awards_settings('mt_awards:read_posts_silver_count_value', $courseid);
    $bronzeaward = get_awards_settings('mt_awards:read_posts_bronze_count_value', $courseid);

    $awardcount = new stdClass();
    $awardcount->gold = get_awards_settings('mt_awards:read_posts_gold_count_value', $courseid);
    $awardcount->silver = get_awards_settings('mt_awards:read_posts_silver_count_value', $courseid);
    $awardcount->bronze = get_awards_settings('mt_awards:read_posts_bronze_count_value', $courseid);

    $periods = get_periods_read_posts($courseid);
    foreach ($periods as $period) {
        $countsparam = array(
            'courseid' => $courseid,
            'period' => $period->period
        );
        $postsreadlist = $DB->get_records('block_mt_ranks_read_posts', $countsparam);
        foreach ($postsreadlist as $postsread) {
            $awardname = get_string('mt_awards:generate_award_read_posts', 'block_mt',
                date_format(new DateTime($period->period), "Y-n"));
            $awardid = null;
            if ($postsread->percent_read >= $bronzeaward) {
                $awardid = BRONZE_AWARD_ID;
            }
            if ($postsread->percent_read >= $silveraward) {
                $awardid = SILVER_AWARD_ID;
            }
            if ($postsread->percent_read >= $goldaward) {
                $awardid = GOLD_AWARD_ID;
            }
            $recordcountparams = array(
                'userid' => $postsread->userid,
                'courseid' => $postsread->courseid,
                'award_name' => $awardname,
                'period' => $period->period,
                'period_type' => RANK_PERIOD_MONTHLY
            );
            $recordcount = $DB->count_records('block_mt_awards_user', $recordcountparams);
            if ($recordcount < 1) {
                $recordcountparams ['awardid'] = $awardid;
                $DB->insert_record('block_mt_awards_user', $recordcountparams);
            } else {
                $updateid = $DB->get_field('block_mt_awards_user', 'id', $recordcountparams);
                $recordcountparams ['awardid'] = $awardid;
                $recordcountparams ['id'] = $updateid;

                $DB->update_record('block_mt_awards_user', $recordcountparams);
            }
        }
        update_last_period_run_awards($paramcourseid, RANK_TYPE_WEEKLY_POSTS, $period->period);
    }
}

/**
 * generate awards read posts overall count
 *
 * @param string $courseid
 * @return null
 */
function generate_read_posts_overall_count($courseid) {
    global $DB;

    $awardweight = new stdClass();
    $awardweight->gold = get_awards_settings('mt_awards:read_posts_gold_weight_value', $courseid);
    $awardweight->silver = get_awards_settings('mt_awards:read_posts_silver_weight_value', $courseid);
    $awardweight->bronze = get_awards_settings('mt_awards:read_posts_bronze_weight_value', $courseid);

    $sql = "SELECT userid
        FROM {block_mt_awards_user}
        join {user}
        on {user}.id={block_mt_awards_user}.userid
        WHERE courseid=:courseid and {user}.trackforums=1
        AND award_name like 'Read posts%'
        GROUP BY userid
        ORDER by userid";

    $studentlist = $DB->get_records_sql($sql, array(
        'courseid' => $courseid
    ));

    foreach ($studentlist as $student) {
        $awardtotal = 0;
        $sql = "SELECT period, awardid
            FROM {block_mt_awards_user}
            join {user}
            on {user}.id={block_mt_awards_user}.userid
            WHERE courseid=:courseid
            and {user}.trackforums=1
            AND award_name like 'Read posts%'
            AND userid=:userid";
        $awardlist = $DB->get_records_sql($sql, array(
            'courseid' => $courseid,
            'userid' => $student->userid
        ));
        $awardtotal = calculate_award_total($awardlist, $awardtotal, $awardweight);

        $recordcountparam = array(
            'courseid' => $courseid,
            'userid' => $student->userid,
            'awardtype' => RANK_TYPE_WEEKLY_POSTS
        );
        $recordcount = $DB->count_records('block_mt_awards_count_all', $recordcountparam);
        if ($recordcount < 1) {
            $recordcountparam['awardtotal'] = $awardtotal;
            $DB->insert_record('block_mt_awards_count_all', $recordcountparam);
        } else {
            $updateid = $DB->get_field('block_mt_awards_count_all', 'id', $recordcountparam);
            $recordcountparam['id'] = $updateid;
            $recordcountparam['awardtotal'] = $awardtotal;
            $DB->update_record('block_mt_awards_count_all', $recordcountparam);
        }
    }
}

/**
 * generate awards rating posts monthly
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_rating_posts_monthly($paramcourseid) {
    global $DB;
    $courseid = $paramcourseid;

    $goldaward = get_awards_settings('mt_awards:rating_posts_gold_count_value', $courseid);
    $silveraward = get_awards_settings('mt_awards:rating_posts_silver_count_value', $courseid);
    $bronzeaward = get_awards_settings('mt_awards:rating_posts_bronze_count_value', $courseid);

    $awardcount = new stdClass();
    $awardcount->gold = get_awards_settings('mt_awards:rating_posts_gold_count_value', $courseid);
    $awardcount->silver = get_awards_settings('mt_awards:rating_posts_silver_count_value', $courseid);
    $awardcount->bronze = get_awards_settings('mt_awards:rating_posts_bronze_count_value', $courseid);

    $periods = get_periods_rating_posts($courseid);
    foreach ($periods as $period) {
        $counts = $DB->get_records('block_mt_ranks_rating_posts', array(
            'courseid' => $courseid,
            'period' => $period->period
        ));
        foreach ($counts as $count) {
            $awardname = get_string('mt_awards:generate_award_rating_posts', 'block_mt',
                date_format(new DateTime($period->period), "Y-n"));
            $awardid = null;

            if ($count->rating_percent >= $bronzeaward) {
                $awardid = BRONZE_AWARD_ID;
            }
            if ($count->rating_percent >= $silveraward) {
                $awardid = SILVER_AWARD_ID;
            }
            if ($count->rating_percent >= $goldaward) {
                $awardid = GOLD_AWARD_ID;
            }
            $recordcountparam = array(
                'userid' => $count->userid,
                'courseid' => $count->courseid,
                'award_name' => $awardname,
                'period' => $period->period,
                'period_type' => RANK_PERIOD_MONTHLY
            );
            $recordcount = $DB->count_records('block_mt_awards_user', $recordcountparam);
            if ($recordcount < 1) {
                $recordcountparam['awardid'] = $awardid;
                $DB->insert_record('block_mt_awards_user', $recordcountparam);
            } else {
                $updateid = $DB->get_field('block_mt_awards_user', 'id', $recordcountparam);
                $recordcountparam['awardid'] = $awardid;
                $recordcountparam['id'] = $updateid;
                $DB->update_record('block_mt_awards_user', $recordcountparam);
            }
        }
        update_last_period_run_awards($paramcourseid, RANK_TYPE_POST_RATING, $period->period);
    }
}

/**
 * generate awards rating posts overall
 *
 *
 * @param string $courseid
 * @return null
 */
function generate_rating_posts_overall_count($courseid) {
    global $DB;

    $awardweight = new stdClass();
    $awardweight->gold = get_awards_settings('mt_awards:rating_posts_gold_weight_value', $courseid);
    $awardweight->silver = get_awards_settings('mt_awards:rating_posts_silver_weight_value', $courseid);
    $awardweight->bronze = get_awards_settings('mt_awards:rating_posts_bronze_weight_value', $courseid);

    $sql = "SELECT userid
        FROM {block_mt_awards_user}
        WHERE courseid=:courseid
        AND award_name like 'Rating posts%'
        GROUP BY userid
        ORDER by userid";

    $studentlist = $DB->get_records_sql($sql, array(
        'courseid' => $courseid
    ));

    foreach ($studentlist as $student) {
        $awardtotal = 0;
        $sql = "SELECT period, awardid
            FROM {block_mt_awards_user}
            WHERE courseid=:courseid
            AND award_name like 'Rating posts%'
            AND userid=:userid";
        $awardlist = $DB->get_records_sql($sql, array(
            'courseid' => $courseid,
            'userid' => $student->userid
        ));
        $awardtotal = calculate_award_total($awardlist, $awardtotal, $awardweight);

        $recordcountparam = array(
            'courseid' => $courseid,
            'userid' => $student->userid,
            'awardtype' => RANK_TYPE_POST_RATING
        );
        $recordcount = $DB->count_records('block_mt_awards_count_all', $recordcountparam);
        if ($recordcount < 1) {
            $recordcountparam['awardtotal'] = $awardtotal;
            $DB->insert_record('block_mt_awards_count_all', $recordcountparam);
        } else {
            $updateid = $DB->get_field('block_mt_awards_count_all', 'id', $recordcountparam);
            $recordcountparam['awardtotal'] = $awardtotal;
            $recordcountparam['id'] = $updateid;
            $DB->update_record('block_mt_awards_count_all', $recordcountparam);
        }
    }
}

/**
 * generate awards rating posts
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_rating_posts($paramcourseid) {
    mtrace(get_string('mt:cron_awards_rating_posts', 'block_mt'));
    generate_awards_rating_posts_monthly($paramcourseid);

    generate_rating_posts_overall_count($paramcourseid);
}

/**
 * generate awards read posts
 *
 * @param string $paramcourseid
 * @return null
 */
function generate_awards_read_posts($paramcourseid) {
    mtrace(get_string('mt:cron_awards_posts_read', 'block_mt'));
    generate_awards_read_posts_monthly($paramcourseid);

    generate_read_posts_overall_count($paramcourseid);
}

/**
 * calculate award total
 *
 * @param array $awardlist
 * @param integer $awardtotal
 * @param array $awardweight
 * @return integer $awardtotal
 */
function calculate_award_total($awardlist, $awardtotal, $awardweight) {
    foreach ($awardlist as $awardentry) {
        $awardperiod = date_create($awardentry->period);
        $currentdate = block_mt_get_current_date();
        $interval = date_diff($currentdate, $awardperiod);
        // Only take the last year.
        if ($interval->format('%y') < 1) {
            switch ($awardentry->awardid) {
                case GOLD_AWARD_ID:
                    $awardtotal = $awardtotal + $awardweight->gold;
                    break;
                case SILVER_AWARD_ID:
                    $awardtotal = $awardtotal + $awardweight->silver;
                    break;
                case BRONZE_AWARD_ID:
                    $awardtotal = $awardtotal + $awardweight->bronze;
                    break;
                default:
                    break;
            }
        }
    }
    return $awardtotal;
}