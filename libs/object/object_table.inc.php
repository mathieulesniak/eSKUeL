<?php


class Table extends SimpleObject
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

	static function load($table_name, $db_name, $sql_handler)
	{
		$table = new Table($sql_handler);
		
		$table->name 		= $table_name;
		$table->database 	= $db_name;
		$table->_sql_handler->selectDb($table->database->name);
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
	static function loadFromArray($data, $sql_handler)
	{
		$table = new Table($sql_handler);

		foreach ( $data as $key=>$val )
		{
			$key = strtolower($key);
			if ( $table->hasPublicProperty($key) )
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
	function getFields()
	{
		return $this->_sql_handler->tableGetFields($this->database->name, $this->name);

/*
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
		}*/
	}
	
	function addField()
	{
	}
	
	
	/**
	* Build an array of Index objects of current table
	*
	* @return void
	* @param void
	* @access public
	*/
	function getIndexes()
	{
		return $this->_sql_handler->tableGetIndexes($this->database->name, $this->name);
		/*if ( $indexes_data !== false )
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

		return $this;*/
	}

	function getInfos()
	{
		return $this->_sql_handler->tableGetInfos($this->database->name, $this->name);
	}
	

	function changeType()
	{
	}

	function export()
	{
	}

	function rename($new_name)
	{
	}

	function copy($db_to, $table_to, $copy_with_data = false)
	{
		return $this->_sql_handler->tableCopy($this->database->name, 
										$this->name, 
										$db_to, $table_to, $copy_with_data);
	}

	function move($db_to, $table_to)
	{
		return $this->_sql_handler->tableMove($this->database->name,
												$this->name,
												$db_to,
												$table_to);
	}

	function delete()
	{
		return $this->_sql_handler->tableDelete($this->database->name,
												 $this->name);
	}

	function query($query)
	{
		return $this->_sql_handler->query($query)->getResults($from, $nb);
	}
	
	function doEmpty()
	{
		return $this->_sql_handler->tableEmpty($this->database->name,
												$this->name);
	}

}

?>