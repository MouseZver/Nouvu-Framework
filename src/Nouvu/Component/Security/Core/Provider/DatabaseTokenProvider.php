<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\Provider;

use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Nouvu\Framework\Component\Database\DatabaseManager;

class DatabaseTokenProvider
{
	public function __construct ( private DatabaseManager $databaseManager )
	{
		$this -> databaseManager -> delete( \RememberMeToken\Expired :: class )();
	}
	
	public function loadTokenByIdentifier( string $identifier ): TokenInterface
	{
		return $this -> getToken( $identifier );
	}
	
	private function getToken( string $identifier ): TokenInterface
	{
		$statement = $this -> databaseManager -> select( \RememberMeToken\By\Series :: class )( $identifier );;
		
		if ( empty ( $statement -> count() ) )
		{
			throw new TokenNotFoundException( 'Token not found in the repository' );
		}
		
		[ 'token' => $token ] = $statement -> get( $statement :: FETCH_ASSOC );
		
		return unserialize ( $token );
	}
}