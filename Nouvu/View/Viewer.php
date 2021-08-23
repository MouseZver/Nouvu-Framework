<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nouvu\Web\Foundation\User\UserInterface;
use Nouvu\Web\View\Builder\ShortTag;
use Nouvu\Web\View\Builder\CreateHtml;

final class Viewer
{
	use Set, Get;
	
	// Путь до шаблонов html страниц
	public string $directory;
	
	// имя родительского шаблона
	public string | null $layout = null;
	
	// <head>все что тут находится</head>
	public Input $head;
	
	// <title>название</title>
	public Input $title;
	
	// выполнение команд от redirect, json или render
	public Input $command;
	
	// html контент или json строка
	public string $content = '';
	
	// 
	protected UserInterface $model;
	
	public string $extension = '.php';
	
	public function __construct ( private Request $request, private Response $response )
	{
		$this -> head = new Input( [ 'list' => [], 'selected' => [] ] );
		
		$this -> title = new Input( [ 'list' => [], 'delimiter' => ' - ' ] );
		
		$this -> command = new Input( [ 'commit' => '', 'container' => [] ] );
	}
	
	public function replaceCode( string $template, string $content ): string
	{
		return ( string ) new ShortTag( [ $this, $this -> model ], function ( array $matches ) use ( $template ): string
		{
			$file = dirname ( $template ) . DIRECTORY_SEPARATOR . $matches[1];
			
			if ( file_exists ( $file . $this -> extension ) )
			{
				return $this -> getHtml( $file );
			}
			
			return "<!-- Not found ({$matches[1]}) -->";
		}, 
		$content );
	}
	
	protected function builderHeads(): \Iterator
	{
		foreach ( $this -> head -> get( 'selected' ) AS $tag )
		{
			yield ( string ) new CreateHtml( $this -> head -> get( 'list.' . $tag ) );
		}
	}
	
	public function render( UserInterface $model, string $content, string | null $layout = null ): array
	{
		$this -> model = $model;
		
		$layout ??= $this -> layout;
		
		return [ 
			'commit' => 'render',
			'container' => compact ( 'content', 'layout' ),
		];
	}
	
	public function redirect( string $path ): array
	{
		return [ 
			'commit' => 'redirect',
			'container' => compact ( 'path' ),
		];
	}
	
	public function json( UserInterface $model, string $content, string | null $layout = null ): array
	{
		$this -> model = $model;
		
		$this -> setLayout( $layout );
		
		$render = $this -> render( $content, $layout );
		
		$render['commit'] = 'json';
		
		return $render;
	}
	
	private function send()
	{
		match ( $this -> commit )
		{
			'render'	=> $this -> contentResponse(),
			'redirect'	=> $this -> redirectResponse(), //$this => response -> headers -> set( 'Location', $url ),
			'json'		=> $this -> jsonResponse(),
		};
		
		/* $response->setStatusCode(Response::HTTP_OK); // Response::HTTP_NOT_FOUND
		
		$response->headers->set('Content-Type', 'text/html');
		
		$response->setCharset('ISO-8859-1'); */
		
		$this -> response -> prepare( $this -> request );
		
		$this -> response -> send();
	}
	
	public function terminal( array $command )
	{
		foreach ( $command AS $name => $value )
		{
			$this -> {$name} = $value;
		}
		
		$this -> send();
	}
	
	private function contentResponse(): void
	{
		$this -> setContent();
		
		$this -> response -> headers -> set( 'Content-Type', 'text/html' );
	}
	
	private function redirectResponse(): void
	{
		
	}
	
	private function jsonResponse(): void
	{
		$this -> setContent( function ( string $content ): string
		{
			return json_encode ( [ 
				'content' => $content, 
				'title' => $this -> getTitle()
			], 
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		} );
		
		$response -> headers -> set( 'Content-Type', 'application/json' );
	}
}
