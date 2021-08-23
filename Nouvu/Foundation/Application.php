<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation;

use Nouvu\Container;

class Application
{
	use Table\App;
	
	public function __construct ( private Container $container, array $tools )
	{
		foreach ( $tools + [ \App :: class => ( fn( Container $container ): self => $this ) ] AS $name => $packaging )
		{
			$this -> setContainer( $name, $packaging );
		}
	}
	
	protected function setContainer( string $name, callable $packaging ): void
	{
		$this -> container -> set( ucfirst ( strtolower ( $name ) ), $packaging );
	}
	
	protected function getContainer( string $name ): mixed
	{
		return $this -> container -> get( str_replace ( '.', '\\', $name ) );
	}
	
	public function __set( string $name, callable $value ): void
	{
		$this -> setContainer( ucfirst ( strtolower ( $name ) ), $value );
	}
	
	public function __get( string $name ): mixed
	{
		return $this -> getContainer( ucfirst ( strtolower ( $name ) ) );
	}
	
	public function init(): void
	{
		foreach ( $this -> config -> get( 'app.ini_set' ) AS $option => $value )
		{
			//printf ( '%s -> %s' . PHP_EOL, $option, ( string ) $value( $this ) );
			
			ini_set ( $option, ( string ) $value( $this ) );
		}
	}
}
