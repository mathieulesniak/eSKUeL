<?php

class URLBuilder
{

	private $components = array();
	private $query = array();

	public function __construct( $url = false )
	{
		$this->setURL( $url );
	}

	public function setURL( $url )
	{
		if ( $parse = parse_url($url) ) {
			$this->components = array_merge($this->components, $parse);
		}

		if( !array_key_exists('query', $this->components) )
		{
			$this->components['query'] = '';
		}

		$this->setQuery( $this->components['query'] );
		return $this;
	}

	public function setQuery($value)
	{
		parse_str( $value, $query );
		$this->query = array_merge($this->query, $query);
		return $this;
	}

	public function getDomain()
	{
		if( $domain = $this->parseDomain() )
		{
			return $domain['domain'] . '.' . $domain['tld'];
		}
		return false;
	}

	public function addParam( $name, $value = false )
	{
		return $this->setParam( $name, $value );
	}

	public function setParam( $name, $value = false )
	{
		$this->query[ trim( $name ) ] = $value;
		return $this;
	}

	public function removeParam( $name )
	{
		unset( $this->query[ trim( $name ) ] );
		return $this;
	}

	public function removeParams()
	{
		$args = func_get_args();
		foreach ($args as $name) {
			$this->removeParam( $name );
		}
		return $this;
	}

	public function removeAllParams()
	{
		$this->components['query'] = '';
		$this->query = array();
		return $this;
	}

	public function getParam( $name )
	{
		if( array_key_exists( trim( $name ), $this->query ) )
		{
			return $this->query[ trim( $name ) ];
		}
		return false;
	}

	public function setHash( $name )
	{
		$this->components['fragment'] = $name;
		return $this;
	}

	public function getHash()
	{
		return $this->getComponent('fragment');
	}

	public function getComponent($name)
	{
		if( array_key_exists($name, $this->components) && $this->components[$name] != '' )
		{
			return $this->components[$name];
		}
		return false;
	}

	public function parseDomain()
	{
		if (preg_match('/^((?P<sub>.*)\.)?(?P<domain>[a-z0-9][a-z0-9\-]{1,63}){1}\.(?P<tld>([a-z.]{2,6}){1})$/iU', $this->getComponent('host'), $regs)) {
		    return $regs;
		}
	}

	public function getSubDomain()
	{
		if( $domain = $this->parseDomain() )
		{
			return $domain['sub'];
		}
		return false;
	}

	public function removeHash()
	{
		unset( $this->components['fragment'] );
		return $this;
	}

	public function build()
	{
		$output = '';
		if( array_key_exists('scheme', $this->components) && !preg_match('/:\/\/$/', $this->components['scheme']) )
		{
			$this->components['scheme'] .= '://';
		}

		foreach ($this->components as $key => $value)
		{
			switch( $key )
			{
				case 'query':
					if( count( $this->query ) )
					{
						$output .= '?';
						$value = http_build_query( $this->query );
					}
					else
					{
						$value = '';
					}
					break;
				case 'fragment':
					$output .= '#';
					break;
			}
			$output .= $value;
		}

		return $output;
	}

	public function __toString()
	{
		return $this->build();
	}

}

?>