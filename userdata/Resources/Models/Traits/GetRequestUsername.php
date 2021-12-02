<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Traits;

trait GetRequestUsername
{
	public function getUsername(): string
	{
		return $this -> app -> request -> request -> get( 'username', '' );
	}
}
