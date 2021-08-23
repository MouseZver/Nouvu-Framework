<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Routing;

use Symfony\Component\Routing\RequestContext AS Context;
use Symfony\Component\HttpFoundation\Request;

class RequestContext
{
	private Context $context;
	
	public function __construct ()
	{
		$this -> context = new Context;
	}
	
	public function copyRequest( Request $request )
	{
		$this -> context -> fromRequest( $request );
	}
	
	public function get(): Context
	{
		return $this -> context;
	}
}
