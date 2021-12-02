<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Auth;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Resources\Models\{ AbstractModel, Traits };

final class ForgotModel extends AbstractModel
{
	use Traits\GetRequestEmail;
	
	public function validation(): UserInterface
	{
		try
		{
			$this -> app -> request -> request -> set( '_email', true );
			
			$user = $this -> app -> container 
				-> get( 'security.database.user_provider' ) -> loadUserByIdentifier( $this -> getEmail() );
		}
		catch ( UsernameNotFoundException )
		{
			if ( ! empty ( $this -> getEmail() ) )
			{
				$this -> app -> request -> request -> set( '_email', false );
			}
		}
		
		$this -> app -> validator -> validate( 'POST', $this -> app -> repository -> get( 'validator.form.forgotPassword' ) );
		
		return $user;
	}
	
	public function sendEmail( UserInterface $user, string $confirm ): void
	{
		$this -> app -> mail -> setTo( $user -> getEmail(), $user -> getUsername() );
		
		$this -> app -> mail -> setSubject( 'Восстановление пароля' );
		
		$this -> app -> mail -> setContent( sprintf ( 
			'<p>Hello <b>%s</b>!<p>' .
				'<p>Если Вы не запрашивали это письмо, то можете спокойно его <b>проигнорировать</b>.</p>' .
				'<p>Для обновления пароля пройдите по нижеследующей ссылке. Это позволит Вам выбрать новый пароль.</p>' .
				'<h2>Forgot Password</h2>' .
				'<p>%s</p>', 
			$user -> getUsername(),
			$this -> app -> request -> getSchemeAndHttpHost() . '/lost-password?confirm=' . $confirm
		) );
		
		$this -> app -> mail -> send();
	}
	
	public function save( UserInterface $user, string $confirm ): void
	{
		$this -> app -> repository -> get( 'query.database.insert.forgot-password' )( $user, $confirm );
	}
}
