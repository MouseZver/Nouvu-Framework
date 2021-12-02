<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Controllers;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Web\Http\Controllers\AbstractController;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Web\Component\Validator\Exception\ViolationsException;

final class RegistrationController extends AbstractController
{
	public function register(): CommitRepository
	{
		if ( $this -> isGranted( [ 'ROLE_USER' ] ) )
		{
			return $this -> redirect( '/' );
		}
		
		$this -> title( [ 'Регистрация' ] );
		
		$registration = $this -> getModel( 'registration.registration' );
		
		if ( $this -> app -> request -> isMethod( 'POST' ) )
		{
			try
			{
				$user = $registration -> validation( function ( UserInterface $user ): void
				{
					$password = $this -> getEncoder( $user ) -> encodePassword( $user -> getPlainPassword(), $user -> getSalt() );
					
					$user -> setPassword( $password );
				}, 
				$registration -> getUserForm() );
				
				$confirm = md5 ( password_hash ( $user -> getEmail(), PASSWORD_DEFAULT ) );
				
				$registration -> sendEmail( $user, $confirm );
				
				$registration -> save( $user, $confirm );
				
				return $this -> render( 'user/register-confirm', 'user/form-template' );
			}
			catch ( ViolationsException $e )
			{
				if ( $this -> isAjax() )
				{
					return $this -> customJson( $e -> getErrors() );
				}
				
				$this -> app -> request -> attributes -> set( 'errors', $e );
			}
		}
		
		return $this -> render( 'user/register', 'user/form-template' );
	}
	
	public function confirm(): CommitRepository
	{
		if ( $this -> isGranted( [ 'ROLE_USER' ] ) )
		{
			return $this -> redirect( '/' );
		}
		
		$this -> title( [ 'Активация аккаунта' ] );
		
		$account = $this -> getModel( 'registration.confirm' );
		
		if ( ! empty ( $account -> getConfirm() ) )
		{
			$confirm = $account -> getDataConfirm();
			
			if ( ! $confirm -> has( 'id' ) )
			{
				return $this -> redirect( '/' );
			}
			
			try
			{
				$user = $account -> validation( $confirm );
				
				$account -> sendEmail( $user );
				
				$account -> updateUser( $user, $confirm );
				
				return $this -> render( 'user/register-success', 'user/form-template' );
			}
			catch ( ViolationsException | UsernameNotFoundException )
			{
				/* if ( $this -> isAjax() )
				{
					return $this -> customJson( $e -> getErrors() );
				}
				
				$this -> app -> request -> attributes -> set( 'errors', $e ); */
			}
		}
		
		return $this -> redirect( '/' );
	}
}
