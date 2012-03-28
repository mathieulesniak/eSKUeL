<?php

interface DBLayer 
{

	// Reminder only
	// Needed member vars
	// var $specific_methods = array();
	// var $field_types = array();
	// var $field_functions = array();

	function connect();

	function query($sql);
    
    function queryAndFetch($sql);

	function getResults($from, $nb);
    
    function fetchResults($from, $nb);

	function numRows();

	function fetchFieldsFromResultset();

	function getSlicedDataFromResultset($start, $length);

	// DB related functions

	function dbList();

	function dbGetTablesInfos($db_name);

	function dbCreate($db_name, $db_options = array());

	function dbDelete($db_name);

	// Table related functions

	function tableGetFields($db_name, $table_name);

	function tableGetIndexes($db_name, $table_name);

	function tableCopy($db_from, $table_from, $db_to, $table_to, $copy_with_data);

	function tableMove($db_from, $table_from, $db_to, $table_to);

	function tableRename($db_old_name, $old_name, $db_new_name, $new_name);

	function tableAddField();

	function tableSetType($db_name, $table_name, $type);
    
    function tableGetType($db_name, $table_name);

	function tableEmpty($db_name, $table_name);

}

?>