<?php

class simple_object
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
		if ( $this->has_public_property($name) )
		{
			return isset($this->values[$name]) ? $this->values[$name] : NULL;
		}
		else if ( $this->has_private_property($name) )
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
		if ( $this->has_public_property($name) )
		{
			$this->values[$name] = $value;
		}
		else if ( $this->has_private_property($name) )
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

	function has_public_property($name)
	{
		return $this->has_property($name, 'public');
	}

	function has_private_property($name)
	{
		return $this->has_property($name, 'private');
	}

	private function has_property($name, $scope)
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

	public function to_JSON()
    {
        if ( count($this->output_fields) )
        {
            $object = new stdClass();
            foreach ( $this->output_fields as $key=>$value ) {
                $object->$key = $this->$value;
            }
            
            return $object;
        }
        else {
            return NULL;
        }
    }

}

?>