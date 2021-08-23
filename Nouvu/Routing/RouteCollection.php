<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Routing;

use \Symfony\Component\Routing\RouteCollection AS Collection;
use \Symfony\Component\Routing\Route;

class RouteCollection
{
	private Collection $collection;
	
	public function __construct ()
	{
		$this -> collection = new Collection;
	}
	
	/*
		- https://symfony.com/doc/4.3/components/routing.html
	*/
	public function add( string $name, array $route )
	{
		$this -> collection -> add( $name, new Route( ...array_values ( $route ) ) );
	}
	
	public function get(): Collection
	{
		return $this -> collection;
	}
}
