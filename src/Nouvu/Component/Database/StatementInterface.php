<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Database;

interface StatementInterface
{
	public const
		FETCH_NUM = 1,
		FETCH_ASSOC = 2,
		FETCH_OBJ = 4,
		FETCH_COLUMN = 265,
		FETCH_KEY_PAIR = 307,
		FETCH_NAMED = 173,
		FETCH_UNIQUE = 333,
		FETCH_GROUP = 428,
		FETCH_FUNC = 586,
		MYSQL_FETCH_FIELD = 343,
		MYSQL_FETCH_BIND = 663;
	
	public function execute( array $data ): void;
	
	public function count(): int;
	
	public function get( int $code, callable | string $argument ): mixed;
	
	public function all( int $code, callable | string $argument ): array;
}
