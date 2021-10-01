<?php

return [
	/*
		- 
	*/
	'ini_set' => [
		'date.timezone' 			=> fn() => $app -> config -> get( 'config.timezone.host' ) ?: '',
		'error_reporting' 			=> fn() => ( $app -> config -> get( 'config.debug.error' ) ? E_ALL : 0 ),
		'html_errors'				=> fn() => ( int ) $app -> config -> get( 'config.debug.html' ),
		'log_errors' 				=> fn() => ( int ) $app -> config -> get( 'config.debug.error' ),
		'log_errors_max_len' 		=> fn() => $app -> config -> get( 'config.debug.log_errors_max_len' ),
		'ignore_repeated_errors' 	=> fn() => ( int ) $app -> config -> get( 'config.debug.ignore_repeated_errors' ),
		'ignore_repeated_source' 	=> fn() => ( int ) $app -> config -> get( 'config.debug.ignore_repeated_source' ),
		'error_log' 				=> fn() => $app -> path( 'userdata' ) . sprintf ( $app -> config -> get( 'config.debug.error_log' ), date ( 'Y-m-d' ) ),
		'display_errors' 			=> fn() => ( $app -> config -> get( 'config.debug.display' ) ? 'on' : 'off' ),
		'display_startup_errors' 	=> fn() => ( int ) $app -> config -> get( 'config.debug.display' ),
		'default_charset' 			=> fn() => $app -> config -> get( 'config.default_charset' ),
	],
	
	/*
		- 
	*/
	'middlewareSystem' => [
		\Session :: class,
		//\Auth :: class,
		\Router :: class,
	],
	
	/*
		- Данный атрибут используется системой
	*/
	'system' => [],
];

