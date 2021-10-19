<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Security;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\{ UserProviderInterface, UserChecker, UserInterface };
use Symfony\Component\Security\Core\Authentication\Token\{ TokenInterface, UsernamePasswordToken };
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Nouvu\Web\Component\Security\TokenBasedRememberMeServices;
use Nouvu\Web\Foundation\Application AS App;

class NouvuAuthenticationHandler
{
	public function __construct ( private App $app )
	{
		
	}
	
	public function handle( UserInterface $user, string $providerKey, bool $remember = false )
	{
		$UsernamePasswordToken = new UsernamePasswordToken( $user, null, 'secured_area', $user -> getRoles() );
		
		$token = $this 
			-> getDaoProvider( new UserChecker, 'secured_area' ) 
			-> authenticate( $UsernamePasswordToken );
		
		$this -> app -> container -> get( 'security.token_storage' ) -> setToken( $token );
		
		if ( $remember )
		{
			$this -> getTokenServices( $providerKey ) -> login( 
				$this -> app -> request, 
				$this -> app -> response, 
				$token 
			);
			
			$cookies = $this -> app -> response -> headers -> getCookies();
			
			$closure = $this -> app -> repository -> get( 'query.database.insert.token' );
			
			$closure( $token, $cookies[0] -> getValue() );
		}
		
		$this -> sessionHandle( $user, $token );
		
		/* $service = $this -> getTokenServices( $providerKey );
		
		$service -> autoLogin( $this -> app -> request ); */
	}
	
	public function sessionHandle( UserInterface $user, TokenInterface $token ): void
	{
		$this -> app -> session -> set( $this -> app -> repository -> get( 'security.session_name' ), serialize ( $token ) );
		
		$this -> app -> session -> set( Security :: LAST_USERNAME, $user -> getUserIdentifier() );
	}
	
	public function getDaoProvider( UserChecker $checker, string $providerKey )
	{
		return new DaoAuthenticationProvider(
			$this -> app -> container -> get( 'security.database.user_provider' ),
			$checker,
			$providerKey,
			$this -> app -> container -> get( 'encoder.factory' ) 
		);
	}
	
	public function getTokenServices( string $providerKey )
	{
		return new TokenBasedRememberMeServices( 
			[ $this -> app -> container -> get( 'security.database.user_provider' ) ], 
			$this -> app -> repository -> get( 'security.secret_key' ),
			$providerKey, 
			$this -> app -> repository -> get( 'security.remember_me' )
		);
	}
}