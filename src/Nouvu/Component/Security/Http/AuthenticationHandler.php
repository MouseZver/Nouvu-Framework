<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Http;

use Nouvu\Framework\Component\Database\DatabaseManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\{ Request };
use Symfony\Component\Security\Core\Authentication\Token\{ UsernamePasswordToken, RememberMeToken, TokenInterface, Storage\TokenStorageInterface };
use Symfony\Component\Security\Core\User\{ UserInterface, UserProviderInterface };
use Symfony\Component\Security\Http\RememberMe\{ RememberMeHandlerInterface };

class AuthenticationHandler
{
	protected const
		CONT_DATABASE_TOKEN_PROVIDER = 'security.database.token_provider',
		CONT_MEMORY_USER_PROVIDER = 'security.memory.user_provider',
		CONT_TOKEN_STORAGE = 'security.token_storage',
		CONT_REMEMBER_HASHER = 'security.signature_remember_me_hasher';
		
	public function __construct ( 
		private ContainerInterface $container,
		private string $secret,
	)
	{}
	
	public function createSession( UserInterface $user, string $providerKey, bool $remember = false, ?TokenStorageInterface $storage = null )
	{
		$this -> container -> get( \SessionRememberMeHandler :: class ) -> save( 
			$storage ?? $this -> container -> get( self :: CONT_TOKEN_STORAGE ), 
			$this -> createToken( $user, $providerKey, $remember ) 
		);
	}
	
	public function createToken( UserInterface $user, string $providerKey, bool $remember ): TokenInterface
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
	
	public function login( TokenStorageInterface $storage, string $series ): void
	{
		//$token = $this -> tokenProvider -> loadTokenByIdentifier( $series );
		$token = $this -> container -> get( self :: CONT_DATABASE_TOKEN_PROVIDER ) -> loadTokenByIdentifier( $series );
		
		//$this -> rememberMeHandler -> createRememberMeCookie( $token -> getUser() );
		$this -> container -> get( self :: CONT_REMEMBER_HASHER ) -> createRememberMeCookie( $token -> getUser() );
		
		$this -> container -> get( \SessionRememberMeHandler :: class ) -> save( $storage, $token );
	}
}








