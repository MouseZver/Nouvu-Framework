<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nouvu\Web\Http\Controllers\AbstractController;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Resources\{ Entity\User, Form\UserType };

final class RegistrationController extends AbstractController
{
	public function register(): CommitRepository
	{
		// 1) build the form
		$user = new User();
		
		$form = $this -> createForm( UserType :: class, $user );
		
		// свои $_GET, $_POST, ...
		// 2) handle the submit (will only happen on POST)
		$form -> handleRequest();
		
		$isSubmitted = $form -> isSubmitted();
		
		var_dump ( $isSubmitted );
		
		if ( $isSubmitted && $form -> isValid() )
		{
			// 3) Encode the password (you could also do this via Doctrine listener)
			$password = $this -> getEncoder( $user ) -> encodePassword( $user, $user -> getPlainPassword() );
			
			$user -> setPassword( $password );
			
			// 4) save the User!
			//$this -> app -> repository -> get( 'query.database.insert.users_register' )( $user );
			
			// ... do any other work - like sending them an email, etc
			// maybe set a "flash" success message for the user
			
			//return $this -> redirectToRoute('replace_with_some_route');
			
			var_dump ( 111 );
		}
		
		//return $this -> render( 'registration/register.html.twig', [ 'form' => $form -> createView() ] );
		
		// Symfony\Component\Form\FormView
		//var_dump ( $form->createView() :: class );
		
		/* foreach ( $form->createView() AS $her )
		{
			//var_dump ( $her ); exit; улетает в бесконечность
		} */
		
		
		
		$this -> title( [ 'Регистрация' ] );
		
		//return $this -> render( 'error.404', 'error-template' );
		return $this -> render( 'user/register', 'default-template' );
	}
}
