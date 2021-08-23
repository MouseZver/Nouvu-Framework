<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation\Table;

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
		if ( is_null ( $this -> {$class} ) )
		{
			$this -> {$class} = fn() => new $class( ...$params );
		}
		
		return $this -> {$class};
	}
	
	public function path( string $name ): string | null
	{
		return $this -> config -> get( 'app.system.directory.' . $name );
	}
}