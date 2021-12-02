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
						`id`, `username`, `email`, `password`, `roles`, `email_confirmed`, `create_at`
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
						`id`, `value`
					FROM 
						{$prefix_}rememberme_token 
					WHERE 
						`series` = ?", 
					[
						$series
					]
				);
			},
			
			/*
				- 
			*/
			'lost-password' => static function ( string $confirm ) use ( $app ): DatabaseRequestInterface
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				return $app -> database -> prepare( 
					"SELECT *
					FROM 
						{$prefix_}forgot_pass 
					WHERE 
						`confirm` = ?", 
					[
						$confirm
					]
				);
			},
			
			/*
				- 
			*/
			'confirm-account' => static function ( string $confirm ) use ( $app ): DatabaseRequestInterface
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				return $app -> database -> prepare( 
					"SELECT *
					FROM 
						{$prefix_}users_pre 
					WHERE 
						`confirm` = ?", 
					[
						$confirm
					]
				);
			},
		],
		'insert' => [
			/*
				- Подтверждение регистрации нового пользователя
			*/
			'users_pre_register' => static function ( UserInterface $user, string $confirm ) use ( $app ): int
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$result = $app -> database -> prepare( 
					"INSERT INTO {$prefix_}users_pre 
						( `confirm`, `email` )
					VALUES
						( ?,? )",
					[ 
						$confirm,
						$user -> getEmail()
					]
				);
				
				return $result -> id();
			},
			
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
			
			/*
				- Добавить запись "Запомнить меня"
			*/
			'forgot-password' => static function ( UserInterface $user, string $confirm ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> prepare( 
					"INSERT INTO {$prefix_}forgot_pass 
						( `user_id`, `email`, `confirm` )
					VALUES
						( ?,?,? )",
					[
						$user -> getId(),
						$user -> getEmail(),
						$confirm
					]
				);
			}
		],
		'update' => [
			/*
				- Выбрать токен по серийному номеру
			*/
			'token_lastUsed' => static function ( int $id ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> query( [ "UPDATE {$prefix_}rememberme_token SET `lastUsed` = NOW() WHERE `id` = %d", $id ] );
			},
			
			/*
				- Обновить пароль пользователю
			*/
			'user_password' => static function ( UserInterface $user ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> prepare( "UPDATE {$prefix_}users SET `password` = ? WHERE `id` = ?", [
					$user -> getPassword(),
					$user -> getId(),
				] );
			},
			
			/*
				- Активировать аккаунт пользователю
			*/
			'email_confirmed' => static function ( UserInterface $user ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> prepare( "UPDATE {$prefix_}users SET `email_confirmed` = 1 WHERE `id` = ?", [
					$user -> getId()
				] );
			},
		],
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
			'token' => static function ( string $series ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> prepare( 
					"DELETE FROM {$prefix_}rememberme_token WHERE `series` = ?",
					[
						$series
					]
				);
			},
			
			/*
				- Удаление всех записей по восстановлению пароля у пользователя
			*/
			'forgot-password' => static function ( UserInterface $user ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> prepare( 
					"DELETE FROM {$prefix_}forgot_pass WHERE `user_id` = ?",
					[
						$user -> getId()
					]
				);
			},
			
			/*
				- Удаление одной выбранной записи по восстановлению пароля
			*/
			'lost-password' => static function ( int $id ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> query( [ "DELETE FROM {$prefix_}forgot_pass WHERE `id` = %d", $id ] );
			},
			
			/*
				- Удаление одной выбранной записи по восстановлению пароля
			*/
			'confirm-account' => static function ( int $id ) use ( $app ): void
			{
				$prefix_ = $app -> repository -> get( 'database.prefix' );
				
				$app -> database -> query( [ "DELETE FROM {$prefix_}users_pre WHERE `id` = %d", $id ] );
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