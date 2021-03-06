<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\View\Repository;

use Nouvu\Framework\Component\Config\Repository;

final class TitleRepository extends Repository
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