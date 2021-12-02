<?php

return [
	/*
		- 
	*/
	'entity' => [
		'user' => function ( int $id, string $username, string $email, string $password, string $roles, int $email_confirmed, string $create_at ): void
		{
			$this -> id = $id;
			
			$this -> setUsername( $username );
			
			$this -> setEmail( $email );
			
			$this -> setPassword( $password );
			
			$this -> setRoles( json_decode ( $roles, true ) );
			
			$this -> setConfirmed( ( bool ) $email_confirmed );
			
			$this -> setCreatedAt( $create_at );
		},
	],
];