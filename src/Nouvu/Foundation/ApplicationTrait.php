<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation;

//use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\{ Security, User\UserInterface };

trait ApplicationTrait
{
	public function getLocale(): string
	{
		return $this -> app -> repository -> get( 'config.locale' );
	}
	
	public function getCharset(): string
	{
		return $this -> app -> repository -> get( 'config.default_charset' );
	}
	
	public function make( string $class, array $params = [] ): mixed
	{
		return $this -> app -> container -> make( $class, $params );
	}
	
	public function path( string $name ): string | null
	{
		return $this -> app -> repository -> get( 'app.system.directory.' . $name );
	}
	
	public function getLastUsername(): string
	{
		return $this -> app -> session -> get( Security :: LAST_USERNAME, '' );
	}
	
	public function getUser(): UserInterface | null
	{
		return $this -> app -> container -> get( 'security.token_storage' ) -> getToken() ?-> getUser();
	}

	public function isGranted( array $attribute, $subject = null ): bool
	{
		try
		{
			return $this -> app -> security -> isGranted( $attribute, $subject );
		}
		catch ( AuthenticationException )
		{
			return false;
		}
	}
	
	public function addFilemtime( string $file ): string
	{
		return $this -> app -> repository -> get( 'app.addFilemtime' )( $file );
	}
}