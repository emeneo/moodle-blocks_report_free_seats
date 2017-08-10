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
 * @subpackage coursereport
 * @copyright  2014 onwards emeneo (http://www.emeneo.com)
 * @author     Flotter Totte (flottertotte@emeneo.com)
 */

defined('MOODLE_INTERNAL') || die();

class block_coursereport extends block_base {

    function init() {
        $this->title = get_string('coursereport', 'block_coursereport');
    }

    function applicable_formats() {
        return array('site' => true);
    }

    function get_content() {
        global $CFG;
        if ( $this->content !== null ) {
            return $this->content;
        }

        $formattr = array('action' => $CFG->wwwroot.'/blocks/coursereport/generate_report.php', 'method' => 'POST');
        $inputattr1 = array('type' => 'hidden', 'name' => 'weeks', 'value' => @$this->config->weeks);
        $inputattr2 = array('type' => 'hidden', 'name' => 'self', 'value' => @$this->config->selfenrol);
        $inputattr3 = array('type' => 'hidden', 'name' => 'waitlist', 'value' => @$this->config->waitlistenrol);
        $submitattr = array( 'type' => 'submit', 'class' => 'btn btn-success');
        $this->content = new stdClass();
        $this->content->text = html_writer::start_tag( 'form', $formattr);
        $this->content->text .= html_writer::empty_tag( 'input', $inputattr1);
        $this->content->text .= html_writer::empty_tag( 'input', $inputattr2);
        $this->content->text .= html_writer::empty_tag( 'input', $inputattr3);
        $this->content->text .= html_writer::tag( 'button', get_string( 'getreport', 'block_coursereport' ), $submitattr );
        $this->content->text .= html_writer::end_tag('form');

        return $this->content;
    }
}