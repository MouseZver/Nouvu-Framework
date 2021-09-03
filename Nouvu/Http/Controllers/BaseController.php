<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http\Controllers;

use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Web\Foundation\User\UserInterface;
use Nouvu\Web\View\Repository\Commit AS CommitRepository;

class BaseController
{
	protected UserInterface $model;
	
	public function __construct ( protected App $app )
	{
		$this -> model = $app -> make( str_replace ( \Controller :: class, \Model :: class, static :: class ), [ $app ] );
		//$this -> model = $app -> make( \Model :: class, [ $app ] );
	}
	
	public function isAjax(): bool
	{
		return $this -> app -> request -> isXmlHttpRequest();
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
	
	public function header( string ...$head ): void
	{
		$this -> app -> view -> head -> add( 'selected', $head );
	}
	
	private function getCommitInstance( array $data ): CommitRepository
	{
		$data['model'] = $this -> model;
		
		return new CommitRepository( $data );
	}
	
	public function render( string $content = '', string | null $layout = null ): CommitRepository
	{
		$commit = $this -> getCommitInstance( compact ( 'content', 'layout' ) );
		
		$this -> app -> view -> render( $commit );
		
		return $commit;
	}
	
	public function redirect( string $path ): CommitRepository
	{
		$commit = $this -> getCommitInstance( compact ( 'path' ) );
		
		$this -> app -> view -> redirect( $commit );
		
		return $commit;
	}
	
	public function json( string $content = '', string | null $layout = null ): CommitRepository
	{
		$commit = $this -> getCommitInstance( compact ( 'content', 'layout' ) );
		
		$this -> app -> view -> json( $commit );
		
		return $commit;
	}
}
