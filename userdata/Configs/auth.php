<?php

return [
	/*
		- 
	*/
	'entity' => [
		'user' => function ( int $id, string $username, string $email, string $password, string $roles = '[]', string $create_at = null ): void
		{
			$this -> id = $id;
			
			$this -> username = $username;
			
			$this -> email = $email;
			
			$this -> password = $password;
			
			$this -> roles = json_decode ( $roles, true );
			
			$this -> create_at = $create_at;
		},
	],
];