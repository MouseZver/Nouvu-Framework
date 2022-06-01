<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Http;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\{ Request, Response, Cookie, Session\SessionInterface };
use Symfony\Component\Security\Core\Authentication\Token\{ Storage\TokenStorageInterface, TokenInterface };
use Symfony\Component\Security\Http\RememberMe\{ RememberMeHandlerInterface, ResponseListener };
use Nouvu\Framework\Component\Database\DatabaseManager;
use Nouvu\Framework\Component\Security\Core\Provider\DatabaseTokenProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class SessionRememberMeHandler
{
	protected const
		CONT_DATABASE_TOKEN_PROVIDER = 'security.database.token_provider',
		CONT_DATABASE_USER_PROVIDER = 'security.database.user_provider',
		CONT_REMEMBER_HASHER = 'security.signature_remember_me_hasher';
	
	public function __construct ( 
		/* private DatabaseTokenProvider $tokenProvider,
		private RememberMeHandlerInterface $rememberMeHandler,
		private DatabaseManager $databaseManager,
		private Request $request,
		private Response $response,
		private SessionInterface $session, */
		private ContainerInterface $container,
		private string $cookie_name, 
		private string $session_name,
	)
	{}
	
	public function push( TokenStorageInterface $storage ): void
	{
		//$series = $this -> request -> cookies -> get( $this -> cookie_name );
		$series = $this -> container -> get( \Request :: class ) -> cookies -> get( $this -> cookie_name );
		
		//$session_token = $this -> session -> get( $this -> session_name );
		$session_token = $this -> container -> get( \Session :: class ) -> get( $this -> session_name );
		
		if ( ! is_null ( $series ) && is_null ( $session_token ) )
		{
			try
			{
				//$this -> login( $storage, $series );
				$this -> container -> get( \AuthenticationHandler :: class ) -> login( $storage, $series );
			}
			catch ( AuthenticationException )
			{
				//$this -> response -> headers -> clearCookie( $this -> cookie_name );
				$this -> clear( $storage );
			}
		}
		else if ( ! is_null ( $session_token ) )
		{
			$email = unserialize ( $session_token ) -> getUser() -> getEmail();
			
			try
			{
				$user = $this -> container -> get( self :: CONT_DATABASE_USER_PROVIDER ) -> loadUserByIdentifier( $email );
				
				$this -> container -> get( \AuthenticationHandler :: class ) -> createSession( $user, 'secured_area' );
			}
			catch ( UserNotFoundException )
			{
				$this -> clear( $storage );
			}
		}
	}
	
	public function login( TokenStorageInterface $storage, string $series ): void
	{
		//$token = $this -> tokenProvider -> loadTokenByIdentifier( $series );
		$token = $this -> container -> get( self :: CONT_DATABASE_TOKEN_PROVIDER ) -> loadTokenByIdentifier( $series );
		
		//$this -> rememberMeHandler -> createRememberMeCookie( $token -> getUser() );
		$this -> container -> get( self :: CONT_REMEMBER_HASHER ) -> createRememberMeCookie( $token -> getUser() );
		
		$this -> save( $storage, $token );
	}
	
	public function getCookie(): ?Cookie
	{
		//return $this -> request -> attributes -> get( ResponseListener :: COOKIE_ATTR_NAME );
		return $this -> container -> get( \Request :: class ) -> attributes -> get( ResponseListener :: COOKIE_ATTR_NAME );
	}
	
	public function save( TokenStorageInterface $storage, TokenInterface $token ): void
	{
		$storage -> setToken( $token );
		
		//$this -> session -> set( $this -> session_name, serialize ( $token ) );
		$this -> container -> get( \Session :: class ) -> set( $this -> session_name, serialize ( $token ) );
		
		//$this -> session -> set( Security :: LAST_USERNAME, $token -> getUser() -> getUsername() );
		$this -> container -> get( \Session :: class ) -> set( Security :: LAST_USERNAME, $token -> getUser() -> getUsername() );
		
		$cookie = $this -> getCookie();
		
		//$series = $this -> request -> cookies -> get( $this -> cookie_name );
		$series = $this -> container -> get( \Request :: class ) -> cookies -> get( $this -> cookie_name );
		
		if ( ! empty ( $cookie ) )
		{
            //$this -> response -> headers -> setCookie( $cookie );
			$this -> container -> get( \Response :: class ) -> headers -> setCookie( $cookie );
			
			if ( ! empty ( $series ) )
			{
				//$closure = $this -> databaseManager -> update( \RememberMeToken\Series\By\Series :: class );
				$closure = $this -> container -> get( \Database :: class ) -> update( \RememberMeToken\Series\By\Series :: class );
				
				$closure( $cookie -> getValue(), $series );
			}
        }
	}
	
	public function clear( TokenStorageInterface $storage ): void
	{
		//$series = $this -> request -> cookies -> get( $this -> cookie_name );
		$series = $this -> container -> get( \Request :: class ) -> cookies -> get( $this -> cookie_name );
		
		if ( ! empty ( $series ) )
		{
			//$this -> databaseManager -> delete( \RememberMeToken\By\Series :: class )( $series );
			$this -> container -> get( \Database :: class ) -> delete( \RememberMeToken\By\Series :: class )( $series );
			
			//$this -> response -> headers -> clearCookie( $this -> cookie_name );
			$this -> container -> get( \Response :: class ) -> headers -> clearCookie( $this -> cookie_name );
		}
		
		$storage -> setToken( null );
		
		$this -> container -> get( \Request :: class ) -> getSession() -> invalidate();
	}
}