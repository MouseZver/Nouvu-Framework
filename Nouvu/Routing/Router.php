<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Routing;

use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Web\Foundation\Table;
use Nouvu\Web\Routing\RouteCollection AS NouvuCollection;
use Nouvu\Web\Routing\RequestContext AS NouvuContext;
use Nouvu\Web\Routing\UrlMatcher AS NouvuMatcher;

class Router
{
	private array $routing;
	
	use Table\CollectAble;
	use Table\ContextAble;
	
	public function __construct ( private App $app )
	{
		$this -> routing = $app -> config -> get( 'router.closure' )( $app );
		
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
		return $this -> getRouting( 'error404' );
	}
	
	public function getAttributesError(): array
	{
		return $this -> getRouting( 'error500' );
	}
	
	public function getRouting( string | int $name ): array
	{
		return array_merge ( $this -> routing[$name]['route']['controller'], [ '_route' => $name ] );
	}
}
