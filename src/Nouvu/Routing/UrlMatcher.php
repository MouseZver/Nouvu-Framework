<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Routing;

use Nouvu\Framework\Routing\RouteCollection AS NouvuCollection;
use Nouvu\Framework\Routing\RequestContext AS NouvuContext;
use Symfony\Component\Routing\Matcher\UrlMatcher AS Matcher;

class UrlMatcher
{
	private Matcher $matcher;
	
	public function __construct ( NouvuCollection $collection, NouvuContext $context )
	{
		$this -> matcher = new Matcher( $collection -> get(), $context -> get() );
	}
	
	public function match( string $path ): array
	{
		return $this -> matcher -> match( $path );
	}
	
	public function get(): Matcher
	{
		return $this -> matcher;
	}
}
