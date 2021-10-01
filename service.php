<?php

use Nouvu\Web\Foundation\Application;

$load = require 'vendor/autoload.php';

$load -> addPsr4( 'Nouvu\\Web\\', __DIR__ . '/Nouvu' );

return static function ( string $name ) use ( $load ): Application
{
	$load -> addPsr4( 'Nouvu\\Resources\\', __DIR__ . "/{$name}/Resources" );
	
	return new Application( new \Nouvu\Container, include $name . '/tools.php' );
};