<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation;

use Nouvu\Web\Routing\RouteCollection;

trait CollectionTrait
{
	public function collection(): RouteCollection
	{
		return $this -> app -> make( RouteCollection :: class );
	}
}