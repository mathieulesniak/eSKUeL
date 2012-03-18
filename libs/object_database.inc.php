<?php
class database extends simple_object {
	var $properties = array(
		'name', 
		'tables',
		'total_rows',
		'total_size'
		);

	var $private_properties = array(
		'_sql_handler'
		);
	

	function __construct($name, $sql_handler) {
		$this->name 			= $name;
		$this->_sql_handler 	= $sql_handler;
		$this->tables 			= array();
		$this->total_rows		= 0;
		$this->total_size		= 0;
	}

	static function load($name, $sql_handler) {
		$database = new database($name, $sql_handler);
		$database->get_tables();

		return $database;
	}

	function create() {

	}

	function delete() {
		$this->_sql_handler->db_delete($this->name);
	}

	function get_tables() {
		$tables_data = $this->_sql_handler->db_get_tables_infos($this->name);

		if ( $tables_data !== false ) {
			$tables = array();
			foreach ( $tables_data as $resultset ) {
				if ( isset($resultset['Name']) ) {
					$current_table = table::load_from_array($resultset, $this->_sql_handler);
					$current_table->database = $this;

					// Compute stats
					$this->total_size 		+= $current_table->data_length + $current_table->index_length;
					$this->total_rows 		+= $current_table->rows;

					$tables[$resultset['Name']] = $current_table;
				}
			}
			$this->tables = $tables;
		}
		else {
			throw new Exception("Unable to load table list from " . $this->name, 1);
		}
	
		return $this;
	}

}


?>