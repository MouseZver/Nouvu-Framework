<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\View;

use Symfony\Component\HttpFoundation\{ Request, Response };
use Nouvu\Framework\View\Builder\Content;
use Nouvu\Framework\View\Repository\{ CommitRepository, HeadRepository, TitleRepository };
use Nouvu\Framework\Component\Config\Repository;

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
		$this -> title -> set( null, $repository -> get( 'config.default_title' ) );
	}
	
	public function render( CommitRepository $commit ): void
	{
		$commit -> get( 'layout' ) ?? $commit -> set ( 'layout', $this -> layout );
		
		$commit -> set ( 'commit', 'render' );
		
		$commit -> replace( 'content', 'container.content' );
		
		$commit -> replace( 'layout', 'container.layout' );
	}
	
	public function redirect( CommitRepository $commit ): void
	{
		$commit -> set ( 'commit', 'redirect' );
		
		$commit -> replace( 'path', 'container' );
	}
	
	public function json( CommitRepository $commit ): void
	{
		$this -> render( $commit );
		
		$commit -> set ( 'commit', 'json' );
		
		$commit -> set( 'container', [ 'content' => $commit -> get( 'container.content' ) ] );
	}
	
	public function custom( CommitRepository $commit ): void
	{
		$commit -> set ( 'commit', 'custom' );
	}
	
	public function filter( CommitRepository $commit ): void
	{
		$commit -> set( null, array_filter ( $commit -> all() ) );
	}
	
	public function filling( CommitRepository $commit ): void
	{
		foreach ( [ 'directory', 'layout', 'head', 'title', 'extension' ] AS $name )
		{
			$commit -> set( $name, $this -> {$name} );
		}
	}
	
	public function terminal( CommitRepository $commit, Content $content ): void
	{
		$this -> filter( $commit );
		
		$this -> filling( $commit );
		
		$this -> send( $commit, new Terminal( $commit ), $content );
	}
	
	private function send( CommitRepository $commit, Terminal $terminal, Content $content ): void
	{
		match ( $commit -> getCommit() )
		{
			'render'	=> $terminal -> contentResponse( $this -> response, $content ),
			'redirect'	=> $terminal -> redirectResponse( $this -> response ),
			'json'		=> $terminal -> jsonResponse( $this -> response, $content ),
			'custom'	=> $terminal -> customResponse( $this -> response ),
		};
		
		$this -> response -> prepare( $this -> request );
		
		$this -> response -> send();
	}
}