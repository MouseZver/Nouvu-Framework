<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Models;

use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Web\Component\Validator\Exception\ViolationsException;

class AbstractModel //implements ???...
{
	public function __construct ( protected App $app )
	{
		
	}
	
	public function getRoute(): string
	{
		return $this -> app -> request -> attributes -> get( '_route' );
	}
	
	public function getErrors(): string
	{
		$errors = $this -> app -> request -> attributes -> get( 'errors' );
		
		if ( $errors instanceof ViolationsException )
		{
			return implode ( PHP_EOL, array_map ( 
				fn( $message ) => "<p style=\"background-color: #ff959580;padding: 10px;font-size: 9pt;border-radius: 10px;\">{$message}</p>", 
				$errors -> getErrors() 
			) );
		}
		
		return '';
	}
}