<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Config;

use Nouvu\Config\Config;

class Repository
{
	private Config $config;
	
	public function __construct ( array $data = [] )
	{
		$this -> config = new Config( $data, '.' );
	}
	
	public function set( array $data ): void
	{
		$this -> config -> set( callable: fn( &$a ) => $a = $data );
	}
	
	public function get( string $offset, mixed $default = null ): mixed
	{
		return $this -> config -> get( $offset, $default );
	}
	
	public function has( string $offset ): bool
	{
		return $this -> config -> has( $offset );
	}
	
	public function add( string $offset, array $data, bool $before = false ): void
	{
		$this -> config -> set( $offset, function ( &$a ) use ( $before, $data ): void
		{
			$a ??= [];
			
			$a = ( $before ? array_merge ( $data, $a ) : array_merge ( $a, $data ) );
		} );
	}
	
	public function delete( string $offset ): void
	{
		$this -> config -> set( $offset, fn( &$a ) => $a = null );
	}
	
	public function reset( string $offset, mixed $data ): void
	{
		$this -> config -> set( $offset, fn( &$a ) => $a = $data );
	}
	
	public function all(): array
	{
		return $this -> config -> get();
	}
}
