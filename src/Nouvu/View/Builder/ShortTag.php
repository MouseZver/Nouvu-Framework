<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\View\Builder;

use Nouvu\Resources\System\Helpers;
use Closure;
use Iterator;
use Stringable;

class ShortTag implements Stringable
{
	//protected string $regex = '#{<{([\w\-\/]+)(=([\w\|\/_-]+)|)}>}#';
	
	protected array $regex = [
		// {<{LastUsername}>}
		// {<{@LastUsername}>}
		'#{<{(\w+)}>}#',
		'#{<{@(\w+)}>}#', 
		
		// {<{head=meta-charset|meta-viewport}>}
		// {<{include=admin-panel/blocks/menu}>}
		// {<{@pathByName=admin-panel/blocks/menu}>}
		'#{<{(\w+)=([\w\|\-\/]+)}>}#',
		'#{<{@(\w+)=([\w\|\-\/]+)}>}#', 
		
		// {<{if(Validator)}>}
		// {<{end(validator)}>}
		//'#{<{(?<action>(if))\(([A-z]+)\)}>}#', 
		
		// {<{@function}>}
	];
	
	public function __construct ( 
		protected array $comparison, 
		protected Closure | null $call = null, 
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
				if ( /* ! is_null ( $class ) &&  */method_exists ( $class, $method ) )
				{
					return $class -> {$method}( ...$this -> atributes( $matches[2] ?? null ) ); // $matches[3]
				}
			}
		}
		
		if ( function_exists ( Helpers :: class . '\\' . $matches[1] ) )
		{
			return ( Helpers :: class . '\\' . $matches[1] )( ...$this -> atributes( $matches[2] ?? null ) );
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
