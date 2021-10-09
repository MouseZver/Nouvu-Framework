<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Routing;

use Nouvu\Web\Routing\RequestContext

trait ContextTrait
{
	public function context(): RequestContext
	{
		return $this -> app -> make( RequestContext :: class );
	}
}