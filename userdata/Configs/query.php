<?php

use Nouvu\Web\Components\Database\DatabaseRequestInterface;

$prefix_ = $app -> repository -> get( 'database.prefix' );

return [
	/*
		- 
	*/
	'database' => [
		'select' => [
			/*
				- 
			*/
			'users_username|email' => static function ( string $name ) use ( $app, $prefix_ ): DatabaseRequestInterface
			{
				return $this -> app -> database -> prepare( 
					"SELECT 
						`id`, `username`, `email`, `password`, `roles`, `create_at`
					FROM 
						{$prefix_}users 
					WHERE 
						`username` = :name OR `email` = :name", 
					[
						'name' => $name
					]
				);
			},
			
			/*
				- 
			*/
		],
		'insert' => [],
		'update' => [],
	],
];