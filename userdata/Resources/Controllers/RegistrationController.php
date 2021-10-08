<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Controllers;

//use App\Form\UserType;
//use App\Entity\User;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Nouvu\Web\Http\Controllers\BaseController;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Resources\Entity\User;
use Nouvu\Resources\Form\UserType;

final class RegistrationController extends BaseController
{
	public function register(): CommitRepository
	{
		// 1) build the form
		$user = new User();
		
		$form = $this -> createForm( UserType :: class, $user );

		// 2) handle the submit (will only happen on POST)
		$form -> handleRequest( $this -> app -> request -> request );
		
		if ( $form -> isSubmitted() && $form -> isValid() )
		{
			// 3) Encode the password (you could also do this via Doctrine listener)
			$password = $passwordEncoder -> encodePassword( $user, $user -> getPlainPassword() );
			$user -> setPassword( $password );
			
			// 4) save the User!
			/* $entityManager = $this -> getDoctrine() -> getManager();
			$entityManager -> persist( $user );
			$entityManager -> flush(); */
			
			$this -> app -> repository -> get( 'query.database.insert.users_register' )( $user );
			
			// ... do any other work - like sending them an email, etc
			// maybe set a "flash" success message for the user
			
			return $this -> redirectToRoute('replace_with_some_route');
		}
		
		return $this -> render( 'registration/register.html.twig', [ 'form' => $form -> createView() ] );
		
		
		
		
		
		$this -> title( [ 'Регистрация' ] );
		
		return $this -> render( 'error.404', 'error-template' );
		//return $this -> render( 'registration/register', 'default-template' );
	}
}
