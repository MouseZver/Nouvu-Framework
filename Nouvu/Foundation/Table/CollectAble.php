<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation\Table;

use Nouvu\Web\Routing\RouteCollection AS NouvuCollection;

trait CollectAble
{
	public function collection(): NouvuCollection
	{
		return $this -> app -> make( NouvuCollection :: class );
	}
}