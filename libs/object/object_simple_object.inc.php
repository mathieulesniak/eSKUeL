<?php

class SimpleObject
{

	protected $properties	= array();
	protected $values		= array();

	protected $private_properties	= array();
	protected $private_values		= array();
    
    protected $output_fields = array();
    
	
	function __construct()
	{
	}

	function __get($name)
	{
		if ( $this->hasPublicProperty($name) )
		{
			return isset($this->values[$name]) ? $this->values[$name] : NULL;
		}
		else if ( $this->hasPrivateProperty($name) )
		{
			return isset($this->private_values[$name]) ? $this->private_values[$name] : NULL;
		}
		else
		{
			throw new ObjectException( ObjectException::UNKNOWN_PROPERTY, $name );
		}
	}

	function __set($name, $value)
	{
		if ( $this->hasPublicProperty($name) )
		{
			$this->values[$name] = $value;
		}
		else if ( $this->hasPrivateProperty($name) )
		{
			$this->private_values[$name] = $value;
		}
		else
		{
			throw new ObjectException( ObjectException::UNKNOWN_PROPERTY, $name );
		}
	}

	function __isset($name)
	{
	}

	function hasPublicProperty($name)
	{
		return $this->hasProperty($name, 'public');
	}

	function hasPrivateProperty($name)
	{
		return $this->hasProperty($name, 'private');
	}

	private function hasProperty($name, $scope)
	{
		switch ($scope)
		{
			case 'public':
				$search_array = $this->properties;
			break;

			case 'private':
				$search_array = $this->private_properties;
			break;

			default:
				throw new ObjectException( ObjectException::WRONG_SCOPE );
			break;
		}

		return in_array($name, $search_array);
	}

	public function export()
    {
        if ( count($this->output_fields) )
        {
            $object = new stdClass();
            foreach ( $this->output_fields as $key=>$value ) 
            {
                if ( method_exists($this->$value, 'export') ) 
                {
                    $object->$key = $this->$value->export();    
                }
                else 
                {
                    $object->$key = $this->$value;
                }
                
            }
            
            return $object;
        }
        else {
            return NULL;
        }
    }

}

?>