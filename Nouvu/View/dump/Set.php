<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View;

trait Set
{
	public function setPath( string $path )
	{
		 $this -> directory = rtrim ( $path, '\\/' ) . DIRECTORY_SEPARATOR;
	}
	
	public function setLayout( string | null $layout )
	{
		$this -> layout = $layout;
	}
	/* ?????????????????????????????
	public function setTitle( array $title, string | null $delimiter = null )
	{
		$this -> title -> list = $title;
		
		$this -> title -> delimiter = $delimiter ?? $this -> title -> delimiter;
	} */
	
	protected function setContent( callable | null $call = null ): void
	{
		foreach ( array_filter ( $this -> container ) AS $name )
		{
			$this -> content = $this -> getHtml( $this -> directory . $name );
		}
		
		if ( is_callable ( $call ) )
		{
			$this -> response -> setContent( $call( $this -> getContent() ) );
			
			return;
		}
		
		$this -> response -> setContent( $this -> getContent() );
	}
	/* ??????????????????????????????????????????
	public function setHead( array $head ): void
	{
		$this -> head -> list = $head;
	} */
}
