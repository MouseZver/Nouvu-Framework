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
];