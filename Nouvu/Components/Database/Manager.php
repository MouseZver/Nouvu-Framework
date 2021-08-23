<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Database;

use Nouvu\Web\Foundation\Application AS App;
//use Nouvu\Web\Foundation\Exception AS InvalidDatabaseConnect;

final class Manager
{
	private Nouvuable $interface;
	
	public function __construct ( private App $app )
	{
		/* $facade = $app -> config -> get( 'database.interChangeAbility' );
		
		$facade ?? throw new InvalidDatabaseConnect( code: 221422 );
		 */
		$this -> interface = $app -> make( $app -> config -> get( 'database.class' ), [ $app ] );
	}
	
	public function prepare( string | array $sql, array | null $data = null ): Request
	{
		$this -> interface -> prepare( $sql, $data );
		
		return $this -> app -> make( Components :: class, [ $this -> app, $this -> interface ] );
	}
	
	public function query( string | array $sql ): Request
	{
		$this -> interface -> query( $sql );
		
		return $this -> app -> make( Components :: class, [ $this -> app, $this -> interface ] );
	}
}
