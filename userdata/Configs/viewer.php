<?php

return [
	'head' => [
		'meta-charset' 		=> [ 'tag' => 'meta', 'data' => [ 'charset' => fn() => $app -> getCharset() ] ],
		'meta-viewport' 	=> [ 'tag' => 'meta', 'data' => [ 'name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no' ] ],
		'js-jquery'			=> [ 'tag' => 'script', 'data' => [ 'src' => '/assets/js/jquery.min.js' ] ],
		'js-popper'			=> [ 'tag' => 'script', 'data' => [ 'src' => '/assets/js/popper.min.js' ] ],
		'js-bootstrap'		=> [ 'tag' => 'script', 'data' => [ 'src' => '/assets/js/bootstrap.min.js' ] ],
		'js-nouvu'			=> [ 'tag' => 'script', 'data' => [ 'src' => fn() => $app -> addFilemtime( '/assets/js/nouvu.js' ) ] ],
		'js-pace' 			=> [ 'tag' => 'script', 'data' => [ 'src' => '/assets/js/pace.min.js' ] ],
		
		
		'css-vectormap' 	=> [ 'tag' => 'link', 'data' => [ 'rel' => 'stylesheet', 'href' => fn() => $app -> addFilemtime( '/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css' ) ] ],
		'css-simplebar' 	=> [ 'tag' => 'link', 'data' => [ 'rel' => 'stylesheet', 'href' => fn() => $app -> addFilemtime( '/assets/plugins/simplebar/css/simplebar.css' ) ] ],
		'css-perfect-scrollbar' => [ 'tag' => 'link', 'data' => [ 'rel' => 'stylesheet', 'href' => fn() => $app -> addFilemtime( '/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css' ) ] ],
		'css-metismenu' 	=> [ 'tag' => 'link', 'data' => [ 'rel' => 'stylesheet', 'href' => fn() => $app -> addFilemtime( '/assets/plugins/metismenu/css/metisMenu.min.css' ) ] ],
		'css-pace' 			=> [ 'tag' => 'link', 'data' => [ 'rel' => 'stylesheet', 'href' => fn() => $app -> addFilemtime( '/assets/css/pace.min.css' ) ] ],
		'css-bootstrap' 	=> [ 'tag' => 'link', 'data' => [ 'rel' => 'stylesheet', 'href' => fn() => $app -> addFilemtime( '/assets/css/bootstrap.min.css' ) ] ],
		'css-icons' 		=> [ 'tag' => 'link', 'data' => [ 'rel' => 'stylesheet', 'href' => fn() => $app -> addFilemtime( '/assets/css/icons.css' ) ] ],
		'css-app' 			=> [ 'tag' => 'link', 'data' => [ 'rel' => 'stylesheet', 'href' => fn() => $app -> addFilemtime( '/assets/css/app.css' ) ] ],
		
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