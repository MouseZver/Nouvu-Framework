<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation\Table;

use Nouvu\Web\Routing\RequestContext AS NouvuContext;

trait ContextAble
{
	public function context(): NouvuContext
	{
		return $this -> app -> make( NouvuContext :: class );
	}
}