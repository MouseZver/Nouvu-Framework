<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Security\Core\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Resources\Entity\User;

class DatabaseUserProvider// implements UserProviderInterface
{
	public function __construct ( private App $app )
	{
		
	}
	
	public function loadUserByUsername( string $string ): User
	{
		return $this -> getUser( $string );
	}
	
	public function loadUserByIdentifier( string $string ): User
	{
		return $this -> getUser( $string );
	}
	
	private function getUser( string $identifier ): User
	{
		$DatabaseRequestInterface = $this -> app -> repository 
			-> get( 'query.database.select.users_username|email' )( $identifier, $identifier );
		
		if ( $DatabaseRequestInterface -> count() )
		{
			$user = new User;
			
			$this -> app -> repository -> get( 'auth.entity.user' ) 
				-> call( $user, ...$DatabaseRequestInterface -> get( $DatabaseRequestInterface :: FETCH_ASSOC ) );
			
			return $user;
		}
		
		$exception = new UsernameNotFoundException( sprintf ( 'User \'%s\' not found in the database.', $identifier ) );
		
		$exception -> setUsername( $identifier );
		
		throw $exception;
	}
	
	public function refreshUser( UserInterface $user ): User
	{
		if ( $user instanceof User )
		{
			return $this -> getUser( $user -> getUsername() );
		}
		
		throw new UnsupportedUserException( sprintf ( 'Instances of \'%s\' are not supported.', $user :: class ) );
	}
	
	public function supportsClass( string $class ): bool
	{
		return User :: class === $class;
	}
}