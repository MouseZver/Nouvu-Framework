<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http\Controllers;

use Nouvu\Web\Components\Config\Repository;

class InputController extends Repository
{
	public function __construct ( array $params )
	{
		parent :: __construct ();
		
		$this -> reset ( 'name', $params['_route'] );
		
		$this -> reset ( 'controller', $params['_controller'][0] );
		
		$this -> reset ( 'action', $params['_controller'][1] );
		
		unset ( $params['_route'], $params['_controller'] );
		
		$this -> reset ( 'arguments', $params );
	}
	
	public function getRouteName(): string
	{
		return $this -> get( 'name' );
	}
	
	public function getControllerName(): string
	{
		return $this -> get( 'controller' );
	}
	
	public function getActionName(): string
	{
		return $this -> get( 'action' );
	}
	
	public function getArguments(): array
	{
		return $this -> get( 'arguments' );
	}
}