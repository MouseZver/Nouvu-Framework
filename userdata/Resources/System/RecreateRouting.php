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
		'TestError' => [
			'route' => [
				'path' => '/error',
				'controller' => [ '_controller' => [ 'Main', 'testError' ] ],
			]
		],
		'Welcome' => [
			'route' => [
				'path' => '/welcome',
				'controller' => [ '_controller' => [ 'Main', 'welcome' ] ],
			]
		],
		'Главная' => [
			'route' => [
				'path' => '/',
				'controller' => [ '_controller' => [ 'Main', 'index' ] ],
			]
		],
		'Регистрация' => [
			'route' => [
				'path' => '/registration',
				'controller' => [ '_controller' => [ 'Registration', 'register' ] ],
			]
		],
	];
	
	private static array $template = [
		'edit' => true,
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