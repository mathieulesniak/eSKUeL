<?php

class index extends simple_object
{

	var $properties = array(
		'key_name',
		'non_unique',
		'seq_in_index',
		'column_name',
		'collation',
		'cardinality',
		'sub_part',
		'packed',
		'null',
		'index_type',
		'comment'
	);

	var $private_properties = array(
		'_sql_handler'
	);

	function __construct()
	{
	}

	static function load_from_array($data, $sql_handler)
	{
		$index = new index($sql_handler);

		foreach ( $data as $key=>$val )
		{
			$key = strtolower($key);
			if ( $index->has_public_property($key) )
			{
				$index->$key = $val;
			}
		}

		return $index;
	}

}

?>