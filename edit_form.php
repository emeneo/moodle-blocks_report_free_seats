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
 * @package    blocks
 * @subpackage report_free_seats
 * @copyright  2014 onwards emeneo (http://www.emeneo.com)
 * @author     Flotter Totte (flottertotte@emeneo.com)
 */

defined('MOODLE_INTERNAL') || die();

class block_report_free_seats_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        $mform->addElement( 'header', 'configheader', get_string('blocksettings', 'block_report_free_seats') );
        $options = array(
            '7' => '1',
            '14' => '2',
            '21' => '3',
            '28' => '4',
            '35' => '5',
            '42' => '6',
            '49' => '7',
            '56' => '8',
            '63' => '9',
            '70' => '10',
            );
        $select = $mform->addElement( 'select', 'config_weeks', get_string('blockweeks', 'block_report_free_seats'), $options );
        $select->setSelected('28');
        $mform->addHelpButton('config_weeks', 'blockweeks', 'block_report_free_seats');
        // $mform->addElement('html', '<label><strong>'.get_string('enrolment_plugins', 'block_report_free_seats').'</strong></label>');
        $mform->addElement('advcheckbox', 'config_selfenrol', get_string('self_enrol', 'block_report_free_seats'));
		$mform->setDefault('config_selfenrol', 1);
        $mform->addHelpButton('config_selfenrol', 'self_enrol', 'block_report_free_seats');
        $mform->addElement('advcheckbox', 'config_waitlistenrol', get_string('waitlist_enrol', 'block_report_free_seats'));
		$mform->setDefault('config_waitlistenrol', 1);
        $mform->addHelpButton('config_waitlistenrol', 'waitlist_enrol', 'block_report_free_seats');
    }
}