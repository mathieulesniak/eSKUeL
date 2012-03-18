<?php


class table extends simple_object
{

	var $properties = array(
		'name',
		'engine',
		'version',
		'row_format',
		'rows',
		'avg_row_length',
		'data_length',
		'max_data_length',
		'index_length',
		'fields',
		'indexes',
		'comment',
		'database'
	);

	var $private_properties = array(
		'_sql_handler'
	);


	function __construct($sql_handler)
	{
		$this->_sql_handler = $sql_handler;
		$this->fields		= array();
		$this->indexes		= array();
	}

	static function load($table_name, $sql_handler)
	{
		$table = new table($sql_handler);
		$table->name = $table_name;

		return $table;
	}

	/**
	* Create a table from an associative array
	*
	* @return Table object
	* @param array $data
	* @param sql_handler $sql_handler
	* @access public
	*/
	static function load_from_array($data, $sql_handler)
	{
		$table = new table($sql_handler);

		foreach ( $data as $key=>$val )
		{
			$key = strtolower($key);
			if ( $table->has_public_property($key) )
			{
				$table->$key = $val;
			}
		}

		return $table;
	}

	/**
	* Build an array of Field objects of current table
	*
	* @return void
	* @param void
	* @access public
	*/
	function get_fields()
	{
		$fields_data = $this->_sql_handler->table_get_fields($this->database->name, $this->name);
		if ( $fields_data !== false )
		{
			$fields = array();
			foreach ( $fields_data as $resultset )
			{
				if ( isset($resultset['Field']) )
				{
					$current_field = field::load_from_array($resultset, $this->_sql_handler);

					$fields[$resultset['Field']] = $current_field;
				}
			}

			$this->fields = $fields;
		}
	}
	/**
	* Build an array of Index objects of current table
	*
	* @return void
	* @param void
	* @access public
	*/
	function get_indexes()
	{
		$indexes_data = $this->_sql_handler->table_get_indexes($this->database->name, $this->name);
		if ( $indexes_data !== false )
		{
			$indexes = array();
			foreach ( $indexes_data as $resultset )
			{
				if ( isset($resultset['Key_name']) )
				{
					$current_index = index::load_from_array($resultset, $this->_sql_handler);

					$indexes[$resultset['Key_name']] = $current_index;
				}
			}
			$this->indexes = $indexes;
		}

		return $this;
	}

	function add_field()
	{
	}

	function change_type()
	{
	}

	function export()
	{
	}

	function rename($new_name)
	{
	}

	function copy()
	{
	}

	function move()
	{
	}

	function delete()
	{
	}

}

?>