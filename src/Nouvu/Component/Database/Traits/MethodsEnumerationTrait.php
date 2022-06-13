<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Database\Traits;

trait MethodsEnumerationTrait
{
	private function cell( string $action, string $name ): \Closure
	{
		if ( $this -> app -> repository -> has( "query.database.{$action}.{$name}" ) )
		{
			return $this -> app -> repository -> get( "query.database.{$action}.{$name}" );
		}
		
		throw new \BadFunctionCallException( '@' . static :: class . ": Not Found callable pack query.database.( action: {$action} | name: {$name} )" );
	}
	
	public function select( string $namespace ): \Closure
	{
		return $this -> cell( __FUNCTION__, $namespace );
	}
	
	public function insert( string $namespace ): \Closure
	{
		return $this -> cell( __FUNCTION__, $namespace );
	}
	
	public function update( string $namespace ): \Closure
	{
		return $this -> cell( __FUNCTION__, $namespace );
	}
	
	public function delete( string $namespace ): \Closure
	{
		return $this -> cell( __FUNCTION__, $namespace );
	}
	
	public function create( string $namespace ): \Closure
	{
		return $this -> cell( __FUNCTION__, $namespace );
	}
	
	public function drop( string $namespace ): \Closure
	{
		return $this -> cell( __FUNCTION__, $namespace );
	}
}