<?php

use \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use \Nouvu\Resources\Entity\User;

return [
	/*
		- Кодировщик Пароля
	*/
	'encoder' => static function (): array
	{
		$defaultEncoder = new MessageDigestPasswordEncoder( 'sha512', true, 4000 );
		
		//$weakEncoder = new MessageDigestPasswordEncoder( 'md5', true, 1 );
		
		return [
			User :: class => $defaultEncoder,
		];
	},
	
	/*
		- куки ( запомнить меня )
	*/
	'remember_me' => [
		'path' => '/',
		'name' => 'nouvu_user',
		'domain' => null,
		'secure' => false,
		'httponly' => true,
		'lifetime' => strtotime ( '30 days', 0 ),
		'always_remember_me' => true,
		'remember_me_parameter' => '_remember_me'
	],
	
	/*
		- имя сессии авторизованного пользователя
	*/
	'session_name' => '_security_token',
	
	/*
		- контроль доступа
	*/
	'hierarchy' => [
		'ROLE_SUPER_ADMIN' => [ 'ROLE_ADMIN', 'ROLE_USER' ],
		'ROLE_ADMIN' => [ 'ROLE_USER' ],
	],
	
	/*
		- 
	*/
	'secret_key' => 'secret_string',
];