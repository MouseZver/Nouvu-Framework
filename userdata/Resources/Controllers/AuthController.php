<?php

//https://github.com/hwi/HWIOAuthBundle/blob/master/Tests/Controller/LoginControllerTest.php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Controllers;

use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nouvu\Web\Http\Controllers\AbstractController;
use Nouvu\Web\View\Repository\CommitRepository;
//use Nouvu\Resources\{ Entity\User, Entity\UserRegisterType, Form\UserType };

final class AuthController extends AbstractController
{
	public function login(): CommitRepository
	{
		/* $password = $this -> getEncoder( $user ) -> encodePassword( User :: class, $user -> getPlainPassword() );
		
		->isPasswordValid($user->getPassword(), $password ) */
		
		/* $requestStack = new RequestStack();
        $requestStack->push($this->request);
        $this->authenticationUtils = new AuthenticationUtils($requestStack); */
		
		
		
		$model = $this -> getModel();
		
		if ( $this -> app -> request -> isMethod( 'POST' ) )
		{
			$input = $this -> getPost();
			
			try
			{
				$userProvider = new \Nouvu\Web\Component\Security\Core\UserDatabaseUserProvider( $this -> app );
				
				$user = $userProvider -> loadUserByIdentifier( $model -> getLogin() );
			}
			catch ( \Symfony\Component\Security\Core\Exception\UserNotFoundException )
			{
				// юзер не найден
				
				var_dump ( 'юзер не найден' );
			}
			
			// Login or Password not valid.
			$input['_validPassword'] = $this -> getEncoder( $user ) -> isPasswordValid( $user, $model -> getPassword() );
			
			$errors = $model -> validator( $input, $user );
			
			if ( count ( $errors ) )
			{
				$this -> app -> request -> attributes -> set( 'errors', $errors );
				
				var_dump ( 'ошибки' );
			}
			else
			{
				var_dump ( 'ошибки' );
			}
		}
		
		return $this -> render( 'user/login', 'default-template' );
	}
	
	
	/* {
		
		
		$userProvider = new \Symfony\Component\Security\Core\User\InMemoryUserProvider( [
			'admin' => [
				// пароль "foo"
				'password' => '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==',
				'roles'    => ['ROLE_ADMIN'],
			],
		] );
		
		
		$daoProvider = new \Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider(
			$userProvider,
			new \Symfony\Component\Security\Core\User\UserChecker(),
			'secured_area',
			$this -> app -> container -> get( 'Encoder.factory' )
		);
		
		$validPassword = $encoder->isPasswordValid(
			$user->getPassword(), // зашифрованный пароль
			$plainPassword,       // отправленный пароль
			$user->getSalt()
		);
		
		$unauthenticatedToken = new UsernamePasswordToken(
            $username,
            $password,
            $this->providerKey
        );
		
		$daoProvider -> authenticate( $unauthenticatedToken );
	} */
	
	
	public function __login(): CommitRepository
	{
		// This data is most likely to be retrieven from the Request object (from Form)
        // But to make it easy to understand ...
        $_username = "batman";
        $_password = "batmobil";

        // Retrieve the security encoder of symfony
        $factory = $this->get('security.encoder_factory');

        /// Start retrieve user
        // Let's retrieve the user by its username:
        // If you are using FOSUserBundle:
        $user_manager = $this->get('fos_user.user_manager');
        $user = $user_manager->findUserByUsername($_username);
        // Or by yourself
        $user = $this->getDoctrine()->getManager()->getRepository("userBundle:User")
                ->findOneBy(array('username' => $_username));
        /// End Retrieve user

        // Check if the user exists !
        if(!$user){
            return new Response(
                'Username doesnt exists',
                Response::HTTP_UNAUTHORIZED,
                array('Content-type' => 'application/json')
            );
        }

        /// Start verification
        $encoder = $factory->getEncoder($user);
        $salt = $user->getSalt();

        if(!$encoder->isPasswordValid($user->getPassword(), $_password, $salt)) {
            return new Response(
                'Username or Password not valid.',
                Response::HTTP_UNAUTHORIZED,
                array('Content-type' => 'application/json')
            );
        } 
        /// End Verification

        // The password matches ! then proceed to set the user in session
        
        //Handle getting or creating the user entity likely with a posted form
        // The third parameter "main" can change according to the name of your firewall in security.yml
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);

        // If the firewall name is not main, then the set value would be instead:
        // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
        $this->get('session')->set('_security_main', serialize($token));
        
        // Fire the login event manually
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        
        /*
         * Now the user is authenticated !!!! 
         * Do what you need to do now, like render a view, redirect to route etc.
         */
        return new Response(
            'Welcome '. $user->getUsername(),
            Response::HTTP_OK,
            array('Content-type' => 'application/json')
        );
	}
}
