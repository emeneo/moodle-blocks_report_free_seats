<?php

require_once('../../config.php');
require_login();
user_download_csv($_POST['weeks']);

function user_download_csv($weeks) {
    global $DB;
    require_once('csvlib.class.php');

    $fields = array(
            'fullname'  => get_string( 'coursename', 'block_coursereport' ),
            'category'  => get_string( 'category', 'block_coursereport' ),
            'startdate' => get_string( 'startdate', 'block_coursereport' ),
            'freeseats' => get_string( 'freeseats', 'block_coursereport' ),
			'enrolmax_users' => get_string( 'maxseats', 'block_coursereport' )
            );
    $filename = clean_filename('Course Report');
	
    /* ob_end_clean(); */
	if (ob_get_contents()) ob_end_clean();
	
    $csvexport = new csv_export_writer();
    $csvexport->set_filename($filename);
    $csvexport->add_data($fields);

    $sql = 'SELECT {course}.id, {course}.fullname, {course}.startdate, {course_categories}.name AS category, 
            SUM(DISTINCT({enrol}.customint3)) AS enrolmax_users,
            COUNT({user_enrolments}.enrolid) AS enrolled_users   
            FROM {course}
            LEFT JOIN {enrol} ON ({course}.id = {enrol}.courseid)
            LEFT JOIN {user_enrolments} ON ({user_enrolments}.enrolid = {enrol}.id)
            LEFT JOIN {course_categories} ON ({course}.category = {course_categories}.id)
            WHERE {course}.category <> "0"
            AND {enrol}.enrol = "waitlist"
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

        $course_data = array();
        foreach ($fields as $field=>$unused) {
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
                $course_data[] = reset($course->$field);
            } else {
                $course_data[] = $course->$field;
            }
        }

        if ( $course->enrolmax_users != 0 && ( $course->enrolmax_users - $course->enrolled_users ) == 0 ) {
            unset($course_data);
        } 

        $csvexport->add_data($course_data);
    }

    $csvexport->download_file();
    die;
}