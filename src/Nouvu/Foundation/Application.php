<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation;

use Psr\Container\ContainerInterface;

class Application
{
	use Table\App;
	
	public function __construct ( private ContainerInterface $ContainerInterface, array $tools )
	{
		foreach ( $tools + [ \App :: class => ( fn( ContainerInterface $ContainerInterface ): self => $this ) ] AS $name => $packaging )
		{
			$this -> setContainer( $name, $packaging );
		}
	}
	
	protected function setContainer( string $name, callable $packaging ): void
	{
		$this -> ContainerInterface -> set( ucfirst ( strtolower ( $name ) ), $packaging );
	}
	
	protected function getContainer( string $name ): mixed
	{
		return $this -> ContainerInterface -> get( str_replace ( '.', '\\', $name ) );
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
		foreach ( $this -> repository -> get( 'app.ini_set' ) AS $option => $value )
		{
			ini_set ( $option, ( string ) $value( $this ) );
		}
	}
}
