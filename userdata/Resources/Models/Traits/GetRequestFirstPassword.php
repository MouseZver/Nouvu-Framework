<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Traits;

trait GetRequestFirstPassword
{
	public function getFirstPassword(): string
	{
		return $this -> app -> request -> request -> get( 'password', [] )['first'] ?? '';
	}
}
