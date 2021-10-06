<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View\Repository;

use Nouvu\Web\Components\Config\Repository;
use Nouvu\Web\View\Builder\CreateHtml;

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
			yield new CreateHtml( $this -> get( 'list.' . $tag ) );
		}
	}
}