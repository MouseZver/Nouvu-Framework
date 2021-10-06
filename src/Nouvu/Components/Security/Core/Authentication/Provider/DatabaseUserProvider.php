<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Components\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Resources\Entity\User;

class DatabaseUserProvider implements UserProviderInterface
{
	public function __construct ( private App $app )
	{
		
	}
	
	public function loadUserByUsername( string $username )
	{
		return $this -> getUser( $username );
	}
	
	private function getUser( string $username ): User
	{
		$DatabaseRequestInterface = $this -> app -> repository -> get( 'query.database.select.users_username|email' )( name: $username );
		
		if ( $DatabaseRequestInterface -> count() )
		{
			$user = new User;
			
			$this -> app -> repository -> get( 'auth.entity.user' ) 
				-> call( $user, ...$DatabaseRequestInterface -> get( $DatabaseRequestInterface :: FETCH_ASSOC ) );
			
			return $user;
		}
		
		$exception = new UserNotFoundException( sprintf ( 'Username \'%s\' not found in the database.', $username ) );
		
		$exception -> setUsername( $username );
		
		throw $exception;
	}
	
	public function refreshUser( UserInterface $user ): User
	{
		if ( ! $user instanceof User )
		{
			throw new UnsupportedUserException( sprintf ( 'Instances of \'%s\' are not supported.', $user :: class ) );
		}
		
		return $this -> getUser( $user -> getUsername() );
	}
	
	public function supportsClass( string $class )
	{
		return User :: class === $class;
	}
}