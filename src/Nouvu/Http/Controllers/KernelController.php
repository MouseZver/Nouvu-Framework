<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Http\Controllers;

use Nouvu\Framework\Foundation\Application AS App;
use Nouvu\Framework\View\Repository\CommitRepository;

class KernelController
{
	private InputController $InputController;
	
	private AbstractController $AbstractController;
	
	public function __construct ( private App $app )
	{
		$this -> InputController = new InputController( $app -> request -> attributes -> all() );
	}
	
	public function getController( string $name ): self
	{
		$this -> AbstractController ??= new ( $name . '\\' . $this -> InputController -> getControllerName() . 'Controller' )( $this -> app );
		
		return $this;
	}
	
	public function action(): CommitRepository
	{
		$method = new \ReflectionMethod( $this -> AbstractController, $this -> InputController -> getActionName() );
		
		$names = array_column ( $method -> getParameters(), 'name' );
		
		$arguments = $this -> InputController -> getArguments();
		
		foreach ( $arguments AS $name => $value )
		{
			if ( ! in_array ( $name, $names ) )
			{
				/* error_log ( sprintf ( 'KernelController - Not found argument %s to method %s :: %s', $name, 
					$this -> InputController -> getControllerName(), 
					$this -> InputController -> getActionName()
				); */
				
				unset ( $arguments[$name] );
			}
		}
		
		return $this -> AbstractController -> {$this -> InputController -> getActionName()}( ...$arguments );
	}
}
