<?php

use Psr\Container\ContainerInterface;

return [
	/*
		- example tool
	*/
	/* name => static function ( ContainerInterface $container ): returnType
	{
		return value;
	}, */
	
	/*
		- 
	*/
	\Container :: class => static function ( ContainerInterface $container ): ContainerInterface
	{
		return $container;
	},
	
	/*
		- 
	*/
	\Repository :: class => static function ( ContainerInterface $container )
	{
		// use in each config
		$app = $container -> get( 'app' );
		
		$repository = new \Nouvu\Web\Component\Config\Repository( [], '.' );
		
		foreach ( glob ( dirname ( __FILE__ ) . '/Configs/*.php' ) AS $file )
		{
			$repository -> reset( basename ( $file, '.php' ), include $file );
		}
		
		$repository -> reset( 'app.system.directory', [
			'userdata' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR,
			'root' => dirname ( __DIR__ ) . DIRECTORY_SEPARATOR,
			'view' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'Resources/View/'
		] );
		
		return $repository;
	},
	
	/*
		- 
	*/
	\Kernel :: class => static function ( ContainerInterface $container )
	{
		return new \Nouvu\Web\Http\Kernel( $container -> get( 'app' ) );
	},
	
	/*
		- 
	*/
	\Request :: class => static function ( ContainerInterface $container )
	{
		return \Symfony\Component\HttpFoundation\Request :: createFromGlobals();
	},
	
	/*
		- 
	*/
	\Response :: class => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\HttpFoundation\Response;
	},
	
	/*
		- 
	*/
	\Database :: class => static function ( ContainerInterface $container )
	{
		$database = new \Nouvu\Web\Component\Database\DatabaseManager( $container -> get( 'app' ) );
		
		$zone = $container -> get( 'repository' ) -> get( 'timezone.database' );
		
		if ( ! empty ( $zone ) )
		{
			//$database -> query( [ 'SET time_zone = "%s"', $zone ] );
			
			$database -> query( [ 'SET session time_zone = "%s"', $zone ] );
		}
		
		return $database;
	},
	
	/*
		- 
	*/
	\Router :: class => static function ( ContainerInterface $container )
	{
		return new \Nouvu\Web\Routing\Router( $container -> get( 'app' ) );
	},
	
	/*
		-
	*/
	\Session :: class => static function ( ContainerInterface $container )
	{
		$session = new \Symfony\Component\HttpFoundation\Session\Session;
		
		$session -> start();
		
		$container -> get( 'request' ) -> setSession( $session );
		
		return $session;
	},
	
	/*
		- 
	*/
	\View :: class => static function ( ContainerInterface $container )
	{
		$app = $container -> get( 'app' );
		
		$repository = $app -> repository;
		
		$viewer = new \Nouvu\Web\View\Viewer( $app -> request, $app -> response );
		
		foreach ( [ 'path', 'layout', 'extension', 'title', 'head' ] AS $name )
		{
			$viewer -> { 'set' . ucfirst ( $name ) }( $repository );
		}
		
		return $viewer;
	},
	
	/*
		- 
	*/
	\Validator :: class => static function ( ContainerInterface $container )
	{
		$validation = \Symfony\Component\Validator\Validation :: createValidator();
		
		return new \Nouvu\Web\Component\Validator\Validation( $container -> get( 'request' ), $validation );
	},
	
	/*
		- 
	*/
	\Mail :: class => static function ( ContainerInterface $container )
	{
		return new \Nouvu\Resources\System\MailerFacade( $container -> get( 'app' ) );
	},
	
	/*
		- 
	*/
	'encoder.factory' => static function ( ContainerInterface $container )
	{
		$closure = $container -> get( 'repository' ) -> get( 'security.encoder' );
		
		return new \Symfony\Component\Security\Core\Encoder\EncoderFactory( $closure() );
	},
	
	/*
		- RequestStack
	*/
	'request_stack' => static function ( ContainerInterface $container )
	{
		$requestStack = new \Symfony\Component\HttpFoundation\RequestStack();
		
		$requestStack -> push( $container -> get( 'request' ) );
		
		return $requestStack;
	},
	
	/*
		- 
	*/
	\Security :: class => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\Security\Core\Security( $container );
	},
	
	/*
		- 
	*/
	'security.database.user_provider' => static function ( ContainerInterface $container )
	{
		return new \Nouvu\Web\Component\Security\Core\User\DatabaseUserProvider( $container -> get( 'app' ) );
	},
	
	/*
		- token_storage
	*/
	'security.token_storage' => static function ( ContainerInterface $container )
	{
		$cookie_name = $container -> get( 'repository' ) -> get( 'security.remember_me.name' );
		
		$session_name = $container -> get( 'repository' ) -> get( 'security.session_name' );
		
		$storage = new \Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage(
			new \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage,
			$container
		);
		
		$remember = $container -> get( 'request' ) -> cookies -> get( $cookie_name );
		
		$session_token = $container -> get( 'session' ) -> get( $session_name );
		
		
		if ( ! is_null ( $remember ) && is_null ( $session_token ) )
		{
			try
			{
				$provider = new \Nouvu\Web\Component\Security\Core\User\DatabaseTokenProvider( $container -> get( 'app' ) );
				
				$token = $provider -> loadTokenByIdentifier( $remember );
				
				$storage -> setToken( $token );
				
				$container -> get( 'session' ) -> set( $session_name, serialize ( $token ) );
				
				$container -> get( 'session' ) -> set( 
					\Symfony\Component\Security\Core\Security :: LAST_USERNAME, 
					$storage -> getToken() -> getUser() -> getUsername()
				);
			}
			catch ( \Symfony\Component\Security\Core\Exception\AuthenticationException )
			{
				$container -> get( 'request' ) -> cookies -> remove( $cookie_name );
			}
		}
		else if ( ! is_null ( $session_token ) )
		{
			$storage -> setToken( unserialize ( $session_token ) );
			
			$container -> get( 'session' ) -> set( $session_name, $session_token );
			
			$container -> get( 'session' ) -> set( 
				\Symfony\Component\Security\Core\Security :: LAST_USERNAME, 
				$storage -> getToken() -> getUser() -> getUsername()
			);
		}
		
		return $storage;
	},
	
	/*
		- AuthenticationUtils
	*/
	'security.authentication_utils' => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\Security\Http\Authentication\AuthenticationUtils( $container -> get( 'request_stack' ) );
	},
	
	/*
		- 
	*/
	'security.role_hierarchy' => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\Security\Core\Role\RoleHierarchy(
			$container -> get( 'repository' ) -> get( 'security.hierarchy' )
		);
	},
	
	/*
		- 
	*/
	'security.role_hierarchy_voter' => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter( 
			$container -> get( 'security.role_hierarchy' )
		);
	},
	
	/*
		- 
	*/
	'security.authentication_provider' => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager(
			[ $container -> get( 'security.database.user_provider' ) ]
		);
	},
	
	/*
		- 
	*/
	'security.access_decision' => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\Security\Core\Authorization\AccessDecisionManager(
			[ $container -> get( 'security.role_hierarchy_voter' ) ],
		);
	},
	
	/*
		- Symfony > security > isGranted
	*/
	'security.authorization_checker' => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\Security\Core\Authorization\AuthorizationChecker(
			$container -> get( 'security.token_storage' ), 
			$container -> get( 'security.authentication_provider' ),
			$container -> get( 'security.access_decision' ),
		);
	},
	
];
