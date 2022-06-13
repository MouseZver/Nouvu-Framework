<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Database;

use Nouvu\Framework\Foundation\Application AS App;

final class DatabaseManager
{
	use Traits\MethodsEnumerationTrait;
	
	private DatabaseInterface $connect;
	
	public function __construct ( private App $app )
	{
		$class = $app -> repository -> get( 'database.class' );
		
		$this -> connect = new $class( $app );
	}
	
	public function prepare( string | array $sql, array | null $data = null ): StatementInterface
	{
		$this -> connect -> prepare( $sql, $data );
		
		return new Statement( $this -> connect );
	}
	
	public function query( string | array $sql ): StatementInterface
	{
		$this -> connect -> query( $sql );
		
		return new Statement( $this -> connect );
	}
}
