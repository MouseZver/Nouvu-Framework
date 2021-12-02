<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models\Traits;

trait GetQueryConfirm
{
	public function getConfirm(): string
	{
		return $this -> app -> request -> query -> get( 'confirm', '' );
	}
}
