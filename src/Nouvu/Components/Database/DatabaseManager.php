<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Components\Database;

use Nouvu\Web\Foundation\Application AS App;

final class DatabaseManager
{
	private DatabaseToolsInterface $DatabaseToolsInterface;
	
	public function __construct ( private App $app )
	{
		$this -> DatabaseToolsInterface = $app -> make( $app -> repository -> get( 'database.class' ), [ $app ] );
	}
	
	public function prepare( string | array $sql, array | null $data = null ): DatabaseRequestInterface
	{
		$this -> DatabaseToolsInterface -> prepare( $sql, $data );
		
		return $this -> app -> make( Components :: class, [ $this -> DatabaseToolsInterface ] );
	}
	
	public function query( string | array $sql ): DatabaseRequestInterface
	{
		$this -> DatabaseToolsInterface -> query( $sql );
		
		return $this -> app -> make( Components :: class, [ $this -> DatabaseToolsInterface ] );
	}
}
