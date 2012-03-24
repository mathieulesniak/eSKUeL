<?php
class sql_resultset extends simple_object 
{
	var $properties = array(
		'fields',
		'records'
		);

	var $private_properties = array(
		);


	function __construct($fields, $records) {
		$this->fields = $fields;
		$this->records = $records;
	}

	function has_field($field) {
	
		foreach ( $this->fields as $current_field ) { 
			if ( $current_field == $field ) {
				return true;
			}
		}

		return false;
	}

	function get_data_from_field_and_record($field, $record) {
		if ( $this->has_field($field) ) {
			if ( isset($this->records[$record]) ) {

			}
			else {
				// Exception unknown record
			}
		}
		else {
			// Exception unkonwn field
		}
	}
}

?>