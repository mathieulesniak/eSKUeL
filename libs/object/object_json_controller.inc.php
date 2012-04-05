<?php
class JsonController extends SimpleObject {

	var $properties = array(
		'answer'
	);

	var $private_properties = array(
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
		$received_call = getPostParam('path');
		if ( $received_call !== false )
		{
			$request_path = explode('/', $received_call);
            $this->_scope    = isset($request_path[1]) ? $request_path[1] : '';
            $this->_method   = isset($request_path[2]) ? $request_path[2] : '';
            $this->_parameters = new stdClass();
            foreach ( $_POST as $key=>$val ) {
                if ( $key != 'path'
                    && !in_array($key, $this->properties)
                    && !in_array($key, $this->private_properties)
                    ) {
                    $this->_parameters->$key = $val;
                }
            }
            
            if ( $this->_method != '' && $this->_scope != '' ) 
			{
				switch ( $this->_scope ) 
				{
					case 'server':
						$this->handleServerScope();
					break;

					case 'db':
						$this->handleDbScope();
					break;

					case 'tbl':
						$this->handleTblScope();
					break;

					default:
						throw new ObjectException( ObjectException::UNKNOWN_JSON_SCOPE );
						
					break;
				}

				$this->sendAnswer();
			}
			else 
			{
				throw new ObjectException( ObjectException::MISSING_JSON_PARAMETER, 'scope');
			}
            
        }
	}
   
	private function handleServerScope() 
	{
		switch ($this->_method)
		{
			case 'processlist':
                $this->answer = $this->_sql_handler->serverProcesslist()->export();
			break;
            
            case 'create_db':
                $mandatory = array('db');
                if ( $this->checkParameters($mandatory) ) {
                    $database = new Database($this->_parameters->db, $this->_sql_handler);
                    $this->answer = $database->create()->export();
                }
			break;

			case 'get_db':
				$this->answer = $this->_sql_handler->dbList()->export();
			break;

			default:
				$this->setError(_('Unknown method'));
			break;
		}
	}

	private function handleDbScope() 
	{
		$mandatory = array('db');
		if ( $this->checkParameters($mandatory) ) {
			$database = Database::load($this->_parameters->db, $this->_sql_handler);
			
			switch ($this->_method) 
			{
				case 'delete_db':
					$this->answer = $database->delete();
				break;
            
                case 'create_tbl':
                    
                break;
            
				case 'get_tbl':
					$this->answer = $database->getTables()->export();
				break;
            
                case 'query':
	                $mandatory = array('query', 'from', 'nb_records');
					if ( $this->checkParameters($mandatory) )
                    {
	                    $this->answer = $database->query(
	                    							$this->_parameters->query,
	                    							$this->_parameters->from,
	                    							$this->_parameters->nb_records
	                    							)->export();
	                }
	            break;
            
                case 'explain':
                    $mandatory = array('query');
                    if ( $this->checkParameters($mandatory) )
                    {
                        $this->answer = $database->explain($this->_parameters->query)->export();
                    }
                break;
	            
               
				default:
					$this->setError(_('Unknown method'));
				break;
			}
		}
	}

	private function handleTblScope() 
	{
		$mandatory = array('db', 'tbl');
		if ( $this->checkParameters($mandatory) ) {
			$database 	= Database::load($this->_parameters->db, $this->_sql_handler);
			$table 		= Table::load($this->_parameters->tbl, $database, $this->_sql_handler);

			switch ($this->_method) 
			{
                case 'get_layer_fields_types':
                    $this->answer = $this->_sql_handler->layerFieldsTypes()->export();
                break;
        
                case 'get_layer_fields_functions':
                    $this->answer = $this->_sql_handler->layerFieldsFunctions()->export();
                break;
            
                case 'get_layer_specific_functions':
                    $this->answer = $this->_sql_handler->layerSpecificFunctions()->export();
                break;
                
				case 'get_fields':
					$this->answer = $table->getFields()->export();
				break;

				case 'get_indexes':
					$this->answer = $table->getIndexes()->export();
				break;
            
                case 'get_infos':
                    $this->answer = $table->getInfos()->export();
                break;

				case 'copy':
					$mandatory = array('db_to', 'tbl_to', 'copy_data');
					if ( $this->checkParameters($mandatory) ) {
						$this->answer = $table->copy(
                                                $this->_parameters->db_to, 
                                                $this->_parameters->tbl_to,
                                                $this->_parameters->copy_data
                                                )->export();
					}
				break;

				case 'move':
	                $mandatory = array('db_to', 'tbl_to');
	                if ( $this->checkParameters($mandatory) )
                    {
	                    $this->answer = $table->move($this->_parameters->db_to, $this->_parameters->tbl_to)->export();
	                }

				break;

				case 'rename':
                    $mandatory = array('tbl_to');
                    if ( $this->checkParameters($mandatory) )
                    {
                        $this->answer = $table->rename($this->_parameters->tbl_to)->export();
                    }
				break;

				case 'add_field':

				break;
            
                case 'set_comment':
                    $mandatory = array('comment');
                    if ( $this->checkParameters($mandatory) ) {
                        $this->answer = $table->setComment($this->_parameters->comment)->export();
                    }
                        
                break;

				case 'set_type':
                    $mandatory = array('type');
                    if ( $this->checkParameters($mandatory) )
                    {
                        $this->answer = $table->etType($this->_parameters->type)->export();        
                    }
				break;
            
                case 'get_type':
                        $this->answer = $table->getType()->export();        
                break;
            
                
	            
	        
				case 'empty':
	                $this->answer = $table->doEmpty()->export();
				break;

				default:
					$this->setError(_('Unknown method'));
				break;
			}
		}

	}

	private function checkParameters($mandatory) 
	{
		foreach ( $mandatory as $parameter )
		{
			if ( !isset($this->_parameters->$parameter) )
			{
				// TODO : Throw Exception ?			
				$this->setError(_('Missing parameter :') . $parameter);
				return false;
			}
		}
		
		return true;	
	}

	private function setError($message) 
	{
		$this->answer = new stdClass();
		$this->answer->data = null;
		$this->answer->message = $message;
        $this->answer->last_query = null;
		$this->answer->return_code = -1;
	}

	private function sendAnswer() 
	{
		
		Header("Content-type: application/json");
		echo json_encode($this->answer);
	}
}

?>