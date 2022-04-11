<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Http;

use Symfony\Component\Security\Core\User\{ UserInterface, UserProviderInterface };
use Symfony\Component\Security\Core\Authentication\Token\{ UsernamePasswordToken, RememberMeToken, TokenInterface, Storage\TokenStorageInterface };
use Symfony\Component\Security\Http\RememberMe\{ RememberMeHandlerInterface };
use Symfony\Component\HttpFoundation\{ Request };
use Nouvu\Framework\Component\Database\DatabaseManager;

class AuthenticationHandler
{
	public function __construct ( 
		private UserProviderInterface $userProvider,
		private RememberMeHandlerInterface $rememberMeHandler,
		private TokenStorageInterface $storage,
		private SessionRememberMeHandler $sessionRememberMeHandler,
		private DatabaseManager $databaseManager,
		private Request $request,
		private string $secret,
	)
	{}
	
	public function createSession( UserInterface $user, string $providerKey, bool $remember = false )
	{
		$this -> sessionRememberMeHandler -> save( $this -> storage, $this -> getToken( $user, $providerKey, $remember ) );
	}
	
	private function getToken( UserInterface $user, string $providerKey, bool $remember ): TokenInterface
	{
		if ( $remember )
		{
			$this -> userProvider -> createUser( $user );
			
			$this -> rememberMeHandler -> createRememberMeCookie( $user );
			
			$token = new RememberMeToken( $user, $providerKey, $this -> secret );
			
			$this -> databaseManager -> insert( \RememberMeToken :: class )( $token, $this -> sessionRememberMeHandler -> getCookie() -> getValue() );
			
			return $token;
		}
		
		return new UsernamePasswordToken( $user, $providerKey, $user -> getRoles() );
	}
}








