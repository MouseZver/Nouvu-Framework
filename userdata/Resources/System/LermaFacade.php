<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\System;

use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Web\Component\Database\DatabaseToolsInterface;
use Nouvu\Database\Lerma;
use Nouvu\Database\LermaStatement;

final class LermaFacade implements DatabaseToolsInterface
{
	private Lerma $connect;
	
	private LermaStatement $statement;
	
	public function __construct ( private App $app )
	{
		$this -> connect = new Lerma( sprintf ( ...array_values ( $app -> repository -> get( 'config.database' ) ) ) );
	}
	
	public function prepare( ...$vars ): void
	{
		$this -> statement = $this -> connect -> prepare( ...$vars );
	}
	
	public function query( ...$vars ): void
	{
		$this -> statement = $this -> connect -> query( ...$vars );
	}
	
	public function execute( array $data ): void
	{
		$this -> connect -> execute( $data );
	}
	
	public function count(): int
	{
		$this -> statement -> rowCount();
	}
	
	public function get( int $code, callable | string $argument = null ): mixed
	{
		return $this -> statement -> fetch( 
			$this -> app -> repository 
				-> get( 'database.code' )( $code ), 
			$argument
		);
	}
	
	public function getAll( int $code, callable | string $argument = null ): array
	{
		return $this -> statement -> fetchAll( 
			$this -> app -> repository 
				-> get( 'database.code' )( $code ), 
			$argument
		);
	}
	
	public function id(): int
	{
		return $this -> connect -> InsertID();
	}
}