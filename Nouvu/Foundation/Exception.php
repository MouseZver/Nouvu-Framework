<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Foundation;

class Exception extends \Exception
{
	private array $codes = [
		221422 => "Missing 'interChangeAbility' attribute in database the configuration file",
	];
	
	public function __construct ( mixed $messages = null, int $code = 0 )
	{
		if ( isset ( $this -> codes[$code] ) )
		{
			$messages = call_user_func_array ( 'sprintf', array_merge ( [ $this -> codes[$code] ], ( is_array ( $messages ) ? $messages : [ $messages ] ) ) );
		}
		else if ( is_array ( $messages ) )
		{
			$messages = implode ( ',', $messages );
		}
		
		parent :: __construct ( $messages, $code );
	}
}
