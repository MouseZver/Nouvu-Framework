<?php

declare ( strict_types = 1 );

/* 
CREATE TABLE `rememberme_token` (
    `series`   char(88)     UNIQUE PRIMARY KEY NOT NULL,
    `value`    varchar(88)  NOT NULL,
    `lastUsed` datetime     NOT NULL,
    `class`    varchar(100) NOT NULL,
    `username` varchar(200) NOT NULL
);
 */

namespace Nouvu\Web\Component\Security\Core\User;

use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Nouvu\Web\Foundation\Application;

class DatabaseTokenProvider
{
	public function __construct ( private Application $app )
	{
		
	}
	
	public function loadTokenByIdentifier( string | null $identifier ): void
	{
		if ( is_null ( $identifier ) )
		{
			return;
		}
		
		$this -> getToken( $identifier );
	}
	
	private function getToken( string $identifier ): void
	{
		$this -> app -> repository -> get( 'query.database.delete.clearing_expired_tokens' )();
		
		$resource = $this -> app -> repository -> get( 'query.database.select.token' )( $identifier );
		
		if ( $resource -> count() )
		{
			[ $token, $username ] = $resource -> get();
			
			$token = unserialize ( $token );
			
			$this -> app -> container -> get( 'security.token_storage' ) -> setToken( $token );
			
			$this -> app -> session -> set( Security :: LAST_USERNAME, $username );
		}
		else
		{
			throw new TokenNotFoundException;
		}
	}
}