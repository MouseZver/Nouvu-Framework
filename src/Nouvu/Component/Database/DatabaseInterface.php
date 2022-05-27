<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Database;

interface DatabaseInterface
{
	public function prepare( string | array $sql, array $data ): void;
	
	public function query( string | array $sql ): void;
	
	public function execute( array $data ): void;
	
	public function count(): int;
	
	public function get( int $code, callable | string $argument ): mixed;
	
	public function all( int $code, callable | string $argument ): iterable;
}
