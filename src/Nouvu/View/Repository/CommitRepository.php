<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\View\Repository;

use Nouvu\Framework\Component\Config\Repository;

final class CommitRepository extends Repository
{
	public function replace( string $from, string $to ): void
	{
		$value = $this -> get( $from );
		
		$this -> remove( $from );
		
		$this -> set( $to, $value );
	}
	
	public function getCommit(): string
	{
		return $this -> get( 'commit' );
	}
	
	public function getContainer(): mixed
	{
		return $this -> get( 'container' );
	}
	
	public function getBody(): string
	{
		return $this -> get( 'body' );
	}
	
	public function getArguments(): array
	{
		$arguments = [];
		
		foreach ( $this -> get( 'arguments', [] ) AS $k => $v )
		{
			$arguments['{{' . trim ( $k, '{}' ) . '}}'] = $v;
		}
		
		return $arguments;
	}
}