<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Routing;

use Symfony\Component\Routing\RequestContext AS SymfonyContext;
use Symfony\Component\HttpFoundation\Request;

class RequestContext
{
	private SymfonyContext $context;
	
	public function __construct ()
	{
		$this -> context = new SymfonyContext;
	}
	
	public function copyRequest( Request $request )
	{
		$this -> context -> fromRequest( $request );
	}
	
	public function get(): SymfonyContext
	{
		return $this -> context;
	}
}
