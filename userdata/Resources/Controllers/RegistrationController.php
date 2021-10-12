<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nouvu\Web\Http\Controllers\AbstractController;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Resources\{ Entity\User, Entity\UserRegisterType, Form\UserType };

final class RegistrationController extends AbstractController
{
	public function register(): CommitRepository
	{
		if ( $this -> app -> request -> isMethod( 'POST' ) )
		{
			$reg = new UserRegisterType;
			
			$errors = $this -> app -> validator -> validate( $reg );
			
			var_dump ( $errors );
		}
		
		return $this -> render( 'user/register', 'default-template' );
	}
	
	
	
	
	
	
	
	
	public function _register(): CommitRepository
	{
		// 1) build the form
		$user = new User();
		
		$form = $this -> createForm( UserType :: class, $user );
		
		// свои $_GET, $_POST, ...
		// 2) handle the submit (will only happen on POST)
		$form -> handleRequest();
		//$form->bindRequest($this -> app -> request);
		
		$isSubmitted = $form -> isSubmitted();
		
		var_dump ( $isSubmitted );
		
		if ( $this -> app -> request -> isMethod( 'POST' ) && $isSubmitted && $form -> isValid() )
		{
			// 3) Encode the password (you could also do this via Doctrine listener)
			$password = $this -> getEncoder( $user ) -> encodePassword( User :: class, $user -> getPlainPassword() );
			
			$user -> setPassword( $password );
			
			// 4) save the User!
			//$this -> app -> repository -> get( 'query.database.insert.users_register' )( $user );
			
			// ... do any other work - like sending them an email, etc
			// maybe set a "flash" success message for the user
			
			//return $this -> redirectToRoute('replace_with_some_route');
			
			var_dump ( 111, $user );
		}
		
		//return $this -> render( 'registration/register.html.twig', [ 'form' => $form -> createView() ] );
		
		// Symfony\Component\Form\FormView
		//var_dump ( $form->createView() :: class );
		
		/* $model = $this -> getModel();
		
		$model -> saveFormView( $form ); */
		
		foreach ( $form->createView() -> getIterator() AS $field )
		{
			print_r ( $field -> vars['full_name'] );
		}
		
		
		$this -> title( [ 'Регистрация' ] );
		
		//return $this -> render( 'error.404', 'error-template' );
		return $this -> render( 'user/register', 'default-template' );
	}
}
