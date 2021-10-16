<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Security;

/* use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
//use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request; */


//use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\{ UserProviderInterface, UserChecker, UserInterface };
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Nouvu\Web\Component\Security\TokenBasedRememberMeServices;
use Nouvu\Web\Foundation\Application AS App;

class NouvuAuthenticationHandler
{
	public function __construct ( private App $app )
	{
		
	}
	
	public function handle( UserInterface $user, string $providerKey, string $secret )
	{
		$UsernamePasswordToken = new UsernamePasswordToken( $user, null, 'secured_area', $user -> getRoles() );
		
		$token = $this -> getDaoProvider( new UserChecker, 'secured_area' ) -> authenticate( $UsernamePasswordToken );
		
		$this -> app -> container -> get( 'security.token_storage' ) -> setToken( $token );
		
		$this -> getTokenServices( $providerKey, $secret ) -> login( 
			$this -> app -> request, 
			$this -> app -> response, 
			$token 
		);
		
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
	
	public function getTokenServices( string $providerKey, string $secret )
	{
		return new TokenBasedRememberMeServices( 
			[ $this -> app -> container -> get( 'security.database.user_provider' ) ], 
			$secret,
			$providerKey, 
			$this -> app -> repository -> get( 'security.remember_me' )
		);
	}
}