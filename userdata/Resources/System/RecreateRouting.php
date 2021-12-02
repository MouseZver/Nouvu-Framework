<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\System;

final class RecreateRouting
{
	private static array $data = [
		404 => [
			'edit' => false,
		],
		500 => [
			'edit' => false,
			'route' => [
				'path' => '/error/500',
				'controller' => [ '_controller' => [ 'Main', 'err500' ] ],
			],
		],
		'test_error' => [
			'route' => [
				'path' => '/error',
				'controller' => [ '_controller' => [ 'Main', 'testError' ] ],
			]
		],
		'welcome' => [
			'route' => [
				'path' => '/welcome',
				'controller' => [ '_controller' => [ 'Main', 'welcome' ] ],
			]
		],
		'index' => [
			'route' => [
				'path' => '/',
				'controller' => [ '_controller' => [ 'Main', 'index' ] ],
			]
		],
		'register' => [
			'route' => [
				'path' => '/registration',
				'controller' => [ '_controller' => [ 'Registration', 'register' ] ],
				'methods' => [ 'GET', 'POST' ],
			]
		],
		'account-activation' => [
			'route' => [
				'path' => '/confirm-account',
				'controller' => [ '_controller' => [ 'Registration', 'confirm' ] ],
				'methods' => [ 'GET' ],
			]
		],
		'login' => [
			'route' => [
				'path' => '/login',
				'controller' => [ '_controller' => [ 'Auth', 'login' ] ],
				'methods' => [ 'GET', 'POST' ],
			]
		],
		'logout' => [
			'route' => [
				'path' => '/logout',
				'controller' => [ '_controller' => [ 'Auth', 'logout' ] ],
			]
		],
		'forgot-password' => [
			'route' => [
				'path' => '/forgot-password',
				'controller' => [ '_controller' => [ 'Auth', 'forgotPassword' ] ],
				'methods' => [ 'GET', 'POST' ],
			]
		],
		'lost-password' => [
			'route' => [
				'path' => '/lost-password',
				'controller' => [ '_controller' => [ 'Auth', 'lostPassword' ] ],
				'methods' => [ 'GET', 'POST' ],
			]
		],
	];
	
	private static array $template = [
		'edit' => true,
		'active' => true,
		'route' => [
			'path' => '/error/404',
			'controller' => [ '_controller' => [ 'Main', 'err404' ] ],
			'requirements' => [],
			'options' => [],
			'host' => '',
			'schemes' => [],
			'methods' => [ 'GET' ],
			'condition' => '',
		]
	];
	
	protected static function map(): \Iterator
	{
		foreach ( self :: $data AS $name => $route )
		{
			yield $name => array_replace_recursive ( self :: $template, $route );
		}
	}
	
	public static function create( string $file ): array
	{
		$array = iterator_to_array ( self :: map() );
		
		file_put_contents ( $file, json_encode ( $array, 480 ) );
		
		return $array;
	}
}