<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Foundation;

use Symfony\Component\Console\Application AS ConsoleApp;
use Symfony\Component\Console\Command\Command;
use Psr\Container\ContainerInterface;

use function App\Foundation\Helpers\{ app };

abstract class Application
{
	protected ContainerInterface $container;
	
	abstract public function getName(): string;
	
	abstract protected function getHelpersDir(): string;
	
	abstract protected function getPackagesDir(): string;
	
	public function __set( string $name, callable $value ): void
	{
		$this -> container ?-> set( $name, $value );
	}
	
	public function __get( string $name ): mixed
	{
		return $this -> container ?-> get( $name );
	}
	
	public function __isset( string $name ): bool
	{
		return ( bool ) $this -> container ?-> has( $name );
	}
	
	public function setContainer( ContainerInterface $container ): void
	{
		$this -> container = $container;
	}
	
	abstract protected function initializeHelpers(): void;
	
	abstract protected function initializePackages(): void;
	
	abstract protected function configureBuilder(): void;

	abstract protected function initializeConfigure(): void;
	
	public function boot(): void
	{
		$this -> initializeHelpers();
		
		$this -> initializePackages();
		
		$this -> configureBuilder();

		$this -> initializeConfigure();
	}
	
	/*
		$console = ( new Application ) -> console( App\Console\Command\Example :: class );
		$console -> run();
	*/
	public function console( Command ...$commands ): ConsoleApp
	{
		$console = new ConsoleApp;
		
		foreach ( $commands AS $command )
		{
			$console -> add( $command );
		}
		
		return $console;
	}
}
