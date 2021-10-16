<?php

use \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use \Nouvu\Resources\Entity\User;

return [
	'encoder' => static function (): array
	{
		$defaultEncoder = new MessageDigestPasswordEncoder( 'sha512', true, 4000 );
		
		//$weakEncoder = new MessageDigestPasswordEncoder( 'md5', true, 1 );
		
		return [
			User :: class => $defaultEncoder,
		];
	},
	'remember_me' => [
		'path' => '/',
		'name' => 'MyRememberMeCookie',
		'domain' => null,
		'secure' => false,
		'httponly' => true,
		'lifetime' => 1209600, // 14 days
		'always_remember_me' => true,
		'remember_me_parameter' => '_remember_me'
	],
	'session_name' => '_security_token',
];