<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View;

use Symfony\Component\HttpFoundation\Response;
use Nouvu\Web\View\Builder\Content;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Resources\System\RestApi;

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
			return json_encode ( RestApi :: success() 
				-> header( action: 'update', container: $this -> commit -> getBody() ) 
				-> data( title: $build -> getTitle(), content: $content ) 
				-> get(), 
				JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE 
			);
		} );
	}
	
	public function customResponse( Response $response ): void
	{
		$closure = $this -> commit -> get( 'closure' );
		
		$closure( $response );
	}
}