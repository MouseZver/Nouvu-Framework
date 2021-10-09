<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Routing;

use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Web\Routing\RouteCollection AS NouvuCollection;
use Nouvu\Web\Routing\RequestContext AS NouvuContext;
use Nouvu\Web\Routing\UrlMatcher AS NouvuMatcher;

class Router
{
	private array $routing;
	
	use CollectionTrait;
	use ContextTrait;
	
	public function __construct ( private App $app )
	{
		$this -> routing = $app -> repository -> get( 'router.closure' )( $app );
		
		foreach ( $this -> routing AS $name => [ 'edit' => $edit, 'route' => $route ] )
		{
			$this -> collection() -> add( $name, $route );
		}
	}
	
	public function getPathInfo(): string
	{
		return $this -> app -> request -> getPathInfo();
	}
	
	public function matcher( NouvuCollection $collection, NouvuContext $context ): NouvuMatcher
	{
		return $this -> app -> make( NouvuMatcher :: class, [ $collection, $context ] );
	}
	
	public function getAttributes( NouvuMatcher $matcher ): array
	{
		return $matcher -> match( $this -> getPathInfo() );
	}
	
	public function getAttributesNotFound(): array
	{
		return $this -> getRouting( $this -> app -> response :: HTTP_NOT_FOUND ); // error404 $this -> app -> response :: HTTP_NOT_FOUND
	}
	
	public function getAttributesError(): array
	{
		return $this -> getRouting( $this -> app -> response :: HTTP_INTERNAL_SERVER_ERROR ); // error500 $this -> app -> response :: HTTP_INTERNAL_SERVER_ERROR
	}
	
	public function getRouting( string | int $name ): array
	{
		// return array_merge ( Arr :: get( $name . '.route.controller', $this -> routing ), [ '_route' => $name ] );
		
		return array_merge ( $this -> routing[$name]['route']['controller'], [ '_route' => $name ] );
	}
}
