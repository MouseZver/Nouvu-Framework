<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Traits;

trait GetRequestSecondPassword
{
	public function getSecondPassword(): string
	{
		return $this -> app -> request -> request -> get( 'password', [] )['second'] ?? '';
	}
}
