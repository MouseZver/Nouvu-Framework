<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View\Builder;

use Closure;
use Iterator;
use Stringable;

class CreateHtml implements Stringable
{
	public array $noEnd = [
		'meta', 'link', 'img', 'input', 
	];
	
	public array $slashEnd = [
	
	];
	
	public array $solo = [
		'br', 'hr',
	];
	
	public function __construct ( private array $data ) {}
	
	//[ 'tag' => 'meta', 'data' => [ 'charset' => fn() => $app -> getCharset() ] ]
	protected function compile( array | string $data ): string
	{
		if ( is_string ( $data ) )
		{
			return $data;
		}
		
		if ( isset ( $data[0] ) )
		{
			/* $closure = function ( array $rows ): Iterator
			{
				foreach ( $rows AS $row )
				{
					yield $this -> compile( $row );
				}
			};
			
			return implode ( ' ', iterator_to_array ( $closure( $data ) ) ); */
			
			$array = array_map ( fn( array | string $row ): string => $this -> compile( $row ), $data );
			
			return implode ( ' ', $array );
		}
		
		$data = ( object ) $data;
		
		$content = '';
		
		if ( isset ( $data -> content ) )
		{
			if ( $data -> content Instanceof Closure )
			{
				$data -> content = ( $data -> content )();
			}
			
			$content = $this -> compile( $data -> content );
			
			unset ( $data -> content );
		}
		
		$template = match( true )
		{
			in_array ( $data -> tag, $this -> noEnd ) => "<{$data -> tag} %s>",
			in_array ( $data -> tag, $this -> slashEnd ) => "<{$data -> tag} %s />",
			in_array ( $data -> tag, $this -> solo ) => "<{$data -> tag}>",
			default => "<{$data -> tag} %s>%s</{$data -> tag}>",
		};
		
		$atributes = implode ( ' ', iterator_to_array ( $this -> atributes( $data -> data ) ) );
		
		return sprintf ( $template, $atributes, $content );
	}
	
	protected function atributes( array $args ): Iterator
	{
		foreach ( $args AS $name => $value )
		{
			yield sprintf ( '%s = "%s"', $name, ( $value Instanceof Closure ? $value() : $value ) );
		}
	}
	
	public function __toString(): string
	{
		return $this -> compile( $this -> data );
	}
}
