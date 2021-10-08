<?php

use Nouvu\Web\Components\Database\DatabaseRequestInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
		'insert' => [
			/*
				- 
			*/
			'users_register' => static function ( UserInterface $user ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> prepare( 
					"INSERT INTO {$prefix_}users 
						( `username`, `email`, `password`, `roles` )
					VALUES
						( ?,?,?,? )",
					[ 
						$user -> getUsername(), 
						$user -> getEmail(), 
						$user -> getPassword(), 
						$user -> getRoles() 
					]
				);
			},
		],
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