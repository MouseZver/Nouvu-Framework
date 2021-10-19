<?php

use Nouvu\Web\Component\Database\DatabaseRequestInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

return [
	/*
		- 
	*/
	'database' => [
		'select' => [
			/*
				- Поиск пользователя
			*/
			'users_username|email' => static function ( string $username = null, string $email = null ) use ( $app ): DatabaseRequestInterface
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				return $app -> database -> prepare( 
					"SELECT 
						`id`, `username`, `email`, `password`, `roles`, `create_at`
					FROM 
						{$prefix_}users 
					WHERE 
						`username` IN( :name, :email ) OR `email` = :email", 
					[
						'name' => $username,
						'email' => $email,
					]
				);
			},
			
			/*
				- Выбрать токен по серийному номеру
			*/
			'token' => static function ( string $series ) use ( $app ): DatabaseRequestInterface
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				return $app -> database -> prepare( 
					"SELECT 
						`value`
					FROM 
						{$prefix_}rememberme_token 
					WHERE 
						`series` = ?", 
					[
						$series
					]
				);
			},
		],
		'insert' => [
			/*
				- Регистрация нового пользователя
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
			
			/*
				- Добавить запись "Запомнить меня"
			*/
			'token' => static function ( TokenInterface $token, string $name ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> prepare( 
					"INSERT INTO {$prefix_}rememberme_token 
						( `series`, `value`, `lastUsed`, `user_id` )
					VALUES
						( ?,?,NOW(),? )",
					[
						$name,
						serialize ( $token ),
						$token -> getUser() -> getId()
					]
				);
			},
		],
		'update' => [],
		'delete' => [
			/*
				- Удаление просроченной записи "Запомнить меня"
			*/
			'clearing_expired_tokens' => static function ( string $username = null, string $email = null ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> query( "DELETE FROM {$prefix_}rememberme_token WHERE `lastUsed` < NOW() - INTERVAL 15 DAY" );
			},
			
			/*
				- Удаление записи "Запомнить меня" 
			*/
			'token' => static function ( TokenInterface $token ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> prepare( 
					"DELETE FROM {$prefix_}rememberme_token WHERE `id` = ?",
					[
						$token -> getUser() -> getId()
					]
				);
			},
		],
		'file' => [],
		'create' => [],
		'alter' => [],
		'index' => [],
		'drop' => [],
		'CreateTemporaryTables' => [],
		'LockTables' => [],
	],
];