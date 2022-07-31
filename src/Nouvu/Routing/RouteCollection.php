<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Routing;

use Symfony\Component\Routing\RouteCollection AS SymfonyCollection;
use Symfony\Component\Routing\Route;

class RouteCollection
{
	private SymfonyCollection $collection;
	
	private array $argumentsRoute = [
		'path',
		'defaults',
		'requirements',
		'options',
		'host',
		'schemes',
		'methods',
		'condition',
	];
	
	public function __construct ()
	{
		$this -> collection = new SymfonyCollection;
	}
	
	/*
		- https://symfony.com/doc/current/routing.html
		
		$route = new Route(
			'/archive/{month}', // path
			-- [ '_controller' => 'showArchive' ], // default values
			[ 'month' => '[0-9]{4}-[0-9]{2}', 'subdomain' => 'www|m' ], // requirements
			[], // options
			'{subdomain}.example.com', // host
			[], // schemes
			[] // methods
		);
	*/
	public function add( string | int $name, array $route )
	{
		$intersect = array_intersect_key ( $route, array_flip ( $this -> argumentsRoute ) );
		
		$this -> collection -> add( ( string ) $name, new Route( ...$intersect ), $route['priority'] );
	}
	
	public function get(): SymfonyCollection
	{
		return $this -> collection;
	}
}
