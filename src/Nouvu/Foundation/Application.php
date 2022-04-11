<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Foundation;

use Psr\Container\ContainerInterface;

class Application
{
	//use ApplicationTrait;
	
	public function __construct ( private ContainerInterface $ContainerInterface, array $tools )
	{
		foreach ( $tools + [ \App :: class => ( fn( ContainerInterface $ContainerInterface ): self => $this ) ] AS $name => $packaging )
		{
			//$this -> setContainer( $name, $packaging );
			$this -> ContainerInterface -> set( $name, $packaging );
		}
	}
	
	/*protected function setContainer( string $name, callable $packaging ): void
	{
		$this -> ContainerInterface -> set( $name, $packaging );
	}*/
	
	/*protected function getContainer( string $name ): mixed
	{
		return $this -> ContainerInterface -> get( str_replace ( '.', '\\', $name ) );
	}*/
	
	public function __set( string $name, callable $value ): void
	{
		//$this -> setContainer( $name, $value );
		$this -> ContainerInterface -> set( $name, $packaging );
	}
	
	public function __get( string $name ): mixed
	{
		//return $this -> getContainer( $name );
		return $this -> ContainerInterface -> get( str_replace ( '.', '\\', $name ) );
	}
	
	public function init(): void
	{
		foreach ( $this -> repository ?-> get( 'app.ini_set', [] ) ?? [] AS $option => $value )
		{
			ini_set ( $option, ( string ) $value( $this ) );
		}
	}
}
