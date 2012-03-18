<?php

class sql_handler extends simple_object implements db_layer
{
	var $properties = array(
		'hostname',
		'database',
		'username',
		'password'
	);

	var $private_properties = array(
		'_db_link',
		'_query_id',
		'_error_no',
		'_error_msg'
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

	function query($query)
	{
		$this->connect();

		$this->_query_id = mysql_query($query, $this->_db_link);
		$this->_error_no = mysql_errno();
		$this->_error_msg = mysql_error();

		return $this;
	}

	function get_results( $type = MYSQL_BOTH )
	{
		$results = array();
		while ( $record = mysql_fetch_array($this->_query_id, $type) ) {
			$results[] = $record;
		}

		return $results;
	}

	function num_rows()
	{
		return mysql_num_rows($this->_query_id);
	}


	//
	// DB related functions
	//

	function db_list()
	{
		$db_list = array();
		$sql = "SHOW DATABASES";

		$results = $this->query($sql)->get_results(MYSQL_ASSOC);
		if ( $results !== false ) {
			foreach ( $results as $resultset ) {
				$db_list[] = $resultset['Database'];
			}
		}

		return $db_list;
	}

	function db_create($db_name)
	{
	}

	function db_delete($db_name)
	{
		$sql = sprintf("DROP DATABASE `%s`;", $db_name);

		$this->query($sql);
	}

	function db_get_tables_infos($db_name)
	{
		$sql = sprintf("SHOW TABLE STATUS FROM `%s`;", $db_name);

		return  $this->query($sql)->get_results(MYSQL_ASSOC);
	}

	// Table related functions

	function table_get_fields($db_name, $table_name)
	{
		$sql = sprintf("DESCRIBE `%s`.`%s`;", $db_name, $table_name);

		return $this->query($sql)->get_results(MYSQL_ASSOC);
	}

	function table_get_indexes($db_name, $table_name)
	{
		$sql = sprintf("SHOW INDEX FROM `%s`.`%s`;", $db_name, $table_name);

		return $this->query($sql)->get_results(MYSQL_ASSOC);
	}

	function table_copy($db_from, $table_from, $db_to, $table_to, $copy_with_data)
	{
	}

	function table_rename($db_old_name, $table_old_name, $db_new_name, $table_new_name)
	{
		$sql = sprtinf("ALTER TABLE `%s`.`%s` RENAME `%s`.`%s`;", $db_old_name, $table_old_name, $db_new_name, $table_new_name);

		return $this->query($sql);
	}

	function table_move()
	{
	}

	function table_add_field()
	{
	}

	function table_change_type()
	{
	}

	function table_empty($db_name, $table_name) {
		$sql = sprintf("DELETE FROM `%s`.`%s`;", $db_name, $table_name);

		return $this->query($sql);
	}
}

?>