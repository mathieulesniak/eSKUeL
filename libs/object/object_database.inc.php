<?php

class Database extends SimpleObject
{

	var $properties = array(
		'name',
		'tables',
		'total_rows',
		'total_size'
	);

	var $private_properties = array(
		'_sql_handler'
	);
    
	function __construct($name, $sql_handler)
	{
		$this->name				= $name;
		$this->_sql_handler		= $sql_handler;
		$this->tables			= array();
		$this->total_rows		= 0;
		$this->total_size		= 0;
	}

	static function load($name, $sql_handler)
	{
		$database = new Database($name, $sql_handler);
        $database->selectDB();

		return $database;
	}
    
    function selectDB()
    {
        $this->_sql_handler->selectDb($this->name);
        
        return $this;    
    }

	function create()
	{
        return $this->_sql_handler->dbCreate($this->name);
	}

	function delete()
	{
		return $this->_sql_handler->dbDelete($this->name);
	}

	function getTables()
	{
        return $this->_sql_handler->dbGetTablesInfos($this->name);

	}
    
    function query($query, $from, $nb)
    {
        return $this->_sql_handler->query($query)->getResults($from, $nb);
    }
    
    function explain($query)
    {
        return $this->_sql_handler->explain($query)->getResults();
    }

}

?>