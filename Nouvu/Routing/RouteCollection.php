<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Routing;

use Symfony\Component\Routing\RouteCollection AS SymfonyCollection;
use Symfony\Component\Routing\Route;

class RouteCollection
{
	private SymfonyCollection $collection;
	
	public function __construct ()
	{
		$this -> collection = new SymfonyCollection;
	}
	
	/*
		- https://symfony.com/doc/4.3/components/routing.html
	*/
	public function add( string | int $name, array $route )
	{
		$this -> collection -> add( ( string ) $name, new Route( ...array_values ( $route ) ) );
	}
	
	public function get(): SymfonyCollection
	{
		return $this -> collection;
	}
}
