<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View;

use Symfony\Component\HttpFoundation\Response;
use Nouvu\Web\View\Builder\Content;
use Nouvu\Web\View\Repository\CommitRepository;

class Terminal
{
	public function __construct ( private CommitRepository $commit )
	{
		// ...
	}
	
	public function contentResponse( Response $response, Content $build ): void
	{
		$response -> headers -> set( 'Content-Type', 'text/html' );
		
		$build -> setContent( $response );
	}
	
	public function redirectResponse( Response $response ): void
	{
		$response -> setStatusCode( Response :: HTTP_MOVED_PERMANENTLY );
		
		$response -> headers -> set( 'Location', $this -> commit -> getContainer() );
	}
	
	public function jsonResponse( Response $response, Content $build ): void
	{
		$response -> headers -> set( 'Content-Type', 'application/json' );
		
		$build -> setContent( $response, function ( string $content ) use ( $build ): string
		{
			$title = $build -> getTitle();
			
			// JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ??
			return json_encode ( compact ( 'content', 'title' ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		} );
	}
}