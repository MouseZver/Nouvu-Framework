<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Auth;

use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Web\Component\Config\Repository;
use Nouvu\Web\Component\Database\DatabaseRequestInterface AS Nouvu;
use Nouvu\Web\Component\Validator\Exception\ViolationsException;
use Nouvu\Resources\Models\{ AbstractModel, Traits };

final class LostModel extends AbstractModel
{
	use Traits\GetQueryConfirm;
	use Traits\GetRequestFirstPassword;
	use Traits\GetRequestSecondPassword;
	
	public function getDataConfirm(): Repository
	{
		$closure = $this -> app -> repository -> get( 'query.database.select.lost-password' );
		
		return new Repository( $closure( $this -> getConfirm() ) -> get( Nouvu :: FETCH_ASSOC ) ?? [] );
	}
	
	public function validation( \Closure $encoder, Repository $confirm ): UserInterface | null
	{
		if ( strtotime ( $confirm -> get( 'created_at' ) . '  +2 hour' ) < time () )
		{
			$failed = new ViolationsException( 'Validation failed' );
			
			$failed -> setErrors( [ 'confirm' => 'Запрос на восстановления пароля просрочен' ] );
			
			$this -> app -> repository -> get( 'query.database.delete.lost-password' )( $confirm -> get( 'id' ) );
			
			throw $failed;
		}
		else if ( $this -> app -> request -> isMethod( 'POST' ) )
		{
			$this -> app -> request -> request -> set( 
				'_password', strcmp ( $this -> getFirstPassword(), $this -> getSecondPassword() ) == 0 
			);
			
			$this -> app -> validator -> validate( 'POST', $this -> app -> repository -> get( 'validator.form.lostPassword' ) );
			
			$user = $this -> app -> container 
				-> get( 'security.database.user_provider' ) -> loadUserByIdentifier( $confirm -> get( 'email' ) );
			
			$encoder( $user );
			
			return $user;
		}
		
		return null;
	}
	
	public function sendEmail( UserInterface $user ): void
	{
		$this -> app -> mail -> setTo( $user -> getEmail(), $user -> getUsername() );
		
		$this -> app -> mail -> setSubject( 'Доступ к аккаунту восстановлен | Nouvu' );
		
		$this -> app -> mail -> setContent( sprintf ( 
			'<p>Hello <b>%s</b>!<p>' .
				'<p>Доступ к вашему аккаунту успешно восстановлен. Вы можете войти с новым паролем.</p>', 
			$user -> getUsername(),
		) );
		
		$this -> app -> mail -> send();
	}
	
	public function updateUser( UserInterface $user ): void
	{
		$this -> app -> repository -> get( 'query.database.update.user_password' )( $user );
		
		$this -> app -> repository -> get( 'query.database.delete.forgot-password' )( $user );
	}
}
