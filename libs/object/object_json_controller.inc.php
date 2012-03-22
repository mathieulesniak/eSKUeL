<?php
class json_controller extends simple_object {

	var $properties = array(
		'received_json',
		'answer'
	);

	var $private_properties = array(
		'_sql_handler',
        'scope',
        'method',
        'parameters'
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
			$this->received_json = json_decode($received_json);
            
            if ( isset($this->received_json->path) ) {
                $json_path = explode('/', $this->received_json->path);

                // Build scope and method
                $this->scope    = isset($json_path[1]) ? $json_path[1] : '';
                $this->method   = isset($json_path[2]) ? $json_path[2] : '';
                
                $this->parameters = new stdClass();
                // Build parameters
                foreach ( $this->received_json as $key=>$val ) {
                    if ( $key != 'path' ) {
                        $this->parameters->$key = $val;
                    }
                }
            }
            else 
			{
				throw new Exception( ObjectException::JSON_MISSING_PARAMETER );
			}
            
          
            
            
			if ( $this->method != '' && $this->scope != '' /* && isset($this->parameters)*/ ) 
			{
				switch ( $this->scope ) 
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
				}

				$this->send_answer();
			}
			else 
			{
				throw new Exception( ObjectException::JSON_MISSING_PARAMETER );
			}
		}

	}

	private function handle_server_scope() 
	{
		switch ($this->method)
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
		$database = database::load($this->received_json->parameters->db, $this->_sql_handler);
		
		switch ($this->method) 
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

	private function handle_tbl_scope() 
	{

		$database 	= database::load($this->parameters->db, $this->_sql_handler);
		$table 		= table::load($$this->parameters->tbl, $database, $this->_sql_handler);

		switch ($this->received_json->method) 
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
													$this->parameters->db_to, 
													$this->parameters->tbl_to, 
													$this->parameters->copy_data
												)->to_JSON();
				}
				
			break;

			case 'move':
                $mandatory = array('db_to', 'tbl_to');
                if ( $this->check_parameters($mandatory) ) {
                    $this->answer = $table->move($this->parameters->db_to, $this->parameters->tbl_to)->to_JSON();
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
                    $this->answer = $table->query($this->parameters->query)->to_JSON();
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

	private function check_parameters($mandatory) 
	{
		foreach ( $mandatory as $parameter )
		{
			if ( !isset($this->parameters->$parameter) )
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