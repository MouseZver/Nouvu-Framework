<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View;

trait Get
{
	public function getContent(): string
	{
		return $this -> content;
	}
	
	public function getTitle(): string
	{
		return implode ( $this -> title -> get( 'delimiter' ), $this -> title -> get( 'list' ) );
	}
	
	public function getHead( string ...$heads )
	{
		if ( func_num_args () > 0 )
		{
			$this -> head -> add( 'selected', func_get_args (), true );
		}
		
		return implode ( PHP_EOL . "\t", iterator_to_array ( $this -> builderHeads() ) );
	}
	
	public function getHtml( string $name ): string
	{
		ob_start ();
		
		//require $this -> app -> path( 'view' ) . "{$this -> theme}/{$this -> layout}.php";
		//( $this -> include )( $this -> directory . $name );
		
		$this -> model -> closure()( $name );
		
		return $this -> replaceCode( $name, ob_get_clean () );
	}
}
