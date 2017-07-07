<?php

class block_coursereport_edit_form extends block_edit_form {
	protected function specific_definition($mform) {
		$mform->addElement( 'header', 'configheader', get_string('blocksettings', 'block_coursereport') );

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
		$select = $mform->addElement( 'select', 'config_weeks', get_string('blockweeks', 'block_coursereport'), $options );
		$select->setSelected('28');
	}
}