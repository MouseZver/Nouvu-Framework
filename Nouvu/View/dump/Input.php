<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View;

use Nouvu\Config\Config;

class Input
{
	private Config $container;
	
	public function __construct ( array $data = [] )
	{
		$this -> container = new Config( $data );
	}
	
	public function set( array $data ): void
	{
		$this -> container -> set( callable: fn( &$a ) => $a = $data );
	}
	
	public function get( string $name, mixed $default = null ): mixed
	{
		return $this -> container -> get( $name, $default );
	}
	
	public function add( string $name, array $data, bool $before = false ): void
	{
		$this -> container -> set( $name, function ( &$a ) use ( $before, $data ): void
		{
			is_array ( $a ) ?: $a = [ $a ];
			
			$a = ( $before ? array_merge ( $data, $a ) : array_merge ( $a, $data ) );
		} );
	}
	
	public function reset( string $name, mixed $data ): void
	{
		$this -> container -> set( $name, fn( &$a ) => $a = $data );
	}
	
	public function all(): array
	{
		return $this -> container -> get();
	}
}
