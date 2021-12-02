<?php

return [
	/*
		- 
	*/
	'ini_set' => [
		'date.timezone' 			=> fn() => $app -> repository -> get( 'config.timezone.host' ) ?: '',
		'error_reporting' 			=> fn() => ( $app -> repository -> get( 'config.debug.error' ) ? E_ALL : 0 ),
		'html_errors'				=> fn() => ( int ) $app -> repository -> get( 'config.debug.html' ),
		'log_errors' 				=> fn() => ( int ) $app -> repository -> get( 'config.debug.error' ),
		'log_errors_max_len' 		=> fn() => $app -> repository -> get( 'config.debug.log_errors_max_len' ),
		'ignore_repeated_errors' 	=> fn() => ( int ) $app -> repository -> get( 'config.debug.ignore_repeated_errors' ),
		'ignore_repeated_source' 	=> fn() => ( int ) $app -> repository -> get( 'config.debug.ignore_repeated_source' ),
		'error_log' 				=> fn() => $app -> path( 'userdata' ) . sprintf ( $app -> repository -> get( 'config.debug.error_log' ), date ( 'Y-m-d' ) ),
		'display_errors' 			=> fn() => ( $app -> repository -> get( 'config.debug.display' ) ? 'on' : 'off' ),
		'display_startup_errors' 	=> fn() => ( int ) $app -> repository -> get( 'config.debug.display' ),
		'default_charset' 			=> fn() => $app -> repository -> get( 'config.default_charset' ),
	],
	
	/*
		- Немедленная загрузка инструмента из списка tools.php
	*/
	'middlewareSystem' => [
		//'security.token_storage', // Немедленная идентификация пользователя
	],
	
	/*
		- Данный атрибут используется системой
	*/
	'system' => [],
	
	/*
		- Вставка метки времени по последнему изменению файла
		- example: /assets/css/app.css?11111111
	*/
	'addFilemtime' => static function ( string $file ) use ( $app ): string
	{
		if ( $app -> repository -> has( 'app.system.directory.site' ) && file_exists ( $app -> repository -> get( 'app.system.directory.site' ) . $file ) )
		{
			$file .= '?' . filemtime ( $app -> repository -> get( 'app.system.directory.site' ) . $file );
		}
		
		return $file;
	},
];

