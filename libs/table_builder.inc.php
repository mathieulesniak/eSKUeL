<?php

class TableBuilder
{

    private $head = array();
    private $body = array();
    private $attributes = array();

    function __construct( $content_object = false )
    {
        if( $content_object )
        {
            foreach ($content_object->header as $id => $name)
			{
                $this->addHead( $name );
            }

            foreach ($content_object->data as $row)
			{
                $this->addRow( $row );
            }

        }
    }

    function setId( $id )
    {
        $this->setAttributes('table', 'table', array(
            'id' => $id
        ));
    }

    function setClass( $classes )
    {
        $this->setAttributes('table', 'table', array(
            'class' => $classes
        ));
    }

    private function setAttributes( $type, $id, $array = false )
    {
        if( $array )
        {
            if( !array_key_exists($type, $this->attributes) )
            {
                $this->attributes[$type] = array();
            }
            if( !array_key_exists($id, $this->attributes[$type]) )
            {
                $this->attributes[$type][$id] = array();
            }

            foreach ($array as $key => $value) {
                if ( !empty($value) ) array_push( $this->attributes[$type][$id], sprintf('%s="%s"', $key, $value) );
            }
        }
    }

    private function getAttributes( $type, $id )
    {
        if( array_key_exists($type, $this->attributes) && array_key_exists($id, $this->attributes[$type]) )
        {
            return ' ' . implode( ' ', $this->attributes[$type][$id] );
        }
    }

    function setHead( $array, $attributes = false )
    {
        if( $array )
        {
            foreach ($array as $col_name => $value) {
                $this->addHead( $col_name, $value );
            }
            $this->setAttributes( 'row', 'head', $attributes );
        }
    }

    function addHead( $col_name, $label = true )
    {
        $this->head[ trim( $col_name ) ] = $label;
    }

    function addRow( $array , $attributes = false )
    {
        if( $array )
        {
            $row_number = count($this->body);
            foreach ($array as $col_name => $content) {
                $this->setCell( $col_name, $row_number, $content);
            }
            $this->setAttributes( 'row', $row_number, $attributes );
        }
    }

    function setCell( $col_name, $row_number, $content)
    {
        if( !array_key_exists( $row_number, $this->body ) )
        {
            $this->body[$row_number] = array();
        }
        $this->body[$row_number][$col_name] = $content;
    }

    function build()
    {
        $body = '';
        foreach ($this->body as $row_number => $row) {
            $body .= '     <tr';
            $body .= $this->getAttributes( 'row', $row_number );
            $body .= '>'."\n";
            foreach ($row as $key => $cell)
			{
				if( !$cell || trim($cell) == '' )
				{
					$cell = '&nbsp;';
				}
                $body .= sprintf('        <td class="col_%s">%s</td>'."\n", $key, $cell);
            }
            $body .= '     </tr>'."\n";
        }

        if ( $this->head )
        {
			$colgroup = '<colgroup>'."\n";
			$head = '<thead>'."\n";
            $head .= '     <tr';
            $head .= $this->getAttributes( 'row', 'head' );
            $head .= '>'."\n";
            foreach ($this->head as $col_name => $label) {
                if( is_bool($label) )
                {
                    $label = ucfirst($col_name);
                }
                $head .= '        <th>'.$label.'</th>'."\n";
				$colgroup .= '<col>'."\n";
            }
            $head .= '     </tr>'."\n";
			$head .= '</thead>'."\n";
			$colgroup .= '</colgroup>'."\n";
        }


        $out = '<table';
        $out .= $this->getAttributes( 'table', 'table' );
//		$out .= ' border="1"';
        $out .= '>'."\n";
		$out .= $colgroup;
        $out .= $head;
        $out .= $body;
        $out .= '</table>'."\n";

        return $out;
    }

    function __toString()
    {
        return $this->build();
    }

}

?>