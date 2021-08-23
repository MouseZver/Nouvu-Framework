<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Database;

class Components implements Request
{
	public function __construct ( 
		private \Nouvu\Web\Foundation\Application $app, 
		private Nouvuable $interface 
	) {}
	
	public function execute( array $data ): void
	{
		$this -> interface -> execute( $data );
	}
	
	public function count(): int
	{
		return $this -> interface -> count();
	}
	
	public function fetch( int $code = Request :: FETCH_OBJ, callable | string $argument = null ): mixed
	{
		return $this -> interface -> fetch( $code, $argument );
	}
	
	public function fetchAll( int $code = Request :: FETCH_OBJ, callable | string $argument = null ): array
	{
		return $this -> interface -> fetchAll( $code, $argument );
	}
}
