<?php

declare ( strict_types = 1 );

//https://github.com/hwi/HWIOAuthBundle/blob/master/Tests/Controller/LoginControllerTest.php

namespace Nouvu\Resources\Controllers;

use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Web\Component\Validator\Exception\ViolationsException;
use Nouvu\Web\Http\Controllers\AbstractController;
use Nouvu\Web\View\Repository\CommitRepository;

final class AuthController extends AbstractController
{
	/*
		- Авторизация
	*/
	public function login(): CommitRepository
	{
		if ( $this -> isGranted( [ 'ROLE_USER' ] ) )
		{
			return $this -> redirect( '/' );
		}
		
		$this -> title( [ 'Авторизация' ] );
		
		$auth = $this -> getModel( 'auth.login' );
		
		if ( $this -> app -> request -> isMethod( 'POST' ) )
		{
			try
			{
				$user = $auth -> validation( function ( UserInterface $user ) use ( $auth ): bool
				{
					return $this -> getEncoder( $user ) 
						-> isPasswordValid( $user -> getPassword(), $auth -> getPassword(), $user -> getSalt() );
				} );
				
				$auth -> sendEmail( $user );
				
				$auth -> authUser( $user );
				
				return $this -> redirect( '/' );
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
		
		return $this -> render( 'user/login', 'user/form-template' );
	}
	
	/*
		- Выход
	*/
	public function logout(): CommitRepository
	{
		$storage = $this -> app -> container -> get( 'security.token_storage' );
		
		$cookie_name = $this -> app -> repository -> get( 'security.remember_me.name' );
		
		$series = $this -> app -> request -> cookies -> get( $cookie_name );
		
		if ( ! empty ( $series ) )
		{
			$this -> app -> repository -> get( 'query.database.delete.token' )( $series );
			
			$this -> app -> response -> headers -> clearCookie( $cookie_name );
		}
		
		$storage -> setToken( null );
		
		$this -> app -> request -> getSession() -> invalidate();
		
		return $this -> redirect( '/' );
	}
	
	/*
		- Забыл пароль
	*/
	public function forgotPassword(): CommitRepository
	{
		if ( $this -> isGranted( [ 'ROLE_USER' ] ) )
		{
			return $this -> redirect( '/' );
		}
		
		$this -> title( [ 'Восстановление пароля' ] );
		
		$forgot = $this -> getModel( 'auth.forgot' );
		
		if ( $this -> app -> request -> isMethod( 'POST' ) )
		{
			try
			{
				$user = $forgot -> validation();
				
				$confirm = md5 ( password_hash ( $user -> getEmail(), PASSWORD_DEFAULT ) );
				
				$forgot -> sendEmail( $user, $confirm );
				
				$forgot -> save( $user, $confirm );
				
				return $this -> render( 'user/forgot-password-mail', 'user/form-template' );
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
		
		return $this -> render( 'user/forgot-password', 'user/form-template' );
	}
	
	/*
		- Новый пароль
	*/
	public function lostPassword(): CommitRepository
	{
		if ( $this -> isGranted( [ 'ROLE_USER' ] ) )
		{
			return $this -> redirect( '/' );
		}
		
		$this -> title( [ 'Сброс пароля' ] );
		
		$lost = $this -> getModel( 'auth.lost' );
		
		if ( ! empty ( $lost -> getConfirm() ) )
		{
			$confirm = $lost -> getDataConfirm();
			
			if ( ! $confirm -> has( 'id' ) )
			{
				return $this -> redirect( '/' );
			}
			
			try
			{
				$user = $lost -> validation( function ( UserInterface $user ) use ( $lost ): void
				{
					$password = $this -> getEncoder( $user ) -> encodePassword( $lost -> getFirstPassword(), $user -> getSalt() );
					
					$user -> setPassword( $password );
				}, 
				$confirm );
				
				if ( $user instanceof UserInterface )
				{
					$lost -> sendEmail( $user );
					
					$lost -> updateUser( $user );
					
					return $this -> render( 'user/lost-password-success', 'user/form-template' );
				}
			}
			catch ( ViolationsException $e )
			{
				if ( $this -> isAjax() )
				{
					return $this -> customJson( $e -> getErrors() );
				}
				
				$this -> app -> request -> attributes -> set( 'errors', $e );
			}
			
			return $this -> render( 'user/lost-password', 'user/form-template' );
		}
		
		return $this -> redirect( '/' );
	}
}
