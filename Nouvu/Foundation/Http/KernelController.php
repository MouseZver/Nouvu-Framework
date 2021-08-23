<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation\Http;

use Nouvu\Web\Foundation\Application AS App;

class KernelController
{
	private InputController $InputController;
	
	private static BaseController $BaseController;
	
	public function __construct ( private App $app )
	{
		$this -> InputController = new InputController( $app -> request -> attributes -> all() );
	}
	
	public function getController( string $name ): self
	{
		self :: $BaseController ??= new ( $name . '\\' . $this -> InputController -> getControllerName() . 'Controller' )( $this -> app );
		
		return $this;
	}
	
	public function action(): array
	{
		$method = new \ReflectionMethod( self :: $BaseController, $this -> InputController -> getActionName() );
		
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
		
		return self :: $BaseController -> {$this -> InputController -> getActionName()}( ...$arguments );
	}
}
