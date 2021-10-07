<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nouvu\Web\View\Builder\Content;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Web\View\Repository\HeadRepository;
use Nouvu\Web\View\Repository\TitleRepository;
use Nouvu\Web\Component\Config\Repository;

final class Viewer
{
	public HeadRepository $head;
	public TitleRepository $title;
	
	private string $directory;
	private string | null $layout = null;
	private string $extension = '.php';
	
	public function __construct ( private Request $request, private Response $response )
	{
		$this -> head = new HeadRepository( [ 'list' => [], 'selected' => [] ] );
		
		$this -> title = new TitleRepository( [ 'list' => [], 'delimiter' => ' - ' ] );
	}
	
	public function setPath( Repository $repository ): void
	{
		$path = $repository -> get( 'app.system.directory.view' ) . $repository -> get( 'config.theme' );
		
		$this -> directory = rtrim ( $path, '\\/' ) . DIRECTORY_SEPARATOR;
	}
	
	public function setLayout( Repository $repository ): void
	{
		$this -> layout = $repository -> get( 'config.default_template' );
	}
	
	public function setExtension( Repository $repository ): void
	{
		$this -> extension = $repository -> get( 'viewer.extension' );
	}
	
	public function setHead( Repository $repository ): void
	{
		$this -> head -> add( 'list', $repository -> get( 'viewer.head' ) );
	}
	
	public function setTitle( Repository $repository ): void
	{
		$this -> title -> set( $repository -> get( 'config.default_title' ) );
	}
	
	public function render( CommitRepository $commit ): void
	{
		$commit -> get( 'layout' ) ?? $commit -> reset ( 'layout', $this -> layout );
		
		$commit -> reset ( 'commit', 'render' );
		
		$commit -> replace( 'content', 'container.content' );
		
		$commit -> replace( 'layout', 'container.layout' );
	}
	
	public function redirect( CommitRepository $commit ): void
	{
		$commit -> reset ( 'commit', 'redirect' );
		
		$commit -> replace( 'path', 'container' );
	}
	
	public function json( CommitRepository $commit ): void
	{
		$this -> render( $commit );
		
		$commit -> reset ( 'commit', 'json' );
	}
	
	public function terminal( CommitRepository $commit ): void
	{
		$commit -> set( array_filter ( $commit -> all() ) );
		
		foreach ( [ 'directory', 'layout', 'head', 'title', 'extension' ] AS $name )
		{
			$commit -> reset ( $name, $this -> {$name} );
		}
		
		$this -> send( $commit, new Terminal( $commit ) );
	}
	
	private function send( CommitRepository $commit, Terminal $terminal ): void
	{
		match ( $commit -> getCommit() )
		{
			'render'	=> $terminal -> contentResponse( $this -> response, new Content( $commit ) ),
			'redirect'	=> $terminal -> redirectResponse( $this -> response ),
			'json'		=> $terminal -> jsonResponse( $this -> response, new Content( $commit ) ),
		};
		
		$this -> response -> prepare( $this -> request );
		
		$this -> response -> send();
	}
}