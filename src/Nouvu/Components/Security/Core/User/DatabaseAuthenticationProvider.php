<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Components\Security\Core\User;
 
use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

use Symfony\Component\Security\Core\Exception\UserNotFoundException; // ?

use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Nouvu\Resources\Entity\User;

// Deprecated
// use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider

/* class DatabaseAuthenticationProvider extends UserAuthenticationProvider
{
	public function __construct ( 
		private UserProviderInterface $userProvider, 
		UserCheckerInterface $userChecker, 
		string $providerKey, 
		bool $hideUserNotFoundExceptions = true 
	)
	{
		parent :: __construct ( $userChecker, $providerKey, $hideUserNotFoundExceptions );
	}
	
	protected function retrieveUser( $username, UsernamePasswordToken $token ): User
	{
		$user = $token -> getUser();
		
		if ( $user instanceof UserInterface )
		{
			return $user;
		}
		
		try
		{
			$user = $this -> userProvider -> loadUserByUsername( $username );
			
			if ( $user instanceof UserInterface )
			{
				return $user;
			}
			
			throw new AuthenticationServiceException( 'The user provider must return a UserInterface object.' );
		}
		catch ( UserNotFoundException $e )
		{
			$e -> setUsername( $username );
			
			throw $e;
		}
		catch ( \Exception $e )
		{
			$e = new AuthenticationServiceException( $e -> getMessage(), 0, $e );
			
			$e -> setToken( $token );
			
			throw $e;
		}
	}

	protected function checkAuthentication( UserInterface $user, UsernamePasswordToken $token ): void
	{
		$currentUser = $token -> getUser();
		
		if ( $currentUser instanceof UserInterface )
		{
			if ( $currentUser -> getPassword() !== $user -> getPassword() )
			{
				throw new AuthenticationException( 'Credentials were changed from another session.' );
			}
		}
		else
		{
			$password = $token -> getCredentials();
			
			if ( empty ( $password ) )
			{
				throw new AuthenticationException( 'Password can not be empty.' );
			}
			??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
			if ( $user -> getPassword() != md5 ( $password ) )
			{
				throw new AuthenticationException( 'Password is invalid.' );
			}
		}
	}
} */
