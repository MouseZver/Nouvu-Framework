<?php

//https://github.com/hwi/HWIOAuthBundle/blob/master/Tests/Controller/LoginControllerTest.php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\{ InMemoryUserProvider, UserChecker };
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\{ UsernamePasswordToken, RememberMeToken };
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Security;

use Nouvu\Web\Component\Security\Core\User\DatabaseUserProvider;
use Nouvu\Web\Http\Controllers\AbstractController;
use Nouvu\Web\View\Repository\CommitRepository;
//use Nouvu\Resources\{ Entity\User, Entity\UserRegisterType, Form\UserType };

final class AuthController extends AbstractController
{
	public function login(): CommitRepository
	{
		if ( $this -> isGranted( [ 'ROLE_USER' ] ) )
		{
			return $this -> redirect( '/' );
		}
		
		$model = $this -> getModel();
		
		if ( $this -> app -> request -> isMethod( 'POST' ) )
		{
			$input = $this -> getPost();
			
			$input['_validPassword'] = $input['_userFound'] = true;
			
			$input['remember_check'] = isset ( $input['remember_check'] );
			
			try
			{
				$user = $this -> app -> container -> get( 'security.database.user_provider' ) 
					-> loadUserByIdentifier( $model -> getLogin() );
				
				// Login or Password not valid.
				$input['_validPassword'] = $this -> getEncoder( $user ) 
					-> isPasswordValid( $user -> getPassword(), $model -> getPassword(), $user -> getSalt() );
				
			}
			catch ( UsernameNotFoundException )
			{
				$input['_userFound'] = false;
			}
			
			
			
			$errors = $model -> validator( $input );
			
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
				$handler = new \Nouvu\Web\Component\Security\NouvuAuthenticationHandler( $this -> app );
				
				$handler -> handle( $user, 'secured_area', $input['remember_check'] );
				
				//var_dump ( $_SESSION );
				
				return $this -> redirect( '/' );
			}
		}
		
		return $this -> render( 'user/login', 'default-template' );
	}
}
