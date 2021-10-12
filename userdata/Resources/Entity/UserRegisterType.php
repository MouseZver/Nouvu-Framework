<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Entity;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class UserRegisterType
{
	private $name;

	public static function loadValidatorMetadata( ClassMetadata $metadata )
	{
		$metadata -> addPropertyConstraint( 'name', new NotBlank() );
	}
}