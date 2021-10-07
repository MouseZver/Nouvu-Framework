<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Security;

use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
//use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\HttpFoundation\Request;

class NouvuAuthenticationListener implements ListenerInterface
{
	/**
	* @var TokenStorageInterface
	*/
	private $tokenStorage;

	/**
	* @var AuthenticationManagerInterface
	*/
	private $authenticationManager;

	/**
	* @var string Uniquely identifies the secured area
	*/
	private $providerKey;

	// ...

	//public function handle(GetResponseEvent $event)
	public function handle( Request $request )
	{
		$username = $request -> request -> get( '' );
		$password = ...;

		$unauthenticatedToken = new UsernamePasswordToken(
			$username,
			$password,
			$this->providerKey
		);

		$authenticatedToken = $this
			->authenticationManager
			->authenticate($unauthenticatedToken);

		$this->tokenStorage->setToken($authenticatedToken);
	}
}