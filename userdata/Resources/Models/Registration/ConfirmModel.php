<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Registration;

use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Web\Component\Config\Repository;
use Nouvu\Web\Component\Database\DatabaseRequestInterface AS Nouvu;
use Nouvu\Web\Component\Validator\Exception\ViolationsException;
use Nouvu\Resources\Models\{ AbstractModel, Traits };

final class ConfirmModel extends AbstractModel
{
	use Traits\GetQueryConfirm;
	
	public function getDataConfirm(): Repository
	{
		$closure = $this -> app -> repository -> get( 'query.database.select.confirm-account' );
		
		return new Repository( $closure( $this -> getConfirm() ) -> get( Nouvu :: FETCH_ASSOC ) ?? [] );
	}
	
	public function validation( Repository $confirm ): UserInterface
	{
		if ( strtotime ( $confirm -> get( 'created_at' ) . '  +2 hour' ) < time () )
		{
			$failed = new ViolationsException( 'Validation failed' );
			
			$failed -> setErrors( [ 'confirm' => 'Запрос на подтверждение аккаунта просрочен' ] );
			
			$this -> app -> repository -> get( 'query.database.delete.confirm-account' )( $confirm -> get( 'id' ) );
			
			throw $failed;
		}
		
		$user = $this -> app -> container 
			-> get( 'security.database.user_provider' ) -> loadUserByIdentifier( $confirm -> get( 'email' ) );
		
		return $user;
	}
	
	public function sendEmail( UserInterface $user ): void
	{
		$this -> app -> mail -> setTo( $user -> getEmail(), $user -> getUsername() );
		
		$this -> app -> mail -> setSubject( 'Регистрация | Nouvu' );
		
		$this -> app -> mail -> setContent( sprintf ( 
			'<p>Hello <b>%s</b>!<p>' .
				'<p>Регистрация успешно завершена. Ваш аккаунт активирован.</p>', 
			$user -> getUsername(),
		) );
		
		$this -> app -> mail -> send();
	}
	
	public function updateUser( UserInterface $user, Repository $confirm ): void
	{
		$this -> app -> repository -> get( 'query.database.update.email_confirmed' )( $user );
		
		$this -> app -> repository -> get( 'query.database.delete.confirm-account' )( $confirm -> get( 'id' ) );
	}
}
