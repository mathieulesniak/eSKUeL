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
		$current_table	= getParam('table');

		if ( $current_db )
		{
			$this->current_db = database::load($current_db, $this->_sql_handler);
			if ( $current_table )
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
	* @return void
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

	/**
	* Build the HTML used in side navigation
	*
	* @return string HTML db/table listing
	* @param void
	* @access private
	*/
	private function render_db_table_listing()
	{
		$output = '<ul>' . "\n";
		foreach ( $this->db_list as $database )
		{
			$url = new URLBuilder();
			$url->addParam('db', $database);
			$output .= sprintf('	<li><a href="%s">%s</a>', $url, $database);
			if ( $this->current_db !== NULL && $database == $this->current_db->name )
			{
				if ( is_array($this->current_db->tables) )
				{
					$output .= '	<ul>' . "\n";
					foreach ( $this->current_db->tables as $table )
					{
						$url->addParam('tbl', $table->name);
						$output .= sprtinf('		<li><a href="%s">%s</a></li>', $url, $table->name) . "\n";
					}
					$output .= '	</ul>' . "\n";
				}
			}
			$output .= '	</li>' . "\n";
		}
		$output .= '</ul>' . "\n";

		return $output;
	}

	/**
	* Build the HTML used in main tab when DB is selected and no table selected
	*
	* @return string HTML db/table listing
	* @param void
	* @access private
	*/
	private function render_db_homepage()
	{
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
		$this->template_end();
	}
}

?>