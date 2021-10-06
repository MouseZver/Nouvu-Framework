<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http;

use Symfony\Component\Routing\Exception\ResourceNotFoundException AS SymfonyRoutingNotFound;
use Symfony\Component\HttpFoundation\Response;
use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Web\Foundation\Table;
use Nouvu\Web\Http\Controllers\KernelController;
use Nouvu\Web\Routing\RouteCollection AS NouvuCollection;
use Nouvu\Web\Routing\RequestContext AS NouvuContext;
use Nouvu\Web\Routing\UrlMatcher AS NouvuMatcher;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Resources\Controllers;

class Kernel
{
	use Table\CollectAble;
	use Table\ContextAble;
	
	public function __construct ( protected App $app )
	{
		$app -> init();
		
		foreach ( $app -> config -> get( 'app.middlewareSystem' ) AS $name )
		{
			$app -> {$name};/*  = fn(): $class => new $class( $app ); */
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
		$this -> app -> response -> setCharset( $this -> app -> getCharset() );
	}
	
	public function getCommit(): CommitRepository
	{
		$KernelController = new KernelController( $this -> app ); // ??????????????????????????????????????????????????????
		
		return $KernelController -> getController( Controllers :: class ) -> action();
	}
	
	public function terminal( CommitRepository $commit ): void
	{
		$this -> app -> view -> terminal( $commit );
	}
	
	public function handle( NouvuMatcher $NouvuMatcher ): CommitRepository
	{
		$this -> setCharset();
		
		try
		{
			$this -> setRequestAttributes( $this -> getAttributes( $NouvuMatcher ) );
			
			return $this -> getCommit();
		}
		catch ( SymfonyRoutingNotFound )
		{
			$this -> setRequestAttributes( $this -> getAttributesNotFound( $NouvuMatcher ) );
			
			return $this -> getCommit();
		}
		catch ( \Throwable )
		{
			$this -> setRequestAttributes( $this -> getAttributesError( $NouvuMatcher ) );
			
			return $this -> getCommit();
		}
	}
	
	public function send()
	{
		$context = $this -> context();
		
		$context -> copyRequest( $this -> app -> request );
		
		$matcher = $this -> getMatcher( $this -> collection(), $context );
		
		$this -> terminal( $this -> handle( $matcher ) );
	}
}