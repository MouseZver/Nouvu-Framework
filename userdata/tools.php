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
	\Repository :: class => static function ( ContainerInterface $container ): \Nouvu\Web\Component\Config\Repository
	{
		$config = [];
		
		// use to each config
		$app = $container -> get( \App :: class );
		
		foreach ( glob ( dirname ( __FILE__ ) . '/Configs/*.php' ) AS $file )
		{
			$config[basename ( $file, '.php' )] = include $file;
		}
		
		$config['app']['system']['directory'] = [
			'userdata' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR,
			'root' => dirname ( __DIR__ ) . DIRECTORY_SEPARATOR,
		];
		
		$userdata = $config['app']['system']['directory']['userdata'];
		
		$config['app']['system']['directory']['view'] = $userdata . 'Resources/View/';
		
		return new \Nouvu\Web\Component\Config\Repository( $config, '.' );
	},
	
	/*
		- 
	*/
	\Kernel :: class => static function ( ContainerInterface $container )
	{
		return new \Nouvu\Web\Http\Kernel( $container -> get( \App :: class ) );
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
		$database = new \Nouvu\Web\Component\Database\DatabaseManager( $container -> get( \App :: class ) );
		
		$zone = $container -> get( \Repository :: class ) -> get( 'timezone.database' );
		
		if ( ! empty ( $zone ) )
		{
			$database -> query( [ 'SET time_zone = "%s"', $zone ] );
		}
		
		return $database;
	},
	
	/*
		- 
	*/
	\Router :: class => static function ( ContainerInterface $container )
	{
		return new \Nouvu\Web\Routing\Router( $container -> get( \App :: class ) );
	},
	
	/*
		-
	*/
	\Session :: class => static function ( ContainerInterface $container )
	{
		$session = new \Symfony\Component\HttpFoundation\Session\Session;
		
		$session -> start();
		
		$container -> get( \Request :: class ) -> setSession( $session );
		
		return $session;
	},
	
	/*
		- 
	*/
	\View :: class => static function ( ContainerInterface $container )
	{
		$app = $container -> get( \App :: class );
		
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
		return \Symfony\Component\Validator\Validation :: createValidator();
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
	\Remember :: class => static function ( ContainerInterface $container )
	{
		$app = $container -> get( \App :: class );
		
		$remember = $app -> session -> get( $app -> repository -> get( 'security.session_name' ) );
		
		if ( ! is_null ( $remember ) )
		{
			$app -> container -> get( 'security.token_storage' ) -> setToken( unserialize ( $remember ) );
		}
	},
	
	/*
		- Без getFormFactory();
	*/
	/* 'form.factory' => static function ( ContainerInterface $container )
	{
		return \Symfony\Component\Form\Forms :: createFormFactoryBuilder();
	}, */
	
	/*
		- 
	*/
	'security.database.user_provider' => static function ( ContainerInterface $container )
	{
		return new \Nouvu\Web\Component\Security\Core\User\DatabaseUserProvider( $container -> get( \App :: class ) );
	},
	
	/*
		- 
	*/
	'encoder.factory' => static function ( ContainerInterface $container )
	{
		$closure = $container -> get( \Repository :: class ) -> get( 'security.encoder' );
		
		return new \Symfony\Component\Security\Core\Encoder\EncoderFactory( $closure() );
	},
	
// -----------------------------------------------------------------------------------------------------------------------------
	
	/*
		- event_dispatcher
	*/
	/* 'event_dispatcher' => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\EventDispatcher\EventDispatcher;
	}, */
	
	/*
		- token_storage
	*/
	'security.token_storage' => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage(
			new \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage,
			$container
		);
	},
	
	/*
		- AuthenticationUtils
	*/
	'security.authentication_utils' => static function ( ContainerInterface $container )
	{
		return new \Symfony\Component\Security\Http\Authentication\AuthenticationUtils( $container -> get( 'request_stack' ) );
	},
	
	/*
		- RequestStack
	*/
	'request_stack' => static function ( ContainerInterface $container )
	{
		$requestStack = new \Symfony\Component\HttpFoundation\RequestStack();
		
		$requestStack -> push( $container -> get( \Request :: class ) );
		
		return $requestStack;
	},
	
	
	
	/*
		- Symfony > security > isGranted
	*/
	'security.authorization_checker' => static function ( ContainerInterface $container )
	{
		$hierarchy = [
			'ROLE_SUPER_ADMIN' => [ 'ROLE_ADMIN', 'ROLE_USER' ],
		];
		
		$roleHierarchy = new \Symfony\Component\Security\Core\Role\RoleHierarchy($hierarchy);
		
		$roleHierarchyVoter = new \Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter($roleHierarchy);
		
		return new \Symfony\Component\Security\Core\Authorization\AuthorizationChecker(
			$container -> get( 'security.token_storage' ), 
			new \Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager(
				[ $container -> get( 'security.database.user_provider' ) ]
			),
			new \Symfony\Component\Security\Core\Authorization\AccessDecisionManager(
				[ $roleHierarchyVoter ],
			)
		);
	},
	
];
