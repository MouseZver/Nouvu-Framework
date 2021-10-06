<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View\Builder;

use Symfony\Component\HttpFoundation\Response;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Web\View\Builder\ShortTag;

class Content
{
	private string $content = '';
	
	public function __construct ( private CommitRepository $commit )
	{
		// $this -> commit -> reset( 'content', '' );
	}
	
	protected function replaceCode( string $template, string $content ): string
	{
		return ( string ) new ShortTag( [ $this, $this -> commit -> get( 'model' ) ], function ( array $matches ) use ( $template ): string
		{
			$file = dirname ( $template ) . DIRECTORY_SEPARATOR . $matches[1];
			
			if ( file_exists ( $file . $this -> commit -> get( 'extension' ) ) )
			{
				return $this -> getHtml( $file );
			}
			
			return "<!-- Not found ({$matches[1]}) -->";
		}, 
		$content );
	}
	
	public function setContent( Response $response, callable | null $call = null ): void
	{
		foreach ( array_filter ( $this -> commit -> getContainer() ) AS $name )
		{
			$this -> content = $this -> getHtml( $this -> commit -> get( 'directory' ) . $name );
		}
		
		if ( is_callable ( $call ) )
		{
			$response -> setContent( $call( $this -> getContent() ) );
			
			return;
		}
		
		$response -> setContent( $this -> getContent() );
	}
	
	public function getContent(): string
	{
		return $this -> content;
	}
	
	public function getTitle(): string
	{
		return $this -> commit -> get( 'title' ) -> getResult();
	}
	
	public function getHead( string ...$heads )
	{
		if ( func_num_args () > 0 )
		{
			$this -> commit -> get( 'head' ) -> add( 'selected', func_get_args (), true );
		}
		
		return implode ( PHP_EOL . "\t", iterator_to_array ( $this -> commit -> get( 'head' ) -> getResult() ) );
	}
	
	public function getHtml( string $name ): string
	{
		ob_start ();
		
		$closure = $this -> commit -> get( 'model' )();
		
		$closure( $name );
		
		return $this -> replaceCode( $name, ob_get_clean () );
	}
}