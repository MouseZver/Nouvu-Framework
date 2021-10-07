<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation\Table;

use Psr\Container\ContainerInterface;

trait App
{
	public function getLocale(): string
	{
		return $this -> repository -> get( 'config.locale' );
	}
	
	public function getCharset(): string
	{
		return $this -> repository -> get( 'config.default_charset' );
	}
	
	public function make( string $class, array $params = [] ): mixed
	{
		return $this -> container -> make( $class, $params );
	}
	
	public function path( string $name ): ?string
	{
		return $this -> repository -> get( 'app.system.directory.' . $name );
	}
}