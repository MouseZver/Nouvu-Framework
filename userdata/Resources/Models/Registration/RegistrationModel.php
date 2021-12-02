<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Registration;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Web\Component\Security\NouvuAuthenticationHandler;
use Nouvu\Resources\Entity\User;
use Nouvu\Resources\Models\{ AbstractModel, Traits };

final class RegistrationModel extends AbstractModel
{
	use Traits\GetRequestUsername;
	use Traits\GetRequestEmail;
	use Traits\GetRequestFirstPassword;
	use Traits\GetRequestSecondPassword;
	
	public function getUserForm(): UserInterface
	{
		$user = new User;
		
		$user -> setUsername( $this -> getUsername() );
		
		$user -> setEmail( mb_strtolower ( $this -> getEmail() ) );
		
		$user -> setUsername( $this -> getUsername() );
		
		$user -> setPlainPassword( $this -> getFirstPassword() );
		
		return $user;
	}
	
	public function validation( \Closure $encoder, UserInterface $user ): UserInterface
	{
		foreach ( [ '_username-email', '_username', '_email', '_password' ] AS $key )
		{
			$this -> app -> request -> request -> set( $key, true );
		}
		
		if ( strcmp ( $this -> getFirstPassword(), $this -> getSecondPassword() ) != 0 )
		{
			$this -> app -> request -> request -> set( '_password', false );
		}
		
		if ( ! empty ( $user -> getUsername() ) && strcmp ( mb_strtolower ( $user -> getUsername() ), $user -> getEmail() ) == 0 )
		{
			$this -> app -> request -> request -> set( '_username-email', false );
		}
		
		// Поиск username или email
		$closure = $this -> app -> repository -> get( 'query.database.select.users_username|email' );
		
		foreach ( $closure( $user -> getUsername(), $user -> getEmail() ) -> getAll() AS $result )
		{
			if ( strcmp ( mb_strtolower ( $user -> getUsername() ), mb_strtolower ( $result -> username ) ) == 0 )
			{
				$this -> app -> request -> request -> set( '_username', false );
			}
			
			if ( strcmp ( $user -> getEmail(), $result -> email ) == 0 )
			{
				$this -> app -> request -> request -> set( '_email', false );
			}
		}
		
		$this -> app -> validator -> validate( 'POST', $this -> app -> repository -> get( 'validator.form.registration' ) );
		
		$encoder( $user );
		
		return $user;
	}
	
	public function sendEmail( UserInterface $user, string $confirm ): void
	{
		$this -> app -> mail -> setTo( $user -> getEmail(), $user -> getUsername() );
		
		$this -> app -> mail -> setSubject( 'Регистрация | Nouvu' );
		
		$this -> app -> mail -> setContent( sprintf ( 
			'<p>Регистрация почти завершена.<br>Для активации аккаунта, перейдите по ссылке:</p>' .
				'<p>%s</p>', 
			$this -> app -> request -> getSchemeAndHttpHost() . '/confirm-account?confirm=' . $confirm,
		) );
		
		$this -> app -> mail -> send();
	}
	
	public function save( UserInterface $user, string $confirm ): void
	{
		$this -> app -> repository -> get( 'query.database.insert.users_pre_register' )( $user, $confirm );
		
		$this -> app -> repository -> get( 'query.database.insert.users_register' )( $user );
	}
}
