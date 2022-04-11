<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Routing;

use Nouvu\Framework\Routing\RouteCollection;

trait CollectionTrait
{
	public function collection(): RouteCollection
	{
		return $this -> app -> container -> make( RouteCollection :: class );
	}
}