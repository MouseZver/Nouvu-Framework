<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View\Repository;

use Nouvu\Web\Component\Config\Repository;
use Nouvu\Web\View\Builder\BuilderHtml;

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
			yield new BuilderHtml( $this -> get( 'list.' . $tag ) );
		}
	}
}