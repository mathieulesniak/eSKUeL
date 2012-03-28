<?php

class SQLHandler extends SimpleObject implements DBLayer
{
	var $properties = array(
		'hostname',
		'database',
		'username',
		'password',
		'character_set',
		'collate'
	);

	var $private_properties = array(
		'_db_link',
		'_query_id',
		'_error_no',
		'_error_msg',
		'_last_query',
        '_last_results',
        '_last_message'
	);
    
    var $output_fields = array(
        'last_query'    => '_last_query',
        'return_code'   => '_error_no',
        'data'          => '_last_results',
        'message'       => '_last_message'
    );
    
	var $specific_methods = array(
		'check' => array(
			'method' => 'table_verify',
			'name' => 'verify table'
		),
		'analyze' => array(
			'method' => '',
			'name' => ''
		),
		'repair' => array(
			'method' => '',
			'name' => '',
		),
		'optimize' => array(
			'method' => '',
			'name' => ''
		)
	);

	var $field_types = array(
		'numeric'	=> array(
			'bit',
			'tinyint',
			'bool',
			'smallint',
			'mediumint',
			'int',
			'bigint',
			'float',
			'double',
			'decimal'
		),
		'string'	=> array(
			'char',
			'varchar',
			'tinytext',
			'text',
			'mediumtext',
			'longtext',
			'tinyblob',
			'blob',
			'mediumblob',
			'longblog',
			'binary',
			'varbinary',
			'enum',
			'set'
		),
		'datetime'	=> array(
			'date',
			'datetime',
			'timestamp',
			'time',
			'year'
		)
	);

	var $field_functions = array(
		'ASCII',
		'CHAR',
		'SOUNDEX',
		'ENCRYPT',
		'LCASE',
		'UCASE',
		'NOW',
		'PASSWORD',
		'OLD_PASSWORD',
		'COMPRESS',
		'UNCOMPRESS',
		'UTC_DATE',
		'UTC_TIME',
		'UTC_TIMESTAMP',
		'HEX',
		'UNHEX',
		'ENCODE',
		'DECODE',
		'MD5',
		'SHA1',
		'RAND',
		'LAST_INSERT_ID',
		'COUNT',
		'AVG',
		'SUM',
		'CURDATE',
		'CURTIME',
		'FROM_DAYS',
		'FROM_UNIXTIME',
		'PERIOD_ADD',
		'PERIOD_DIFF',
		'TO_DAYS',
		'USER',
		'WEEKDAY',
		'UNIX_TIMESTAMP');

	function __construct($hostname, $username, $password, $database = '')
	{
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;

		$this->connect();
	}

	function connect()
	{
		if ( $this->_db_link == null )
		{
			$this->_db_link = mysql_connect($this->hostname, $this->username, $this->password);
		}

		return $this;
	}
    
    /**
	* Execute a query against the DB server without fetching results
	*
	* @return sql_handler
	* @param string $query
	* @access public
	*/
    function query($query)
    {
        return $this->doQuery($query, false);
    }
    
    /**
	* Execute a query against the DB server and fetch results
	*
	* @return sql_handler
	* @param string $query
	* @access public
	*/
    function queryAndFetch($query)
    {
        return $this->doQuery($query, true);
    }

	private function doQuery($query, $do_fetch)
	{
		$this->connect();
        
        $this->_last_query      = null;
        $this->_last_results    = null;

		$this->_query_id 	    = mysql_query($query, $this->_db_link);
		$this->_last_query 	    = $query;
		$this->_error_no 	    = mysql_errno();
		$this->_error_msg 	    = mysql_error();
        if ( $this->_error_no !== 0 )
        {
                $this->_last_message    = $this->_error_msg;
        }
        else
        {
                $this->_last_message    = mysql_info($this->_db_link);        
        }
        $this->_last_results = new SQLResultset($this, $this->_query_id);
        
        if ( $do_fetch && $this->_error_no == 0 )
        {
                $this->fetchResults();
        }
		return $this;
	}
    
    function fetchResults($start = 0, $nb = 'ALL')
	{
		$this->_last_results->slice($start, $nb);
        return $this;
	}
    
    // Resultset management
	function fetchFieldsFromResultset() {
		$nb_fields 	= mysql_num_fields($this->_query_id);
		$fields 	= array();

		$i = 0;
		while ( $i < $nb_fields ) 
		{
        	$fields[$i] = mysql_fetch_field($this->_query_id, $i)->name;
            $i++;
		}

		return $fields;
	}

	function getSlicedDataFromResultset($start, $length) 
	{
		$results = array();
		mysql_data_seek($this->_query_id, $start);
		$to = $start + $length;

		for ( $i = $start; $i < $to; $i++ ) 
		{
		
			$results[$i] = mysql_fetch_array($this->_query_id, MYSQL_NUM);
		}

		return $results;
	}

    function getResults($from = 0, $nb = 'ALL') {
        return $this->_last_results->slice($from, $nb);
    }

	function numRows()
	{
        if ( is_resource($this->_query_id) )
        {
                return mysql_num_rows($this->_query_id);
        }
        else {
                return false;
        }
	}
    
    function selectDb($db)
    {
        mysql_select_db($db, $this->_db_link);
        return $this;
    }
    
    function serverProcesslist()
    {
        $sql = "SHOW PROCESSLIST;";
        return $this->query($sql)->getResults();
    }



	//
	// DB related functions
	//

	function dbList()
	{
		$sql = "SHOW DATABASES";
		return $this->queryAndFetch($sql);
	}

	function dbCreate($db_name, $db_options = array())
	{
		$valid_options = array('CHARACTER SET', 'COLLATE');
		$sql = sprintf("CREATE DATABASE `%s`", $db_name);
		foreach ( $db_options as $key=>$val )
		{
				if ( in_array($key, $valid_options) )
				{
						$sql .= sprintf(" %s %s", $key, $val);
				}
		}
		
		$this->query($sql);
	}

	function dbDelete($db_name)
	{
		$sql = sprintf("DROP DATABASE `%s`;", $db_name);

		$this->query($sql);
	}

	function dbGetTablesInfos($db_name)
	{
		$sql = sprintf("SHOW TABLE STATUS FROM `%s`;", $db_name);
		$this->queryAndFetch($sql);

		// Alter resultset to comply interface
		$raw_fields = $this->_last_results->fields;
		$to_keep 	= array('Name' 			=> 'Name', 
							'Engine' 		=> 'Engine', 
							'Rows' 			=> 'Rows', 
							'Collation' 	=> 'Collation',
							'Data_length' 	=> 'Size',
							'Index_length' 	=> 'Size'
							);
		$new_fields 	= array_unique(array_values($to_keep));
		$new_fields_inv = array_flip($new_fields);

		$mapping 	= array();
		foreach ( $raw_fields as $key=>$field ) {
			if ( isset($to_keep[$field]) ) 
			{
				$mapping[$key] = $new_fields_inv[$to_keep[$field]];
			}
		}

		
		$new_fields_mapping = array_flip($new_fields);
		$new_records 		= array();

		foreach ( $this->_last_results->records as $key=>$data)
		{
			$new_records[$key] = array();
			foreach ( $data as $field_index=>$field_value )
			{
				if ( isset($mapping[$field_index]) ) 
				{

					if ( isset($new_records[$key][$mapping[$field_index]]) ) {
						$new_records[$key][$mapping[$field_index]] += $field_value;
					}
					else {
						$new_records[$key][$mapping[$field_index]] = $field_value;
					}
					
				}
			}
			$new_records[$key][$new_fields_inv['Size']] = convert_from_bytes($new_records[$key][$new_fields_inv['Size']]);
		}

		$this->_last_results->fields 			= $new_fields;
		$this->_last_results->result_records 	= $new_records;
		$this->_last_results->records 			= $new_records;
		
		return $this;
	}

	//
	// Table related functions
	//

	function tableGetFields($db_name, $table_name)
	{
		$sql = sprintf("DESCRIBE `%s`.`%s`;", $db_name, $table_name);

		return $this->queryAndFetch($sql);
	}

	function tableGetIndexes($db_name, $table_name)
	{
		$sql = sprintf("SHOW INDEX FROM `%s`.`%s`;", $db_name, $table_name);

		return $this->queryAndFetch($sql);
	}
    
    function tableGetInfos($db_name, $table_name)
    {
        $sql = sprintf("SHOW TABLE STATUS FROM `%s` WHERE Name='%s'", $db_name, $table_name);
        
        return $this->queryAndFetch($sql);
    }

	function tableCopy($db_from, $table_from, $db_to, $table_to, $copy_with_data)
	{

		// Get original table description
		$sql = sprintf("SHOW CREATE TABLE `%s`.`%s`", 
						$db_from, $table_from);
		$results = $this->queryAndFetch($sql)->getResults();

		if ( $results !== false ) 
		{
			$sql = str_replace( sprintf('CREATE TABLE `%s`', $table_from), 
								sprintf('CREATE TABLE `%s`.`%s`', $db_to, $table_to), 
								$results['record'][0][1]
								);

			$this->query($sql);
			if ( $copy_with_data ) 
			{
				// Copy records from the old table to the new one
				$sql = sprintf("INSERT INTO `%s`.`%s` SELECT * FROM `%s `.`%s`",
								$db_from, $table_from,
								$db_to, $table_to);
				
				$this->query($sql);
			}
		}
		return $this;
	}

	function tableRename($db_old_name, $table_old_name, $db_new_name, $table_new_name)
	{
		$sql = sprintf("ALTER TABLE `%s`.`%s` RENAME `%s`.`%s`;", $db_old_name, $table_old_name, $db_new_name, $table_new_name);

		return $this->query($sql);
	}
    
    function tableDelete($db_name, $table_name)
    {
        
        
    }


	function tableMove($db_old_name, $table_old_name, $db_new_name, $table_new_name)
	{
		// Get original table description
		$sql = sprintf("SHOW CREATE TABLE `%s`.`%s`", 
						$db_old_name, $table_old_name);
		$results = $this->queryAndFetch($sql)->get_results();

		if ( $results !== false ) 
		{
			$sql = str_replace( sprintf('CREATE TABLE `%s`', $table_old_name), 
								sprintf('CREATE TABLE `%s`.`%s`', $db_new_name, $table_new_name), 
								$results['record'][0][1]
								);

			$this->query($sql);
			// Copy records from the old table to the new one
			$sql = sprintf("INSERT INTO `%s`.`%s` SELECT * FROM `%s `.`%s`",
							$db_old_name, $table_old_name,
							$db_new_name, $table_new_name);
			
			$this->query($sql);

			// Delete old table
			$sql = sprintf("DELETE FROM `%s`.`%s`",
							$db_old_name, $table_old_name);

			$this->query($sql);
		}
	}

	function tableAddField()
	{
	}

	function tableChangeType($db_name, $table_name, $table_type)
	{
		$sql = sprintf("ALTER TABLE `%s`.`%s` ENGINE=%s", $db_name, $table_name, $table_type);

		return $this->query($sql);
	}

	function tableEmpty($db_name, $table_name)
	{
		$sql = sprintf("DELETE FROM `%s`.`%s`;", $db_name, $table_name);

		return $this->query($sql);
	}
	
	
}

?>