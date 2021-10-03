<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View\Builder;

use Closure;
use Iterator;

class ShortTag
{
	protected string $regex = '#{<{([\w\-\/]+)(=([\w\|_-]+)|)}>}#';
	
	public function __construct ( 
		protected array $comparison, 
		protected \Closure | null $call = null, 
		protected string $content = ''
	)
	{
		
	}
	
	protected function atributes( string | null $string ): array
	{
		if ( is_null ( $string ) )
		{
			return [];
		}
		
		return explode ( '|', $string );
	}
	
	protected function map( array $matches ): string
	{
		if ( is_bool ( strpos ( $matches[1], '/' ) ) )
		{
			if ( ! is_bool ( strpos ( $matches[1], '-' ) ) )
			{
				$explode = explode ( '-', $matches[1] );
			}
			
			$method = 'get';
			
			foreach ( $explode ?? [ $matches[1] ] AS $string )
			{
				$method .= ucfirst ( strtolower ( $string ) );
			}
			
			foreach ( $this -> comparison AS $class )
			{
				if ( method_exists ( $class, $method ) )
				{
					return $class -> {$method}( ...$this -> atributes( $matches[3] ?? null ) );
				}
			}
		}
		
		if ( is_callable ( $this -> call ) )
		{
			return ( $this -> call )( $matches );
		}
		
		return "<!-- Not found ({$matches[1]}) -->";
	}
	
	public function __toString(): string
	{
		return preg_replace_callback ( $this -> regex, [ $this, 'map' ], $this -> content );
	}
}
