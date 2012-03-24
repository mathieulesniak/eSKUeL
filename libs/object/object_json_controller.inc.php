<?php
class json_controller extends simple_object {

	var $properties = array(
		'answer'
	);

	var $private_properties = array(
        '_received_json',
		'_sql_handler',
        '_scope',
        '_method',
        '_parameters'
	);

	function __construct($sql_handler)
	{
		$this->_sql_handler = $sql_handler;
	}



	function receive()
	{
		$received_json = getPostParam('json');
		if ( $received_json != NULL )
		{
			$this->_received_json = json_decode($received_json);
            
            if ( isset($this->_received_json->path) ) {
                $json_path = explode('/', $this->_received_json->path);

                // Build scope and method
                $this->_scope    = isset($json_path[1]) ? $json_path[1] : '';
                $this->_method   = isset($json_path[2]) ? $json_path[2] : '';
                
                $this->_parameters = new stdClass();
                // Build parameters
                foreach ( $this->_received_json as $key=>$val ) {
                    if ( $key != 'path' ) {
                        $this->_parameters->$key = $val;
                    }
                }
            }
            else 
			{
				throw new ObjectException( ObjectException::MISSING_JSON_PARAMETER );
			}
            
          
            
            
			if ( $this->_method != '' && $this->_scope != '' /* && isset($this->parameters)*/ ) 
			{
				switch ( $this->_scope ) 
				{
					case 'server':
						$this->handle_server_scope();
					break;

					case 'db':
						$this->handle_db_scope();
					break;

					case 'tbl':
						$this->handle_tbl_scope();
					break;

					default:
						throw new ObjectException( ObjectException::UNKNOWN_JSON_SCOPE, null );
						
					break;
				}

				$this->send_answer();
			}
			else 
			{
				throw new ObjectException( ObjectException::MISSING_JSON_PARAMETER );
			}
		}

	}

	private function handle_server_scope() 
	{
		switch ($this->_method)
		{
			case 'processlist':
                $this->answer = $this->_sql_handler->server_processlist()->to_JSON();
			break;


			case 'db_list':
				$this->answer = $this->_sql_handler->db_list()->to_JSON();
			break;

			default:
				$this->set_error(_('Unknown method'));
			break;
		}
	}

	private function handle_db_scope() 
	{
		$mandatory = array('db');
		if ( $this->check_parameters($mandatory) ) {
			$database = database::load($this->_parameters->db, $this->_sql_handler);
			
			switch ($this->_method) 
			{
				case 'create':

				break;

				case 'delete':

				break;

				case 'get_tables':
					$database->get_tables()->to_JSON();
				break;

				default:
					$this->set_error(_('Unknown method'));
				break;
			}
		}
	}

	private function handle_tbl_scope() 
	{
		$mandatory = array('db', 'tbl');
		if ( $this->check_parameters($mandatory) ) {
			$database 	= database::load($this->_parameters->db, $this->_sql_handler);
			$table 		= table::load($this->_parameters->tbl, $database, $this->_sql_handler);

			switch ($this->_method) 
			{
				case 'get_fields':
					$this->answer = $table->get_fields()->to_JSON();
				break;

				case 'get_indexes':
					$this->answer = $table->get_indexes()->to_JSON();
				break;

				case 'copy':
					$mandatory = array('db_to', 'tbl_to', 'copy_data');
					if ( $this->check_parameters($mandatory) ) {
						$this->answer = $table->copy(
														$this->_parameters->db_to, 
														$this->_parameters->tbl_to, 
														$this->_parameters->copy_data
													)->to_JSON();
					}
					
				break;

				case 'move':
	                $mandatory = array('db_to', 'tbl_to');
	                if ( $this->check_parameters($mandatory) ) {
	                    $this->answer = $table->move($this->_parameters->db_to, $this->_parameters->tbl_to)->to_JSON();
	                }

				break;

				case 'rename':

				break;

				case 'add_field':

				break;

				case 'change_type':

				break;
	            
	            case 'query':
	                $mandatory = array('query');
					if ( $this->check_parameters($mandatory) ) {
	                    $this->answer = $table->query($this->_parameters->query)->to_JSON();
	                }
	                break;
	        
				case 'empty':
	                $this->answer = $table->do_empty()->to_JSON();
				break;

				default:
					$this->set_error(_('Unknown method'));
				break;
			}
		}

	}

	private function check_parameters($mandatory) 
	{
		foreach ( $mandatory as $parameter )
		{
			if ( !isset($this->_parameters->$parameter) )
			{
				// TODO : Throw Exception ?			
				$this->set_error(_('Missing parameter :') . $parameter);
				return false;
			}
		}
		
		return true;	
	}

	private function set_error($message) 
	{
		$this->answer = new stdClass();
		$this->answer->data = null;
		$this->answer->message = $message;
        $this->answer->last_query = null;
		$this->answer->return_code = -1;
	}

	private function send_answer() 
	{
		
		Header("Content-type: application/json");
		echo json_encode($this->answer);
	}
}

?>