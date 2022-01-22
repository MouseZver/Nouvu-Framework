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

use Symfony\Component\Security\Core\{ Security, Exception\TokenNotFoundException };
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Nouvu\Web\Foundation\Application;

class DatabaseTokenProvider
{
	public function __construct ( private Application $app )
	{
		
	}
	
	public function loadTokenByIdentifier( string $identifier ): TokenInterface
	{
		return $this -> getToken( $identifier );
	}
	
	private function getToken( string $identifier ): TokenInterface
	{
		$this -> app -> repository -> get( 'query.database.delete.rememberme_token@clearing-expired-tokens' )();
		
		$resource = $this -> app -> repository -> get( 'query.database.select.rememberme_token@by_series' )( $identifier );
		
		if ( empty ( $resource -> count() ) )
		{
			throw new TokenNotFoundException( 'Token not found in the repository' );
		}
		
		[ 'id' => $id, 'value' => $token ] = $resource -> get( $resource :: FETCH_ASSOC );
		
		$this -> app -> repository -> get( 'query.database.update.rememberme_token@update-lastUsed_by_id' )( $id );
		
		return unserialize ( $token );
	}
}