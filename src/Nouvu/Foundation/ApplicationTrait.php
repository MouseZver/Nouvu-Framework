<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation;

use Psr\Container\ContainerInterface;

trait ApplicationTrait
{
	public function getLocale(): string
	{
		return $this -> app -> repository -> get( 'config.locale' );
	}
	
	public function getCharset(): string
	{
		return $this -> app -> repository -> get( 'config.default_charset' );
	}
	
	public function make( string $class, array $params = [] ): mixed
	{
		return $this -> app -> container -> make( $class, $params );
	}
	
	public function path( string $name ): ?string
	{
		return $this -> app -> repository -> get( 'app.system.directory.' . $name );
	}
}