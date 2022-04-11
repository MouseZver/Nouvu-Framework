<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Routing;

use Nouvu\Framework\Routing\RequestContext;

trait ContextTrait
{
	public function context(): RequestContext
	{
		return $this -> app -> container -> make( RequestContext :: class );
	}
}