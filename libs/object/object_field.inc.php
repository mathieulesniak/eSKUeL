<?php

class field extends simple_object
{

	var $properties = array(
		'name',
		'raw_type',
		'type',
		'length',
		'can_be_null',
		'attributes',
		'default_value',
		'auto_increment',
		'key',
		'table'
	);

	function __construct($table)
	{
		$this->table = $table;
	}

	static function load_from_array($data, $sql_handler)
	{
		$field = new field($sql_handler);

		$constructed_data = array();
		// Iterate through data, and reassign to good keys
		foreach ( $data as $key=>&$val )
		{
			$key = strtolower($key);
			switch ($key) {
				case 'field':
					$key = 'name';
				break;

				case 'type':
					preg_match('|(.*)\((.*)\)|', $val, $matches);
					if ( isset($matches[2]) && $matches[2] != '' )
					{
						$constructed_data['type'] 	= $matches[1];
						$constructed_data['length'] = $matches[2];
					}
					else
					{
						$constructed_data['type'] = $val;
					}
					$key = 'raw_type';
				break;

				case 'null':
					$key = 'can_be_null';
					if ( $val == 'NO' )
					{
						$val = false;
					}
					else if ( $val == 'YES' )
					{
						$val = true;
					}
				break;

				case 'default':
					$key = 'default_value';
				break;

				case 'extra':
					if ( $val == 'auto_increment' )
					{
						$constructed_data['auto_increment'] = true;
					}
					$key = '';
				break;

			}
			if ( $key != '' )
			{
				$constructed_data[$key] = $val;
			}
		}

		// Iterate through newly built data
		foreach ( $constructed_data as $key=>$val)
		{
			if ( $field->has_public_property($key) )
			{
				$field->$key = $val;
			}
		}

		return $field;
	}

	function save()
	{
	}

	function delete()
	{
	}

}

?>