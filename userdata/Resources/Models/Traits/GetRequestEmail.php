<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Traits;

trait GetRequestEmail
{
	public function getEmail(): string
	{
		return $this -> app -> request -> request -> get( 'email', '' );
	}
}
