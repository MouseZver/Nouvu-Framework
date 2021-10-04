<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation\Table;

use Psr\Container\ContainerInterface;

trait App
{
	public function getLocale(): string
	{
		return $this -> config -> get( 'config.locale' );
	}
	
	public function getCharset(): string
	{
		return $this -> config -> get( 'config.default_charset' );
	}
	
	public function make( string $class, array $params = [] ): mixed
	{
		/* return $this -> getContainer( \Container :: class ) 
			-> make( $class, fn( ContainerInterface $ContainerInterface ): mixed => new $class( ...$params ) ); */
		
		return $this -> container -> make( $class, fn( ContainerInterface $ContainerInterface ): mixed => new $class( ...$params ) );
	}
	
	public function path( string $name ): string | null
	{
		return $this -> config -> get( 'app.system.directory.' . $name );
	}
}