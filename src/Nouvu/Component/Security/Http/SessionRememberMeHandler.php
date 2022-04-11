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
//use Nouvu\Framework\Http\Kernel;

class SessionRememberMeHandler
{
	public function __construct ( 
		//private Kernel $kernel,
		private DatabaseTokenProvider $tokenProvider,
		private RememberMeHandlerInterface $rememberMeHandler,
		private DatabaseManager $databaseManager,
		private Request $request,
		private Response $response,
		private SessionInterface $session,
		private string $cookie_name, 
		private string $session_name,
	)
	{}
	
	public function push( TokenStorageInterface $storage ): void
	{
		$series = $this -> request -> cookies -> get( $this -> cookie_name );
		
		$session_token = $this -> session -> get( $this -> session_name );
		
		if ( ! is_null ( $series ) && is_null ( $session_token ) )
		{
			try
			{
				$this -> login( $storage, $series );
			}
			catch ( AuthenticationException )
			{
				$this -> response -> headers -> clearCookie( $this -> cookie_name );
			}
		}
		else if ( ! is_null ( $session_token ) )
		{
			$this -> save( $storage, unserialize ( $session_token ) );
		}
	}
	
	public function login( TokenStorageInterface $storage, string $series ): void
	{
		$token = $this -> tokenProvider -> loadTokenByIdentifier( $series );
		
		$this -> rememberMeHandler -> createRememberMeCookie( $token -> getUser() );
		
		$this -> save( $storage, $token );
	}
	
	public function getCookie(): ?Cookie
	{
		return $this -> request -> attributes -> get( ResponseListener :: COOKIE_ATTR_NAME );
	}
	
	public function save( TokenStorageInterface $storage, TokenInterface $token ): void
	{
		$storage -> setToken( $token );
		
		$this -> session -> set( $this -> session_name, serialize ( $token ) );
		
		$this -> session -> set( Security :: LAST_USERNAME, $token -> getUser() -> getUsername() );
		
		$cookie = $this -> getCookie();
		
		$series = $this -> request -> cookies -> get( $this -> cookie_name );
		
		if ( ! empty ( $cookie ) )
		{
            $this -> response -> headers -> setCookie( $cookie );
			
			if ( ! empty ( $series ) )
			{
				$closure = $this -> databaseManager -> update( \RememberMeToken\Series\By\Series :: class );
				
				$closure( $cookie -> getValue(), $series );
			}
        }
	}
	
	public function clear( TokenStorageInterface $storage ): void
	{
		$series = $this -> request -> cookies -> get( $this -> cookie_name );
		
		if ( ! empty ( $series ) )
		{
			$this -> databaseManager -> delete( \RememberMeToken\By\Series :: class )( $series );
			
			$this -> response -> headers -> clearCookie( $this -> cookie_name );
		}
		
		$storage -> setToken( null );
		
		$this -> request -> getSession() -> invalidate();
	}
}