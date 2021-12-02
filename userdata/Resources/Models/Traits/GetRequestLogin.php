<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Traits;

trait GetRequestLogin
{
	public function getLogin(): string
	{
		return $this -> app -> request -> request -> get( 'login', '' );
	}
}
