<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Database;

use Nouvu\Web\Foundation\Application AS App

class Components implements DatabaseRequestInterface
{
	public function __construct ( private DatabaseToolsInterface $DatabaseToolsInterface )
	{
		
	}
	
	public function execute( array $data ): void
	{
		$this -> DatabaseToolsInterface -> execute( $data );
	}
	
	public function count(): int
	{
		return $this -> DatabaseToolsInterface -> count();
	}
	
	public function get( int $code = DatabaseRequestInterface :: FETCH_OBJ, callable | string $argument = null ): mixed
	{
		return $this -> DatabaseToolsInterface -> get( $code, $argument );
	}
	
	public function getAll( int $code = DatabaseRequestInterface :: FETCH_OBJ, callable | string $argument = null ): array
	{
		return $this -> DatabaseToolsInterface -> getAll( $code, $argument );
	}
	
	public function id(): int
	{
		return $this -> DatabaseToolsInterface -> id();
	}
}
