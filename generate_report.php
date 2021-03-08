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
 * Copyright (C) 2014 onwards emeneo (http://www.emeneo.com)
 *
 * @package    blocks_report_free_seats
 * @copyright  2014 onwards emeneo (http://www.emeneo.com)
 * @author     Flotter Totte (flottertotte@emeneo.com)
 */

require_once('../../config.php');

$weeks = required_param('weeks', PARAM_INT);
$self = required_param('self', PARAM_INT);
$waitlist = required_param('waitlist', PARAM_INT);

require_login();
user_download_csv($weeks, $self, $waitlist);

function user_download_csv($weeks, $self, $waitlist) {
    global $DB;
    require_once('csvlib.class.php');

    $fields = array(
            'fullname'  => get_string('coursename', 'block_report_free_seats'),
            'category'  => get_string('category', 'block_report_free_seats'),
            'startdate' => get_string('startdate', 'block_report_free_seats'),
            'freeseats' => get_string('freeseats', 'block_report_free_seats'),
            'enrolmax_users' => get_string('maxseats', 'block_report_free_seats')
            );
    $filename = clean_filename('Free_seats_report');

    if (ob_get_contents()) {
        ob_end_clean();
    }

    $csvexport = new csv_export_writer();
    $csvexport->set_filename($filename);
    $csvexport->add_data($fields);

    $cond = '';
    if ($self == 1 && $waitlist == 0) {
        $cond = 'AND {enrol}.enrol = "self"';
    } else if ($self == 0 && $waitlist == 1) {
        $cond = 'AND {enrol}.enrol = "waitlist"';
    } else if ($self == 1 && $waitlist == 1) {
        $cond = 'AND ({enrol}.enrol = "self" OR {enrol}.enrol = "waitlist")';
    } else {
        $cond = 'AND ({enrol}.enrol != "self" AND {enrol}.enrol != "waitlist")';
    }

    $sql = 'SELECT {course}.id, {course}.fullname, {course}.startdate, {course_categories}.name AS category,
            SUM(DISTINCT({enrol}.customint3)) AS enrolmax_users,
            COUNT({user_enrolments}.enrolid) AS enrolled_users
            FROM {course}
            RIGHT JOIN {enrol} ON ({course}.id = {enrol}.courseid)
            LEFT JOIN {user_enrolments} ON ({user_enrolments}.enrolid = {enrol}.id)
            LEFT JOIN {course_categories} ON ({course}.category = {course_categories}.id)
            WHERE {course}.category <> "0"
            '.$cond.'
            AND {enrol}.status = "0"
            AND TO_DAYS(FROM_UNIXTIME({course}.startdate)) BETWEEN TO_DAYS(CURDATE())
            AND (TO_DAYS(CURDATE()) + ?)
            GROUP BY {course}.id';

    $weeks = $weeks ?: 28;
    $courses = $DB->get_records_sql( $sql, array($weeks) );

    foreach ($courses as $course) {
        if ( !$course ) {
            continue;
        }

        $coursedata = array();
        foreach ($fields as $field => $unused) {
            if ($field == 'startdate') {
                $date = usergetdate( $course->$field );
                $course->$field = $date['mday'] .'-' . $date['month'] . '-' . $date['year'];
            }
            if ($field == 'freeseats') {
                if ( $course->enrolmax_users == 0 ) {
                    $course->$field = 'Unlimited';
                } else {
                    $course->$field = $course->enrolmax_users - $course->enrolled_users;
                }
            }

            if (is_array($course->$field)) {
                $coursedata[] = reset($course->$field);
            } else {
                $coursedata[] = $course->$field;
            }
        }

        if ( $course->enrolmax_users != 0 && ( $course->enrolmax_users - $course->enrolled_users ) == 0 ) {
            unset($coursedata);
        }

        $csvexport->add_data($coursedata);
    }

    $csvexport->download_file();
    die;
}