<?php

use Nouvu\Web\Foundation\Application;

$load = require 'vendor/autoload.php';

return static function ( string $name ) use ( $load ): Application
{
	$load -> addPsr4( 'Nouvu\\Resources\\', __DIR__ . "/{$name}/Resources" );
	
	return new Application( new \Nouvu\Container\Container, include $name . '/tools.php' );
};
