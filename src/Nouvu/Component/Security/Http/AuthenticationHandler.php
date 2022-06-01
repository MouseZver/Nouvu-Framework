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
	protected const
		CONT_MEMORY_USER_PROVIDER = 'security.memory.user_provider',
		CONT_TOKEN_STORAGE = 'security.token_storage',
		CONT_REMEMBER_HASHER = 'security.signature_remember_me_hasher';
		
	public function __construct ( 
		/* private UserProviderInterface $userProvider,
		private RememberMeHandlerInterface $rememberMeHandler,
		private TokenStorageInterface $storage,
		private SessionRememberMeHandler $sessionRememberMeHandler,
		private DatabaseManager $databaseManager,
		private Request $request, */
		private string $secret,
	)
	{}
	
	public function createSession( UserInterface $user, string $providerKey, bool $remember = false )
	{
		$this -> container -> get( \SessionRememberMeHandler :: class ) -> save( 
			$this -> container -> get( self :: CONT_TOKEN_STORAGE ), 
			$this -> getToken( $user, $providerKey, $remember ) 
		);
	}
	
	private function getToken( UserInterface $user, string $providerKey, bool $remember ): TokenInterface
	{
		if ( $remember )
		{
			$this -> container -> get( self :: CONT_MEMORY_USER_PROVIDER ) -> createUser( $user );
			
			$this -> container -> get( self :: CONT_REMEMBER_HASHER ) -> createRememberMeCookie( $user );
			
			$token = new RememberMeToken( $user, $providerKey, $this -> secret );
			
			$closure = $this -> container -> get( \Database :: class ) -> insert( \RememberMeToken :: class );
			
			$closure( $token, $this -> container -> get( \SessionRememberMeHandler :: class ) -> getCookie() -> getValue() );
			
			return $token;
		}
		
		return new UsernamePasswordToken( $user, $providerKey, $user -> getRoles() );
	}
}








