<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View\Repository;

use Nouvu\Web\Components\Config\Repository;

final class Title extends Repository
{
	public function getDelimiter(): string
	{
		return $this -> get( 'delimiter' );
	}
	
	public function getList(): array
	{
		return $this -> get( 'list' );
	}
	
	public function getResult(): string
	{
		return implode ( $this -> getDelimiter(), $this -> getList() );
	}
}