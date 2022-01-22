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
	
	public function set( string | int | null $offset, mixed $data ): void
	{
		$this -> config -> set( $offset, fn( &$a ) => $a = $data );
	}
	
	public function get( string | int $offset, mixed $default = null ): mixed
	{
		return $this -> config -> get( $offset, $default );
	}
	
	public function has( string | int $offset ): bool
	{
		return $this -> config -> has( $offset );
	}
	
	public function add( string | int | null $offset, array $data, bool $before = false ): void
	{
		$this -> config -> set( $offset, function ( &$a ) use ( $before, $data ): void
		{
			$a ??= [];
			
			$a = ( $before ? array_merge ( $data, $a ) : array_merge ( $a, $data ) );
		} );
	}
	
	public function remove( string $offset ): void
	{
		$reverse = array_reverse ( explode ( '.', $offset ) );
		
		$remove = array_shift ( $reverse );
		
		$this -> config -> set( implode ( '.', array_reverse ( $reverse ) ), function ( &$a ) use ( $remove ): void
		{
		    unset ( $a[$remove] );
		} );
	}
	
	/* public function reset( string $offset, mixed $data ): void
	{
		$this -> config -> set( $offset, fn( &$a ) => $a = $data );
	} */
	
	public function all(): array
	{
		return $this -> config -> get( null );
	}
}
