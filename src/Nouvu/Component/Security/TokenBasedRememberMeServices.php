<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Security;

use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices as BaseTokenBasedRememberMeServices;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TokenBasedRememberMeServices extends BaseTokenBasedRememberMeServices
{
	
	
	public function login( Request $request, Response $response, TokenInterface $token )
	{
		$this -> loginSuccess( $request, $response, $token );
	}
	
	// процесс автологина
}
