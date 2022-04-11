<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\Provider;

use Symfony\Component\Security\Core\User\{ UserProviderInterface, UserInterface };
use Symfony\Component\Security\Core\Exception\{ UserNotFoundException, UnsupportedUserException };
use Nouvu\Resources\Entity\User;
use Nouvu\Framework\Component\Database\DatabaseManager;

class DatabaseUserProvider implements UserProviderInterface
{
	public function __construct ( 
		private DatabaseManager $databaseManager,
		private \Closure $entityUser,
	)
	{}
	
	public function loadUserByIdentifier( string $string ): UserInterface
	{
		return $this -> getUser( $string );
	}
	
	private function getUser( string $identifier ): UserInterface
	{
		$statement = $this -> databaseManager -> select( \Users\By\Username\Or\Email :: class )( $identifier, $identifier );
		
		if ( $statement -> count() )
		{
			$user = new User;
			
			$this -> entityUser -> call( $user, $statement -> get( $statement :: FETCH_OBJ ) );
			
			return $user;
		}
		
		$exception = new UserNotFoundException( sprintf ( 'User \'%s\' not found in the database.', $identifier ) );
		
		$exception -> setUserIdentifier( $identifier );
		
		throw $exception;
	}
	
	public function refreshUser( UserInterface $user ): UserInterface
	{
		if ( $user instanceof User )
		{
			return $this -> getUser( $user -> getUsername() );
		}
		
		throw new UnsupportedUserException( sprintf ( 'Instances of \'%s\' are not supported.', $user :: class ) );
	}
	
	public function supportsClass( $class )//: bool
	{
		return $class instanceof User;
	}
}