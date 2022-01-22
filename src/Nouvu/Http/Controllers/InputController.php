<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http\Controllers;

use Nouvu\Web\Component\Config\Repository;

class InputController extends Repository
{
	public function __construct ( array $params )
	{
		parent :: __construct ();
		
		$this -> set ( 'name', $params['_route'] );
		
		$this -> set ( 'controller', $params['_controller'][0] );
		
		$this -> set ( 'action', $params['_controller'][1] );
		
		//unset ( $params['_route'], $params['_controller'] );
		
		$this -> set ( 'arguments', $params );
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
		return json_decode ( json_encode ( $this -> get( 'arguments' ), JSON_NUMERIC_CHECK ), true );
	}
}