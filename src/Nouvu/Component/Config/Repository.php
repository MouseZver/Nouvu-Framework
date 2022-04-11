<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Config;

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
		$this -> config -> set( $offset, $data );
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
		$this -> config -> add( $offset, $data, $before );
	}
	
	public function remove( string $offset ): void
	{
		$reverse = array_reverse ( explode ( '.', $offset ) );
		
		$remove = array_shift ( $reverse );
		
		$newOffset = implode ( '.', array_reverse ( $reverse ) );
		
		$get = $this -> config -> get( $newOffset, [] );
		
		unset ( $get[$remove] );
		
		$this -> config -> set( $newOffset, $get );
	}
	
	public function all(): array
	{
		return $this -> config -> get( null );
	}
}
