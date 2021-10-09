<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models;

use Nouvu\Web\Foundation\Application AS App;

class AbstractModel //implements ???...
{
	public function __construct ( protected App $app )
	{
		
	}
	
	// getPage -> getRoute
	public function getRoute(): string
	{
		return $this -> app -> request -> attributes -> get( '_route' );
	}
	
	// to controller
	/* public function __invoke()
	{
		return $this -> app -> repository -> get( 'viewer.include' );
	}
	
	// to controller
	public function getLocale(): string
	{
		return $this -> app -> getLocale();
	}
	
	// to abstract model
	// getRoute ?
	public function getPage(): string
	{
		return $this -> app -> request -> attributes -> get( '_route' );
	} */
}