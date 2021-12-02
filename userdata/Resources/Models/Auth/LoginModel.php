<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Auth;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Web\Component\Security\NouvuAuthenticationHandler;
use Nouvu\Resources\Models\{ AbstractModel, Traits };

final class LoginModel extends AbstractModel
{
	use Traits\GetRequestLogin;
	use Traits\GetRequestPassword;
	
	public function validation( \Closure $encoder ): UserInterface
	{
		foreach ( [ '_isPasswordValid', '_userFound' ] AS $key )
		{
			$this -> app -> request -> request -> set( $key, true );
		}
		
		try
		{
			$user = $this -> app -> container 
				-> get( 'security.database.user_provider' ) -> loadUserByIdentifier( $this -> getLogin() );
			
			$this -> app -> request -> request -> set( '_isPasswordValid', $encoder( $user ) );
			
			$this -> app -> request -> request -> set( '_account', $user -> getConfirmed() );
		}
		catch ( UsernameNotFoundException )
		{
			$this -> app -> request -> request -> set( '_userFound', false );
		}
		
		$this -> app -> validator -> validate( 'POST', $this -> app -> repository -> get( 'validator.form.login' ) );
		
		return $user;
	}
	
	public function sendEmail( UserInterface $user ): void
	{
		$this -> app -> mail -> setTo( $user -> getEmail(), $user -> getUsername() );
		
		$this -> app -> mail -> setSubject( 'Авторизация | Nouvu' );
		
		$this -> app -> mail -> setContent( sprintf ( 
			'Добро пожаловать <b>%s</b>! Это тестовое сообщение в <b>LoginModel</b> и вы успешно авторизовались на сайте', 
			$user -> getUsername(),
		) );
		
		$this -> app -> mail -> send();
	}
	
	public function authUser( UserInterface $user ): void
	{
		$handler = new NouvuAuthenticationHandler( $this -> app );
		
		$handler -> handle( $user, 'secured_area', $this -> app -> request -> request -> has( 'remember_check' ) );
	}
}
