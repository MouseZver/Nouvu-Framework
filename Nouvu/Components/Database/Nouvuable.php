<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Database;

interface Nouvuable
{
	public function prepare( string | array $sql, array $data ): void;
	
	public function query( string | array $sql ): void;
	
	public function execute( array $data ): void;
	
	public function count(): int;
	
	public function fetch( int $code, callable | string $argument ): mixed;
	
	public function fetchAll( int $code, callable | string $argument ): array;
}
