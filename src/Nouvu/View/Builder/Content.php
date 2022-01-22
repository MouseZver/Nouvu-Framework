<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View\Builder;

use Symfony\Component\HttpFoundation\Response;
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Web\View\Builder\ShortTag;
use Stringable;

class Content
{
	private string $content = '';
	
	public function __construct ( private CommitRepository $commit )
	{
		// $this -> commit -> set( 'content', '' );
	}
	
	protected function replaceCode( string $template, string $content ): Stringable
	{
		return new ShortTag( 
			[ 
				$this, 
				$this -> commit -> get( 'controller' ), 
				...$this -> commit -> get( 'models', [] ) 
			], 
			function ( array $matches ) use ( $template ): string
			{
				$file = dirname ( $template ) . DIRECTORY_SEPARATOR . $matches[1];
				
				if ( file_exists ( $file . $this -> commit -> get( 'extension' ) ) )
				{
					return $this -> getHtml( $file );
				}
				
				throw new \InvalidArgumentException( sprintf ( 'Not loading template-tag {%s}, template: {%s}', 
					$matches[1], 
					str_replace ( $this -> commit -> get( 'directory' ), '', $template ),
				) );
			}, 
			$content 
		);
	}
	
	public function setContent( Response $response, callable | null $call = null ): void
	{
		foreach ( array_filter ( $this -> commit -> getContainer() ) AS $key => $name )
		{
			$this -> content = $this -> getHtml( $this -> commit -> get( 'directory' ) . $name );
			
			if ( $key == 'content' )
			{
				$this -> content = strtr ( $this -> content, $this -> commit -> getArguments() );
			}
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
	
	public function getHead( string ...$heads ): string
	{
		if ( func_num_args () > 0 )
		{
			$this -> commit -> get( 'head' ) -> add( 'selected', func_get_args (), true );
		}
		
		$head = implode ( PHP_EOL . "\t", iterator_to_array ( $this -> commit -> get( 'head' ) -> getResult() ) );
		
		$this -> commit -> get( 'head' ) -> set( 'selected', [] );
		
		return $head;
	}
	
	public function getInclude( string $name ): string
	{
		return $this -> getHtml( $this -> commit -> get( 'directory' ) . $name );
	}
	
	public function getHtml( string $name ): string
	{
		ob_start ();
		
		$closure = $this -> commit -> get( 'controller' )();
		
		$closure( $name );
		
		return ( string ) $this -> replaceCode( $name, ob_get_clean () );
	}
	
	
}