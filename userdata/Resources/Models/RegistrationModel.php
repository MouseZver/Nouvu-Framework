<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models;

//use Symfony\Component\Form\FormView;
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
			$errors 
		) );
	}
	
	public function getErrorsArray(): array
	{
		$errors = $this -> app -> request -> attributes -> get( 'errors' );
		
		if ( is_null ( $errors ) )
		{
			return [];
		}
		
		return array_map ( fn( $e ) => $e -> getMessage(), $errors );
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
	
	protected function constraint(): Assert\Collection
	{
		$assertUsername = [ 
			new Assert\Length( [ 
				'min' => 3, 'max' => 50, 
				'minMessage' => 'Ваше имя пользователя должно содержать не менее {{ limit }} символов',
				'maxMessage' => 'Ваше имя пользователя не может быть длиннее {{ limit }} символов'
			] ),
			new Assert\Regex( [ 
				'pattern' => '/^\w+$/ui', 
				'message' => 'Имя пользователя может содержать только [A-Za-z0-9_] символы',
			] )
		];
		
		$assertEmail = [
			new Assert\Email( [ 'message' => 'Неверный адрес электронной почты' ] ),
			new Assert\NotBlank( [ 'message' => 'Адрес электронной почты не должен быть пустой' ] )
		];
		
		$assertPassword = new Assert\Length( [ 
			'min' => 10, 
			'max' => 100,
			'minMessage' => 'Ваш пароль должен содержать не менее {{ limit }} символов',
			'maxMessage' => 'Ваш пароль превышает {{ limit }} символов',
		] );
		
		/* $assertConfirmPassword = new Assert\Length( [ 
			'min' => 6, 
			'minMessage' => 'Ваш проверочный пароль должен содержать не менее {{ limit }} символов',
		] ); */
		
		$assert_username = new Assert\IsTrue( [ 'message' => 'Имя пользователя уже используются' ] );
		
		$assert_email = new Assert\IsTrue( [ 'message' => 'Адрес электронной почты уже используются' ] );
		
		$assert_password = new Assert\IsTrue( [ 'message' => 'Ваш пароль не совпадает с паролем для подтверждения' ] );
		
		$assert_username_email = new Assert\IsTrue( [ 'message' => 'Ваше имя пользователя не должно быть одинаковым с эл. почтой' ] );
		
		
		/* [
			'username|Length|min:3,max:50',
			'username|Regex|pattern:/^\w+$/ui',
			'email|Email|message:Неверный адрес электронной почты',
			'email|NotBlank|message:Адрес электронной почты не должен быть пустой',
			'password.first|Length|min:10,max:100',
			'password.second',
			'submit',
			'_username|IsTrue|message:Имя пользователя уже используются',
			'_email|IsTrue|message:Адрес электронной почты уже используются',
			'_password|IsTrue|message:Ваш пароль не совпадает с паролем для подтверждения',
			'_username_email|IsTrue|message:Ваше имя пользователя не должно быть одинаковым с эл. почтой',
		] */
		
		return new Assert\Collection( [
			'username' => $assertUsername,
			'email' => $assertEmail,
			'password' => new Assert\Collection( [
				'first' => $assertPassword,
				'second' => [],
			] ),
			'submit' => [],
			'_username' => $assert_username,
			'_email' => $assert_email,
			'_password' => $assert_password,
			'_username|email' => $assert_username_email
		] );
	}
	
	public function validator( array $input, User $user ): array
	{
		$input['_username|email'] = $input['_username'] = $input['_email'] = $input['_password'] = true;
		
		if ( strcmp ( $this -> getFirstPassword(), $this -> getSecondPassword() ) != 0 )
		{
			$input['_password'] = false;
		}
		
		if ( strcmp ( mb_strtolower ( $user -> getUsername() ), $user -> getEmail() ) == 0 )
		{
			$input['_username|email'] = false;
		}
		
		// Поиск username или email
		$select = $this -> app -> repository 
			-> get( 'query.database.select.users_username|email' )( $user -> getUsername(), $user -> getEmail() );
		
		foreach ( $select -> getAll() AS $result )
		{
			if ( strcmp ( mb_strtolower ( $user -> getUsername() ), mb_strtolower ( $result -> username ) ) == 0 )
			{
				$input['_username'] = false;
			}
			
			if ( strcmp ( $user -> getEmail(), $result -> email ) == 0 )
			{
				$input['_email'] = false;
			}
		}
		
		$constraint = $this -> constraint();
		
		$groups = new Assert\GroupSequence( [ 'Default', 'custom' ] );
		
		return iterator_to_array ( $this -> app -> validator -> validate( $input, $constraint, $groups ) );
	}
}
