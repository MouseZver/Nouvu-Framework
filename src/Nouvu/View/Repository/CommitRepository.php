<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View\Repository;

use Nouvu\Web\Component\Config\Repository;

final class CommitRepository extends Repository
{
	public function replace( string $from, string $to ): void
	{
		$value = $this -> get( $from );
		
		$this -> delete( $from );
		
		$this -> reset( $to, $value );
	}
	
	public function getCommit(): string
	{
		return $this -> get( 'commit' );
	}
	
	public function getContainer(): mixed
	{
		return $this -> get( 'container' );
	}
}