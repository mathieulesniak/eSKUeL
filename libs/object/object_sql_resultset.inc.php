<?php
class SQLResultset extends SimpleObject implements Countable, ArrayAccess
{
	var $properties = array(
		'fields',
		'records',
		'result_records',
		'total_num_rows'
		);

	var $private_properties = array(
		'_sql_handler',
		'_sql_resource',
		);
	
	var $output_fields = array(
        'field'    => 'fields',
        'record'   => 'result_records'
		);


	function __construct($sql_handler, $sql_resource)
	{
		$this->_sql_handler 	= $sql_handler;
		$this->_sql_resource 	= $sql_resource;

		$this->total_num_rows 	= $sql_handler->numRows();
		$this->fields 			= $sql_handler->fetchFieldsFromResultset();
		$this->records 			= array();
		
	}



	function has_field($field)
	{
		return in_array($this->fields, $field);	
	}
	
	function get_data_from_field_and_record($field, $record)
	{
		if ( $this->has_field($field) ) {
			if ( isset($this->records[$record]) ) {

			}
			else {
				// Exception unknown record
			}
		}
		else {
			// Exception unknown field
		}
	}
	
	function slice($start, $length)
	{
		$length	= ( $length == 'ALL' ) ? ($this->total_num_rows - $start) : $length;
		$i 		= $start;
		$end 	= $start + $length;

		$result = array();
		
		while ( $i < $end ) {			
			if ( !isset($this->records[$i]) ) {
				$this->getDataFromHandler($i, 1);
			}

			$result[strval($i)] = $this->records[$i]; 
			$i++;
		}

		$this->result_records = $result;

		return $this;
	}
	
	function fetchAll() {
		$this->slice(0, 'ALL');
	}
	
	private function getDataFromHandler($start, $length) 
	{
		$this->records =  $this->records + $this->_sql_handler->getSlicedDataFromResultset($start, $length);
	}


	//
	// Implementation functions
	//
	
	// Countable
	function count()
	{
		return count($this->records);
	}
	
	//ArrayAccess
	function offsetGet($index)
	{
		if ( $index >= 0 && $index < $this->total_num_rows ) 
		{
			if ( !isset($this->records[$index]) ) 
			{
				$this->getDataFromHandler($index, 1);		
			}

			return $this->records[$index];	

		}
		else {
			return false;
		}
		
	}
	
	function offsetSet($index, $value)
	{
		$this->records[$index] = $value;
	}
	
	function offsetExists($index)
	{
		return ($index >= 0 && $index < $this->total_num_rows);
	}
	
	function offsetUnset($index)
	{
		unset($this->records[$index]);
	}
}

?>