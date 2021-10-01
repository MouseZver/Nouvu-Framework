<?php

return [
	/*
		- Настройка Часового пояса. Список поддерживаемых временных зон:
		- https://www.php.net/manual/ru/timezones.php
		- Для использования default зоны, следует установить значение false
	*/
	'timezone' => [
		'host' => 'Europe/Moscow',
		'database' => 'Europe/Moscow',
	],
	
	/*
		- Обработка ошибок.
		- Настоятельно рекомендуется, на боевом сайте, изменить значение атрибута @display на false
	*/
	'debug' => [
		'error' => true,
		'display' => false,
		'error_log' => 'Logs/Debug-%s.log',
		'log_errors_max_len' => 0,
		'ignore_repeated_errors' => false,
		'ignore_repeated_source' => false,
		'html' => true,
	],
	
	/*
		- 
	*/
	'default_charset' => 'UTF-8',
	
	/*
		- Локализация
	*/
	'locale' => 'ru',
	
	/*
		- Стандартная тема View/{theme}
	*/
	'theme' => 'default',
	
	/*
		- Стандартный шаблон страниц
	*/
	'default_template' => 'default-template',
	
	'default_title' => [
		'list' => [ 'Nouvu' ],
		'delimiter' => ' | ',
	],
];
