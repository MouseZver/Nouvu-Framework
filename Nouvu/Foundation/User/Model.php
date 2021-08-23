<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation\User;

use Closure;
use Nouvu\Web\Foundation\Application AS App;

class Model implements UserInterface
{
	public function __construct ( protected App $app )
	{
		
	}
	
	public function closure(): Closure
	{
		return $this -> app -> config -> get( 'viewer.include' );
	}
	
	public function getLocale(): string
	{
		return $this -> app -> getLocale();
	}
	
	public function getPage(): string
	{
		return $this -> app -> request -> attributes -> get( '_route' );
	}
	
	/* public function getTitle()
	{
		return $this -> app -> view -> getLocale();
	}
	
	public function getHead( string ...$heads )
	{
		return $this -> app -> view -> getHead( ...$heads );
	} */
}