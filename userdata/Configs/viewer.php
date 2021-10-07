<?php

return [
	'head' => [
		'meta-charset' 		=> [ 'tag' => 'meta', 'data' => [ 'charset' => fn() => $app -> getCharset() ] ],
		'meta-viewport' 	=> [ 'tag' => 'meta', 'data' => [ 'name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no' ] ],
		/* 'js-jquery'		=> [ 'tag' => 'script', 'data' => [ 'src' => '/assets/js/jquery.min.js' ] ],
		'js-popper'		=> [ 'tag' => 'script', 'data' => [ 'src' => '/assets/js/popper.min.js' ] ],
		'js-bootstrap'		=> [ 'tag' => 'script', 'data' => [ 'src' => '/assets/js/bootstrap.min.js' ] ], */
		/* '0000' => [ 'tag' => '00000', 'data' => [ '0000' => '00000', '0000' => '00000' ] ],
		'0000' => [ 'tag' => '00000', 'data' => [ '0000' => '00000', '0000' => '00000' ] ], */
	],
	
	/*
		- 
	*/
	'extension' => '.php',
	
	/*
		- Замыкание
		- Подключение шаблона с использованием $app внутри.
	*/
	'include' => function ( string $template ) use ( $app )
	{
		include $template . $app -> repository -> get( 'viewer.extension' );
	},
];