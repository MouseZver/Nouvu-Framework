<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Http;

use Symfony\Component\Routing\Exception\ResourceNotFoundException AS SymfonyRoutingNotFound;
use Symfony\Component\HttpFoundation\Response;
use Nouvu\Framework\Foundation\Application AS App;
use Nouvu\Framework\Http\Controllers\KernelController;
use Nouvu\Framework\Routing\{ 
	RouteCollection AS NouvuCollection, RequestContext AS NouvuContext, UrlMatcher AS NouvuMatcher,
	CollectionTrait, ContextTrait
};
use Nouvu\Framework\View\{ Repository\CommitRepository, Builder\Content };
use Nouvu\Resources\Controllers;

class Kernel
{
	use CollectionTrait, ContextTrait;
	
	public function __construct ( protected App $app )
	{
		$app -> init();
		
		foreach ( $app -> repository -> get( 'app.middlewareSystem' ) AS $name )
		{
			$app -> container -> get( $name );
		}
	}
	
	public function getMatcher( NouvuCollection $NouvuCollection, NouvuContext $NouvuContext ): NouvuMatcher
	{
		return $this -> app -> router -> matcher( $NouvuCollection, $NouvuContext );
	}
	
	public function getAttributes( NouvuMatcher $NouvuMatcher ): array
	{
		$this -> app -> response -> setStatusCode( Response :: HTTP_OK );
		
		return $this -> app -> router -> getAttributes( $NouvuMatcher );
	}
	
	public function getAttributesNotFound( NouvuMatcher $NouvuMatcher ): array
	{
		$this -> app -> response -> setStatusCode( Response :: HTTP_NOT_FOUND );
		
		return $this -> app -> router -> getAttributesNotFound( $NouvuMatcher );
	}
	
	public function getAttributesError( NouvuMatcher $NouvuMatcher ): array
	{
		$this -> app -> response -> setStatusCode( Response :: HTTP_INTERNAL_SERVER_ERROR );
		
		return $this -> app -> router -> getAttributesError( $NouvuMatcher );
	}
	
	public function setRequestAttributes( array $args ): void
	{
		$this -> app -> request -> attributes -> add( $args );
	}
	
	public function setCharset(): void
	{
		$this -> app -> response -> setCharset( $this -> app -> repository -> get( 'config.default_charset' ) );
	}
	
	public function getCommit(): CommitRepository
	{
		$KernelController = new KernelController( $this -> app );
		
		return $KernelController -> getController( Controllers :: class ) -> action();
	}
	
	public function terminal( CommitRepository $commit ): void
	{
		if ( ! $this -> app -> container -> has( Content :: class ) )
		{
			$class = new class
			{
				private Content $content;
				
				public function set( CommitRepository $commit ): self
				{
					$this -> content = new Content( $commit );
					
					return $this;
				}
				
				public function get(): Content
				{
					return $this -> content;
				}
			};
			
			$this -> app -> container -> set( Content :: class, fn() => $class );
		}
		
		$this -> app -> view -> terminal( $commit, $this -> app -> container -> get( Content :: class ) -> set( $commit ) -> get() );
	}
	
	public function handle( NouvuMatcher $NouvuMatcher ): CommitRepository
	{
		$this -> setCharset();
		
		try
		{
			$this -> setRequestAttributes( $this -> getAttributes( $NouvuMatcher ) );
			
			return $this -> getCommit();
		}
		catch ( SymfonyRoutingNotFound $e )
		{
			error_log ( $e -> getMessage() );
			
			$this -> setRequestAttributes( $this -> getAttributesNotFound( $NouvuMatcher ) );
			
			return $this -> getCommit();
		}
	}
	
	public function send()
	{
		$context = $this -> context();
		
		$context -> copyRequest( $this -> app -> request );
		
		$matcher = $this -> getMatcher( $this -> collection(), $context );
		
		try
		{
			$this -> terminal( $this -> handle( $matcher ) );
		}
		catch ( \Throwable $e )
		{
			if ( $this -> app -> repository -> get( 'config.debug.display' ) )
			{
				throw $e;
			}
			
			$this -> setRequestAttributes( $this -> getAttributesError( $matcher ) );
			
			$this -> terminal( $this -> getCommit() );
		}
	}
}
