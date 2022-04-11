<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Routing;

use Nouvu\Framework\Foundation\Application AS App;
use Nouvu\Framework\Routing\RouteCollection AS NouvuCollection;
use Nouvu\Framework\Routing\RequestContext AS NouvuContext;
use Nouvu\Framework\Routing\UrlMatcher AS NouvuMatcher;
use Nouvu\Framework\Component\Config\Repository;

class Router
{
	private Repository $routing;
	
	use CollectionTrait;
	use ContextTrait;
	
	public function __construct ( private App $app )
	{
		$this -> routing = new Repository( $app -> repository -> get( 'router.closure' )( $app ) );
		
		foreach ( $this -> routing -> all() AS $name => [ 'active' => $active, 'route' => $route ] )
		{
			if ( $active )
			{
				$this -> collection() -> add( $name, $route );
			}
		}
	}
	
	public function getPathInfo(): string
	{
		return $this -> app -> request -> getPathInfo();
	}
	
	public function matcher( NouvuCollection $collection, NouvuContext $context ): NouvuMatcher
	{
		return $this -> app -> container -> make( NouvuMatcher :: class, [ $collection, $context ] );
	}
	
	public function getAttributes( NouvuMatcher $matcher ): array
	{
		return $matcher -> match( $this -> getPathInfo() );
	}
	
	public function getAttributesNotFound(): array
	{
		return $this -> getRoutingByName( $this -> app -> response :: HTTP_NOT_FOUND ); // error404 $this -> app -> response :: HTTP_NOT_FOUND
	}
	
	public function getAttributesError(): array
	{
		return $this -> getRoutingByName( $this -> app -> response :: HTTP_INTERNAL_SERVER_ERROR ); // error500 $this -> app -> response :: HTTP_INTERNAL_SERVER_ERROR
	}
	
	public function getRoutingByName( string | int $name ): array
	{
		// return array_merge ( Arr :: get( $name . '.route.controller', $this -> routing ), [ '_route' => $name ] );
		
		// return array_merge ( $this -> routing[$name]['route']['controller'], [ '_route' => $name ] );
		
		$this -> routing -> add( $name . '.route.controller', [ '_route' => $name ] );
		
		return $this -> routing -> get( $name . '.route.controller' );
	}
	
	public function getPathByName( string | int $name ): string | null
	{
		return $this -> routing -> get( $name . '.route.path', null );
	}
}
