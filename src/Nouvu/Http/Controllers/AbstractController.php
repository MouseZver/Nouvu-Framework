<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Nouvu\Web\Foundation\{ Application, ApplicationTrait };
use Nouvu\Web\View\Repository\CommitRepository;

class AbstractController
{
	use ApplicationTrait;
	
	public function __construct ( protected Application $app )
	{
		/* $model = str_replace ( \Controller :: class, \Model :: class, static :: class );
		
		$this -> app -> request -> attributes -> set( '_model', $model ); */
	}
	
	protected function getModel( string $string )
	{
		$transformedSegments = array_map ( fn( string $a ) => ucfirst ( strtolower ( $a ) ), explode ( '.', $string ) );
		
		$name = sprintf ( 'Nouvu\\Resources\\Models\\%sModel', implode ( '\\', $transformedSegments ) );
		
		$this -> app -> repository -> add( 'app.system.listModels', [ $name ] );
		
		return $this -> make( $name, [ $this -> app ] );
		
		//return $this -> make( $this -> app -> request -> attributes -> get( '_model.' . $name ), [ $this -> app ] );
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
			$this -> app -> view -> title -> reset( 'list', $title );
			
			return;
		}
		
		$this -> app -> view -> title -> add( 'list', $title, true );
	}
	
	protected function header( string ...$head ): void
	{
		$this -> app -> view -> head -> add( 'selected', $head );
	}
	
	protected function render( string $content = '', string | null $layout = null ): CommitRepository
	{
		if ( $this -> isAjax() )
		{
			return $this -> json( $content );
		}
		
		$commit = $this -> getCommitInstance( compact ( 'content', 'layout' ) );
		
		$this -> app -> view -> render( $commit );
		
		return $commit;
	}
	
	protected function redirect( string $path ): CommitRepository
	{
		if ( $this -> isAjax() )
		{
			return $this -> customJson( [ 
				'response' => 'redirect',
				'path' => $path
			] );
		}
		
		$commit = $this -> getCommitInstance( compact ( 'path' ) );
		
		$this -> app -> view -> redirect( $commit );
		
		return $commit;
	}
	
	protected function json( string $content = '', string | null $layout = null ): CommitRepository
	{
		$commit = $this -> getCommitInstance( compact ( 'content', 'layout' ) );
		
		$this -> app -> view -> json( $commit );
		
		return $commit;
	}
	
	protected function customJson( array $content ): CommitRepository
	{
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
	
	protected function getPost(): array
	{
		return $this -> app -> request -> request -> all();
	}
	
	protected function getEncoder( UserInterface $user ): PasswordEncoderInterface
	{
		return $this -> app -> container -> get( 'encoder.factory' ) -> getEncoder( $user );
	}
}
