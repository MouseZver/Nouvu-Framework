<?php

use Nouvu\Web\Components\Database\DatabaseRequestInterface;

return [
	/*
		- 
	*/
	'database' => [
		'select' => [
			/*
				- 
			*/
			'users_username|email' => static function ( string $name ) use ( $app ): DatabaseRequestInterface
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				return $app -> database -> prepare( 
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
		'delete' => [],
		'file' => [],
		'create' => [],
		'alter' => [],
		'index' => [],
		'drop' => [],
		'CreateTemporaryTables' => [],
		'LockTables' => [],
	],
];