<?php

use Nouvu\Web\Component\Database\DatabaseRequestInterface;
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
			'users_username|email' => static function ( string $username, string $email ) use ( $app ): DatabaseRequestInterface
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				return $app -> database -> prepare( 
					"SELECT 
						`id`, `username`, `email`, `password`, `roles`, `create_at`
					FROM 
						{$prefix_}users 
					WHERE 
						`username` = ? OR `email` = ?", 
					[
						$username,
						$email
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
			'users_register' => static function ( UserInterface $user ) use ( $app ): int
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$result = $app -> database -> prepare( 
					"INSERT INTO {$prefix_}users 
						( `username`, `email`, `password`, `roles` )
					VALUES
						( ?,?,?,? )",
					[ 
						$user -> getUsername(), 
						$user -> getEmail(), 
						$user -> getPassword(), 
						json_encode ( $user -> getRoles() )
					]
				);
				
				return $result -> id();
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