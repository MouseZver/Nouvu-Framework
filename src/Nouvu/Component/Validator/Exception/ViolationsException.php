<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Validator\Exception;

class ViolationsException extends RuntimeException
{
	private array $errors = [];
	
	public function __construct ( string $message )
	{
		parent :: __construct ( $message );
	}
	
	public function setErrors( array $errors ): void
	{
		$this -> errors = $errors;
	}
	
	public function getErrors(): array
	{
		return $this -> errors;
	}
}