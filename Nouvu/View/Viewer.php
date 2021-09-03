<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nouvu\Web\View\Builder\Content;
use Nouvu\Web\View\Repository\{
	Commit AS CommitRepository,
	Head AS HeadRepository,
	Title AS TitleRepository,
};
use Nouvu\Config\Config AS NouvuConfig;

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
	
	public function setPath( NouvuConfig $NouvuConfig ): void
	{
		$path = $NouvuConfig -> get( 'app.system.directory.view' ) . $NouvuConfig -> get( 'config.theme' );
		
		$this -> directory = rtrim ( $path, '\\/' ) . DIRECTORY_SEPARATOR;
	}
	
	public function setLayout( NouvuConfig $NouvuConfig ): void
	{
		$this -> layout = $NouvuConfig -> get( 'config.default_template' );
	}
	
	public function setExtension( NouvuConfig $NouvuConfig ): void
	{
		$this -> extension = $NouvuConfig -> get( 'viewer.extension' );
	}
	
	public function setHead( NouvuConfig $NouvuConfig ): void
	{
		$this -> head -> add( 'list', $NouvuConfig -> get( 'viewer.head' ) );
	}
	
	public function setTitle( NouvuConfig $NouvuConfig ): void
	{
		$this -> title -> set( $NouvuConfig -> get( 'config.default_title' ) );
	}
	
	public function render( CommitRepository $commit ): void
	{
		$commit -> get( 'layout' ) ?? $commit -> reset( 'layout', $this -> layout );
		
		$commit -> reset( 'commit', 'render' );
		
		$commit -> replace( 'content', 'container.content' );
		
		$commit -> replace( 'layout', 'container.layout' );
	}
	
	public function redirect( CommitRepository $commit ): void
	{
		$commit -> reset( 'commit', 'redirect' );
		
		$commit -> replace( 'path', 'container' );
	}
	
	public function json( CommitRepository $commit ): void
	{
		$this -> render( $commit );
		
		$commit -> reset( 'commit', 'json' );
	}
	
	public function terminal( CommitRepository $commit ): void
	{
		$commit -> set( array_filter ( $commit -> all() ) );
		
		foreach ( [ 'directory', 'layout', 'head', 'title', 'extension' ] AS $name )
		{
			$commit -> reset( $name, $this -> {$name} );
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