<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation\Http;

use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Web\Foundation\User\UserInterface;

class BaseController
{
	protected UserInterface $model;
	
	public function __construct ( protected App $app )
	{
		$this -> model = $app -> make( str_replace ( \Controller :: class, \Model :: class, static :: class ), [ $app ] );
	}
	
	public function title( array $title, bool $replace = false ): void
	{
		if ( $replace )
		{
			$this -> app -> view -> title -> reset( 'list', $title );
			
			return;
		}
		
		$this -> app -> view -> title -> add( 'list', $title, true );
	}
	
	public function render( string $content = '', string | null $layout = null ): array
	{
		return $this -> app -> view -> render( $this -> model, $content, $layout );
	}
	
	public function redirect( string $path ): array
	{
		return $this -> app -> view -> redirect( $path );
	}
	
	public function header( string ...$head )
	{
		$this -> app -> view -> head -> add( 'selected', $head );
	}
	
	// json ?
}
