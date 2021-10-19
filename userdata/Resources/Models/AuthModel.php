<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models;

//use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints as Assert;
use Nouvu\Resources\Entity\User;
//use Nouvu\Web\View\Builder\BuilderHtml;

final class AuthModel extends AbstractModel
{
	public function getLogin(): string
	{
		return $this -> app -> request -> request -> get( 'login', '' );
	}
	
	public function getPassword(): string
	{
		return $this -> app -> request -> request -> get( 'password', '' );
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
	
	protected function constraint(): Assert\Collection
	{
		$assert_validPassword = new Assert\IsTrue( [ 'message' => 'Неверный логин или пароль' ] );
		
		$assert_userFound = new Assert\IsTrue( [ 'message' => 'Пользователь не найден' ] );
		
		return new Assert\Collection( [
			'login' => [],
			'password' => [],
			'remember_check' => [],
			'submit' => [],
			'_validPassword' => $assert_validPassword,
			'_userFound' => $assert_userFound,
		] );
	}
	
	public function validator( array $input ): array
	{
		$constraint = $this -> constraint();
		
		$groups = new Assert\GroupSequence( [ 'Default', 'custom' ] );
		
		return iterator_to_array ( $this -> app -> validator -> validate( $input, $constraint, $groups ) );
	}
}
