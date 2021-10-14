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
	
	/* public function getUser(): User
	{
		$user = new User;
		
		$user -> setUsername( $this -> getUsername() );
		
		$user -> setEmail( mb_strtolower ( $this -> getEmail() ) );
		
		$user -> setUsername( $this -> getUsername() );
		
		$user -> setPlainPassword( $this -> getFirstPassword() );
		
		return $user;
	} */
	
	protected function constraint(): Assert\Collection
	{
		$assert_validPassword = new Assert\IsTrue( [ 'message' => 'Неверный логин или пароль' ] );
		
		return new Assert\Collection( [
			'login' => [],
			'password' => [],
			'submit' => [],
			'_validPassword' => $assert_validPassword,
		] );
	}
	
	public function validator( array $input, User $user ): array
	{
		$constraint = $this -> constraint();
		
		$groups = new Assert\GroupSequence( [ 'Default', 'custom' ] );
		
		return iterator_to_array ( $this -> app -> validator -> validate( $input, $constraint, $groups ) );
	}
}
