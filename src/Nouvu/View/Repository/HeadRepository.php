<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\View\Repository;

use Nouvu\Framework\Component\Config\Repository;
use Nouvu\Resources\System\BuilderHtml\Builder;

final class HeadRepository extends Repository
{
	public function getSelected(): array
	{
		return $this -> get( 'selected' );
	}
	
	public function getList(): array
	{
		return $this -> get( 'list' );
	}
	
	public function getResult(): \Iterator
	{
		foreach ( $this -> getSelected() AS $tag )
		{
			if ( ! $this -> has( 'list.' . $tag ) )
			{
				throw new \InvalidArgumentException( 'HeadRepository - not found HeadTag: ' . $tag );
			}
			
			yield new Builder( $this -> get( 'list.' . $tag ) );
		}
	}
}