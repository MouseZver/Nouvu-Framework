<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models;

use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints as Assert;
use Nouvu\Resources\Entity\User;
//use Nouvu\Web\View\Builder\BuilderHtml;

final class RegistrationModel extends AbstractModel
{
	public function getUsername(): string
	{
		return $this -> app -> request -> request -> get( 'username', '' );
	}
	
	public function getEmail(): string
	{
		return $this -> app -> request -> request -> get( 'email', '' );
	}
	
	public function getFirstPassword(): string
	{
		return $this -> app -> request -> request -> get( 'password', [] )['first'] ?? '';
	}
	
	public function getSecondPassword(): string
	{
		return $this -> app -> request -> request -> get( 'password', [] )['second'] ?? '';
	}
	
	public function getErrors(): string
	{
		$errors = $this -> app -> request -> attributes -> get( 'errors' );
		
		if ( is_null ( $errors ) )
		{
			return '';
		}
		
		return implode ( PHP_EOL, array_map ( 
			fn( $e ) => sprintf ( '<p>%s</p>', $e -> getMessage() ), 
			iterator_to_array ( $errors ) 
		) );
	}
	
	public function getErrorsArray(): array
	{
		$errors = $this -> app -> request -> attributes -> get( 'errors' );
		
		if ( is_null ( $errors ) )
		{
			return [];
		}
		
		return array_map ( fn( $e ) => $e -> getMessage(), iterator_to_array ( $errors ) );
	}
	
	public function getUser(): User
	{
		$user = new User;
		
		$user -> setUsername( $this -> getUsername() );
		
		$user -> setEmail( mb_strtolower ( $this -> getEmail() ) );
		
		$user -> setUsername( $this -> getUsername() );
		
		$user -> setPlainPassword( $this -> getFirstPassword() );
		
		return $user;
	}
	
	public function validator( array $input )
	{
		// This field was not expected.
		unset ( $input['submit'] );
		
		$assertUsername = [ 
			new Assert\Length( [ 
				'min' => 3, 'max' => 50, 
				'minMessage' => 'Ваше имя пользователя должно содержать не менее {{ limit }} символов',
				'maxMessage' => 'Ваше имя пользователя не может быть длиннее {{ limit }} символов'
			] ),
			new Assert\Regex( [ 
				'pattern' => '/^\w+$/ui', 
				'message' => 'Имя пользователя может содержать только латинские символы',
			] )
		];
		
		$assertEmail = [
			new Assert\Email( [ 'message' => 'Неверный адрес электронной почты' ] ),
			new Assert\NotBlank( [ 'message' => 'Адрес электронной почты не должен быть пустой' ] )
		];
		
		$assertPassword = new Assert\Length( [ 
			'min' => 6, 
			'minMessage' => 'Ваш пароль должен содержать не менее {{ limit }} символов',
		] );
		
		/* $assertConfirmPassword = new Assert\Length( [ 
			'min' => 6, 
			'minMessage' => 'Ваш проверочный пароль должен содержать не менее {{ limit }} символов',
		] ); */
		
		$assert_username = new Assert\IsTrue( [ 'message' => 'Имя пользователя уже используются' ] );
		
		$assert_email = new Assert\IsTrue( [ 'message' => 'Адрес электронной почты уже используются' ] );
		
		$assert_password = new Assert\IsTrue( [ 'message' => 'Ваш пароль не совпадает с паролем для подтверждения' ] );
		
		
		$constraint = new Assert\Collection( [
			'username' => $assertUsername,
			'email' => $assertEmail,
			'password' => new Assert\Collection( [
				'first' => $assertPassword,
				'second' => [],
			] ),
			'_username' => $assert_username,
			'_email' => $assert_email,
			'_password' => $assert_password,
		] );
		
		$groups = new Assert\GroupSequence( [ 'Default', 'custom' ] );
		
		return $this -> app -> validator -> validate( $input, $constraint, $groups );
	}
}
