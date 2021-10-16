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
		if ( $this -> isGranted( [ 'ROLE_USER' ] ) )
		{
			return $this -> redirect( '/' );
		}
		
		$model = $this -> getModel();
		
		if ( $this -> app -> request -> isMethod( 'POST' ) )
		{
			$user = $model -> getUser();
			
			$input = $this -> getPost();
			
			$errors = $model -> validator( $input, $user );
			
			if ( count ( $errors ) )
			{
				$this -> app -> request -> attributes -> set( 'errors', $errors );
				
				if ( $this -> isAjax() )
				{
					return $this -> customJson( $model -> getErrorsArray() );
				}
			}
			else
			{
				$password = $this -> getEncoder( $user ) -> encodePassword( $user -> getPlainPassword(), $user -> getSalt() );
				
				$user -> setPassword( $password );
				
				$this -> app -> repository -> get( 'query.database.insert.users_register' )( $user );
				
				return $this -> redirect( '/' );
			}
			
		}
		
		return $this -> render( 'user/register', 'default-template' );
	}
}
