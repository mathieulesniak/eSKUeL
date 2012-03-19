<?php

interface db_layer 
{

	// Reminder only
	// Needed member vars
	// var $specific_methods = array();
	// var $field_types = array();
	// var $field_functions = array();

	function connect();

	function query($sql);

	function get_results();

	function num_rows();

	// DB related functions

	function db_list();

	function db_get_tables_infos($db_name);

	function db_create($db_name, $db_options = array());

	function db_delete($db_name);

	// Table related functions

	function table_get_fields($db_name, $table_name);

	function table_get_indexes($db_name, $table_name);

	function table_copy($db_from, $table_from, $db_to, $table_to, $copy_with_data);

	function table_move();

	function table_rename($db_old_name, $old_name, $db_new_name, $new_name);

	function table_add_field();

	function table_change_type($db_name, $table_name, $type);

	function table_empty($db_name, $table_name);

}

?>