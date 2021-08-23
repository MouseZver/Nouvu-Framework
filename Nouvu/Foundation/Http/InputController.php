<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation\Http;

class InputController
{
	private $input;
	
	public function __construct ( array $params )
	{
		$this -> input = new \stdClass;
		
		$this -> input -> name = $params['_route'];
		
		$this -> input -> controller = $params['_controller'][0];
		
		$this -> input -> action = $params['_controller'][1];
		
		unset ( $params['_route'], $params['_controller'] );
		
		$this -> input -> arguments = $params;
	}
	
	public function getRouteName(): string
	{
		return $this -> input -> name;
	}
	
	public function getControllerName(): string
	{
		return $this -> input -> controller;
	}
	
	public function getActionName(): string
	{
		return $this -> input -> action;
	}
	
	public function getArguments(): array
	{
		return $this -> input -> arguments;
	}
}