<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Traits;

trait GetRequestPassword
{
	public function getPassword(): string
	{
		return $this -> app -> request -> request -> get( 'password', '' );
	}
}
