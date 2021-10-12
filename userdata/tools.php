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
	\Kernel :: class => static function ( ContainerInterface $container ): \Nouvu\Web\Http\Kernel
	{
		return new \Nouvu\Web\Http\Kernel( $container -> get( \App :: class ) );
	},
	
	/*
		- 
	*/
	\Request :: class => static function ( ContainerInterface $container ): \Symfony\Component\HttpFoundation\Request
	{
		return \Symfony\Component\HttpFoundation\Request :: createFromGlobals();
	},
	
	/*
		- 
	*/
	\Response :: class => static function ( ContainerInterface $container ): \Symfony\Component\HttpFoundation\Response
	{
		return new \Symfony\Component\HttpFoundation\Response;
	},
	
	/*
		- 
	*/
	\Database :: class => static function ( ContainerInterface $container ): \Nouvu\Web\Component\Database\DatabaseManager
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
	\Router :: class => static function ( ContainerInterface $container ): \Nouvu\Web\Routing\Router
	{
		return new \Nouvu\Web\Routing\Router( $container -> get( \App :: class ) );
	},
	
	/*
		-
	*/
	\Session :: class => static function ( ContainerInterface $container ): \Symfony\Component\HttpFoundation\Session\Session
	{
		$session = new \Symfony\Component\HttpFoundation\Session\Session;
		
		$session -> start();
		
		$container -> get( \Request :: class ) -> setSession( $session );
		
		return $session;
	},
	
	/*
		- 
	*/
	\View :: class => static function ( ContainerInterface $container ): \Nouvu\Web\View\Viewer
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
	\Validator :: class => static function ( ContainerInterface $container ): \Symfony\Component\Validator\Validator\ValidatorInterface
	{
		return \Symfony\Component\Validator\Validation :: createValidator();
	},
	
	/*
		- Без getFormFactory();
	*/
	'form.factory' => static function ( ContainerInterface $container ): \Symfony\Component\Form\FormFactoryBuilderInterface
	{
		return \Symfony\Component\Form\Forms :: createFormFactoryBuilder();
	},
	
	
	/*
		- 
	*/
	'encoder.factory' => static function ( ContainerInterface $container ): \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
	{
		$closure = $container -> get( \Repository :: class ) -> get( 'security.encoder' );
		
		return new \Symfony\Component\Security\Core\Encoder\EncoderFactory( $closure() );
	},
	
// -----------------------------------------------------------------------------------------------------------------------------
	/*
		- 
	*/
	\Security :: class => static function ( ContainerInterface $container ): \Symfony\Component\Security\Core\Security
	{
		return new \Symfony\Component\Security\Core\Security( $container );
	},
	
	/*
		- Symfony > security > isGranted
	*/
	'security.authorization_checker' => static function ( ContainerInterface $container ): \Symfony\Component\Security\Core\Authorization
	{
		return new \Symfony\Component\Security\Core\Authorization\AuthorizationChecker(
			$container -> make( \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage :: class ), 
			
		);
	},
	
	/*
		- Symfony > security > getToken
	*/
	'security.token_storage' => static function ( ContainerInterface $container ): ContainerInterface
	{
		//return $container -> get( 0000000000 );
	},
	
	// make
	/* \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage :: class => static function ( ContainerInterface $container ): \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
	{
		return \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
	}, */
	
	/* \Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager :: class => static function ( ContainerInterface $container ): \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface
	{
		return \Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager
	} */
];
