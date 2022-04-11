<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\{ Security, User\UserInterface };
use Nouvu\Framework\Foundation\Application;
use Nouvu\Framework\View\Repository\CommitRepository;
use Nouvu\Resources\Controllers\AbstractController AS UserController;
use Nouvu\Resources\Models\AbstractModel;
use Nouvu\Resources\System\RestApi;

class AbstractController extends UserController
{
	public function __construct ( protected Application $app )
	{}
	
	protected function model( string $namespace ): AbstractModel
	{
		$name = sprintf ( \Nouvu\Resources\Models :: class . '\\%sModel', $namespace );
		
		if ( $this -> app -> repository -> has( 'app.system.listModels.' . $namespace ) )
		{
			return $this -> app -> container -> get( $name );
		}
		
		$this -> app -> repository -> set( 'app.system.listModels.' . $namespace, $name );
		
		return $this -> app -> container -> make( $name );
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
	
	protected function setThreadTitles( string ...$titles ): void
	{
		$this -> app -> view -> title -> add( 'list', $titles, true );
	}
	
	protected function setSingleTitles( string ...$titles ): void
	{
		$this -> app -> view -> title -> set( 'list', $titles );
	}
	
	protected function header( string ...$heades ): void
	{
		$this -> app -> view -> head -> add( 'selected', $heades );
	}
	
	protected function render( string $content = '', string | null $layout = null, array $arguments = [], string $body = 'body' ): CommitRepository
	{
		if ( $this -> app -> request -> isXmlHttpRequest() )
		{
			return $this -> json( $content, body: $body );
		}
		
		$commit = $this -> getCommitInstance( compact ( 'content', 'layout', 'arguments' ) );
		
		$this -> app -> view -> render( $commit );
		
		return $commit;
	}
	
	protected function redirect( string $path ): CommitRepository
	{
		if ( $this -> app -> request -> isXmlHttpRequest() )
		{
			return $this -> customJson( RestApi :: success() -> redirect( path: $path ) );
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
	
	protected function getPasswordHasher( UserInterface $user ): PasswordHasherInterface
	{
		/*$memory = $this -> app -> container -> get( 'security.memory.user_provider' );
		
		$memory -> createUser( $user );*/
		
		return $this -> app -> container -> get( 'encoder.factory' ) -> getPasswordHasher( $user );
		//	-> getPasswordHasher( $memory -> loadUserByIdentifier( $user -> getUserIdentifier() ) );
	}
}
