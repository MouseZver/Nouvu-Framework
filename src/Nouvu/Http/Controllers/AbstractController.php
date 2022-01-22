<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Nouvu\Web\Foundation\Application;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Resources\Controllers\AbstractController AS UserController;
use Nouvu\Resources\System\RestApi;

class AbstractController extends UserController
{
	public function __construct ( protected Application $app )
	{}
	
	protected function getModel( string $string )
	{
		$transformedSegments = array_map ( fn( string $a ) => ucfirst ( strtolower ( $a ) ), explode ( '.', $string ) );
		
		$name = sprintf ( 'Nouvu\\Resources\\Models\\%sModel', implode ( '\\', $transformedSegments ) );
		
		$this -> app -> repository -> add( 'app.system.listModels', [ $name ] );
		
		return $this -> make( $name/* , [ $this -> app ] */ );
	}
	
	private function getCommitInstance( array $data ): CommitRepository
	{
		foreach ( array_unique ( $this -> app -> repository -> get( 'app.system.listModels', [] ) ) AS $name )
		{
			$data['models'][] = $this -> app -> container -> get( $name );
		}
		
		$data['controller'] = $this;
		
		return new CommitRepository( $data );
	}
	
	protected function isAjax(): bool
	{
		return $this -> app -> request -> isXmlHttpRequest();
	}
	
	protected function title( array $title, bool $replace = false ): void
	{
		if ( $replace )
		{
			$this -> app -> view -> title -> set( 'list', $title );
			
			return;
		}
		
		$this -> app -> view -> title -> add( 'list', $title, true );
	}
	
	protected function header( string ...$head ): void
	{
		$this -> app -> view -> head -> add( 'selected', $head );
	}
	
	protected function render( string $content = '', string | null $layout = null, array $arguments = [], string $body = 'body' ): CommitRepository
	{
		if ( $this -> isAjax() )
		{
			return $this -> json( $content, body: $body );
		}
		
		$commit = $this -> getCommitInstance( compact ( 'content', 'layout', 'arguments' ) );
		
		$this -> app -> view -> render( $commit );
		
		return $commit;
	}
	
	protected function redirect( string $path ): CommitRepository
	{
		if ( $this -> isAjax() )
		{
			return $this -> customJson( RestApi :: success() -> header( action: 'redirect', path: $path ) );
		}
		
		$commit = $this -> getCommitInstance( compact ( 'path' ) );
		
		$this -> app -> view -> redirect( $commit );
		
		return $commit;
	}
	
	protected function json( string $content = '', array $arguments = [], string $body = 'body' ): CommitRepository
	{
		$commit = $this -> getCommitInstance( compact ( 'content', 'arguments', 'body' ) );
		
		$this -> app -> view -> json( $commit );
		
		return $commit;
	}
	
	protected function customJson( array | RestApi $content ): CommitRepository
	{
		if ( $content instanceOf RestApi )
		{
			$content = $content -> get();
		}
		
		$commit = $this -> getCommitInstance( [ 'closure' => function ( Response $response ) use ( $content ): void
		{
			$response -> headers -> set( 'Content-Type', 'application/json' );
			
			$response -> setContent( json_encode ( $content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
		} ] );
		
		$this -> app -> view -> custom( $commit );
		
		return $commit;
	}
	
	public function __invoke()
	{
		return $this -> app -> repository -> get( 'viewer.include' );
	}
	
	protected function getEncoder( UserInterface $user ): PasswordEncoderInterface
	{
		return $this -> app -> container -> get( 'encoder.factory' ) -> getEncoder( $user );
	}
}
