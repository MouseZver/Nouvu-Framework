<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Database;

interface DatabaseRequestInterface
{
	public const
		FETCH_NUM			= 1,
		FETCH_ASSOC			= 2,
		FETCH_OBJ			= 4,
		MYSQL_FETCH_BIND	= 663,
		FETCH_COLUMN		= 265,
		FETCH_KEY_PAIR		= 307,
		FETCH_NAMED			= 173,
		FETCH_UNIQUE		= 333,
		FETCH_GROUP			= 428,
		FETCH_FUNC			= 586,
		MYSQL_FETCH_FIELD	= 343;
	
	public function execute( array $data ): void;
	
	public function count(): int;
	
	public function get( int $code, callable | string $argument ): mixed;
	
	public function getAll( int $code, callable | string $argument ): array;
}
