<?php

class controller extends simple_object
{
	var $properties = array(
		'current_db',
		'current_table',
		'current_action',
		'db_list'
	);

	var $private_properties = array(
		'_sql_handler'
	);

	function __construct($sql_handler)
	{
		$this->_sql_handler = $sql_handler;
		$this->db_list		= array();

		// Get current state from REQUEST
		$current_db		= getParam('db');
		$current_table	= getParam('tbl');

		if ( $current_db != '' )
		{
			$this->current_db = database::load($current_db, $this->_sql_handler);
			if ( $current_table != '' )
			{
				$this->current_table = table::load($current_table, $this->_sql_handler, $this->current_db);
			}
		}

		$this->current_action	= getParam('action', '');

		$this->build_db_array();
	}

	/**
	* Construct internal array of server databases
	*
	* @return voide
	* @param void
	* @access private
	*/
	private function build_db_array()
	{
		$db_list = $this->_sql_handler->db_list();
		if ( $db_list !== false )
		{
			$this->db_list = $db_list;
		}
	}

	private function template_start()
	{
		$template_data = array();

		$this->template_render('start', $template_data);
	}

	private function template_db_list()
	{
		$template_data = array();
		$template_data['db_list']		= $this->db_list;
		$template_data['selected_db']	= $this->current_db->name;
		$template_data['table_list']	= $this->current_db->tables;

		$this->template_render('db_list', $template_data);
	}

	private function template_end()
	{
		$template_data = array();
		$this->template_render('end', $template_data);
	}

	/**
	* Build the HTML used in main tab when DB is selected and table is selected
	*
	* @return void
	* @param void
	* @access private
	*/
	private function template_table_homepage()
	{
		$template_data = array();

		$this->template_render('table_homepage', $template_data);
	}

	private function template_db_homepage()
	{
		$template_data = array();
		$template_data['table_list']	= $this->current_db->tables;

		$this->template_render('db_homepage', $template_data);
	}

	private function template_render($template, $template_data)
	{
		if ( is_file('libs/views/template_' . $template . '.php') )
		{
			foreach ( $template_data as $key=>$val )
			{
				$$key = $val;
			}

			include 'libs/views/template_' . $template . '.php';
		}
		else
		{
			throw new Exception( ObjectException::TEMPLATE_NOT_FOUND, $template);
		}
	}

	function render()
	{
		$this->template_start();
		$this->template_db_list();

		if ( $this->current_db == NULL )
		{

		}
		else {
			if ( $this->current_table != NULL )
			{
				switch ( $this->current_action )
				{
					default:
						$this->template_table_homepage();
					break;
				}
			}
			else
			{
				$this->template_db_homepage();
			}
		}

		$this->template_end();
	}
}

?>