<?php

class sql_handler extends simple_object implements db_layer
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
		if ( $this->_db_link == NULL )
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
        return $this->do_query($query, false);
    }
    
    /**
	* Execute a query against the DB server and fetch results
	*
	* @return sql_handler
	* @param string $query
	* @access public
	*/
    function query_and_fetch($query)
    {
        return $this->do_query($query, true);
    }

	private function do_query($query, $do_fetch)
	{
		$this->connect();
        
        $this->_last_query      = NULL;
        $this->_last_results    = NULL;

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
        
        if ( $do_fetch && $this->_error_no == 0 )
        {
                $this->fetch_results();
        }
		return $this;
	}

	private function fetch_results()
	{
		$results        = array();
        $results_fields = array();
        if ( $this->num_rows() )
        {
                while ( $record = mysql_fetch_array($this->_query_id, MYSQL_NUM) )
                {
                    $results[] = $record;
                }
        }
        if ( count($results) )
        {
                $i = 0;
                $nb_fields = mysql_num_fields($this->_query_id);
                
                while ( $i < $nb_fields ) {
                        $results_fields[$i] = mysql_fetch_field($this->_query_id, $i)->name;
                        $i++;
                }
        }
        
        $this->_last_results = array('field' => $results_fields, 'record' => $results);
        return $this;
	}
    
    function get_results() {
        return $this->_last_results;
    }

	function num_rows()
	{
        if ( is_resource($this->_query_id) )
        {
                return mysql_num_rows($this->_query_id);
        }
        else {
                return false;
        }
	}
    
    function select_db($db)
    {
        mysql_select_db($db, $this->_db_link);
        return $this;
    }
    
    function server_processlist()
    {
        $sql = "SHOW PROCESSLIST;";
        return $this->query_and_fetch($sql);
    }



	//
	// DB related functions
	//

	function db_list()
	{
		$db_list = array();
		$sql = "SHOW DATABASES";

		return $this->query_and_fetch($sql);
	}

	function db_create($db_name, $db_options = array())
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

	function db_delete($db_name)
	{
		$sql = sprintf("DROP DATABASE `%s`;", $db_name);

		$this->query($sql);
	}

	function db_get_tables_infos($db_name)
	{
		$sql = sprintf("SHOW TABLE STATUS FROM `%s`;", $db_name);

		return $this->query($sql)->get_results(MYSQL_ASSOC);
	}

	// Table related functions

	function table_get_fields($db_name, $table_name)
	{
		$sql = sprintf("DESCRIBE `%s`.`%s`;", $db_name, $table_name);

		return $this->query_and_fetch($sql);
	}

	function table_get_indexes($db_name, $table_name)
	{
		$sql = sprintf("SHOW INDEX FROM `%s`.`%s`;", $db_name, $table_name);

		return $this->query_and_fetch($sql);
	}

	function table_copy($db_from, $table_from, $db_to, $table_to, $copy_with_data)
	{
		return $this;
	}

	function table_rename($db_old_name, $table_old_name, $db_new_name, $table_new_name)
	{
		$sql = sprintf("ALTER TABLE `%s`.`%s` RENAME `%s`.`%s`;", $db_old_name, $table_old_name, $db_new_name, $table_new_name);

		return $this->query($sql);
	}

	function table_move($db_old_name, $table_old_name, $db_new_name, $table_new_name)
	{
	}

	function table_add_field()
	{
	}

	function table_change_type($db_name, $table_name, $table_type)
	{
		$sql = sprintf("ALTER TABLE `%s`.`%s` ENGINE=%s", $db_name, $table_name, $table_type);

		return $this->query($sql);
	}

	function table_empty($db_name, $table_name)
	{
		$sql = sprintf("DELETE FROM `%s`.`%s`;", $db_name, $table_name);

		return $this->query($sql);
	}
	
	
}

?>