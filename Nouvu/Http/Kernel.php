<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http;

use Symfony\Component\Routing\Exception\ResourceNotFoundException AS SymfonyRoutingNotFound;
use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Web\Foundation\Table;
use Nouvu\Web\Foundation\Http\KernelController;
use Nouvu\Web\Routing\RouteCollection AS NouvuCollection;
use Nouvu\Web\Routing\RequestContext AS NouvuContext;
use Nouvu\Web\Routing\UrlMatcher AS NouvuMatcher;
use Nouvu\Resources\Controllers;

class Kernel
{
	use Table\CollectAble;
	use Table\ContextAble;
	
	public function __construct ( protected App $app )
	{
		$app -> init();
		
		foreach ( $app -> config -> get( 'app.middlewareSystem' ) AS $name => $class )
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
		return $this -> app -> router -> getAttributes( $NouvuMatcher );
	}
	
	public function getAttributesNotFound( NouvuMatcher $NouvuMatcher ): array
	{
		return $this -> app -> router -> getAttributesNotFound( $NouvuMatcher );
	}
	
	public function getAttributesError( NouvuMatcher $NouvuMatcher ): array
	{
		return $this -> app -> router -> getAttributesError( $NouvuMatcher );
	}
	
	public function setRequestAttributes( array $args ): void
	{
		$this -> app -> request -> attributes -> add( $args );
	}
	
	public function getCommand(): array
	{
		$KernelController = new KernelController( $this -> app );
		
		return $KernelController -> getController( Controllers :: class ) -> action();
	}
	
	public function terminal( array $command ): void
	{
		$this -> app -> view -> terminal( $command );
	}
	
	public function handle( NouvuMatcher $NouvuMatcher ): array
	{
		try
		{
			$this -> setRequestAttributes( $this -> getAttributes( $NouvuMatcher ) );
			
			return $this -> getCommand();
		}
		catch ( SymfonyRoutingNotFound )
		{
			$this -> setRequestAttributes( $this -> getAttributesNotFound( $NouvuMatcher ) );
			
			return $this -> getCommand();
		}
		catch ( \Throwable )
		{
			$this -> setRequestAttributes( $this -> getAttributesError( $NouvuMatcher ) );
			
			return $this -> getCommand();
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
