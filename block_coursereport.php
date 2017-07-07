<?php

class block_coursereport extends block_base {
	
	function init() {
		$this->title = get_string( 'coursereport', 'block_coursereport' );
	}
	// end function init()

	function applicable_formats() {
        return array('site' => true);
    }
	
	function get_content() {
		global $CFG;
		if ( $this->content !== NULL ) {
			return $this->content;
		}

		$form_attr = array( 'action'	=> $CFG->wwwroot.'/blocks/coursereport/generate_report.php',
							'method'	=> 'POST' );

		$input_attr = array( 'type'	=> 'hidden',
							'name'	=> 'weeks',
							'value'	=> $this->config->weeks );
		
		$submit_attr = array( 'type' => 'submit',
							'class' => 'btn btn-success' );

		$this->content			= new stdClass();
		$this->content->text	= html_writer::start_tag( 'form', $form_attr );
		$this->content->text	.= html_writer::empty_tag( 'input', $input_attr );
		$this->content->text	.= html_writer::tag( 'button', get_string( 'getreport', 'block_coursereport' ), $submit_attr );
		$this->content->text	.= html_writer::end_tag( 'form' );

		
		return $this->content;
	}
}