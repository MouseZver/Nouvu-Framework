<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Database;

use Nouvu\Framework\Foundation\Application AS App;

class Statement implements StatementInterface, \IteratorAggregate
{
	public function __construct ( private DatabaseInterface $DatabaseInterface )
	{}
	
	public function execute( array $data ): void
	{
		$this -> DatabaseInterface -> execute( $data );
	}
	
	public function count(): int
	{
		return $this -> DatabaseInterface -> count();
	}
	
	public function get( int $code = StatementInterface :: FETCH_OBJ, callable | string $argument = null ): mixed
	{
		return $this -> DatabaseInterface -> get( $code, $argument );
	}
	
	public function all( int $code = StatementInterface :: FETCH_OBJ, callable | string $argument = null ): array
	{
		return $this -> DatabaseInterface -> all( $code, $argument );
	}
	
	public function id(): int
	{
		return $this -> DatabaseInterface -> id();
	}
	
	public function getIterator(): \Traversable
	{
		return $this -> DatabaseInterface;
	}
}
