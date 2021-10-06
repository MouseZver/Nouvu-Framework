<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
//use Symfony\Component\Validator\Constraints as Assert;
//use Doctrine\ORM\Mapping as ORM;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	private ?int $id = null; // ???????????? __unserialize
	//private ?string $fullName;
	private ?string $username;
	private ?string $email;
	private ?string $password;
	private array $roles = [];
	
	// ????????????????????? как id значение присвоится // __unserialize
	public function getId(): ?int
	{
		return $this -> id;
	}
	
	public function setFullName( string $fullName ): void
	{
		$this -> fullName = $fullName;
	}
	
	public function getFullName(): ?string
	{
		return $this -> fullName;
	}
	
	public function getUserIdentifier(): ?string
	{
		return $this -> username;
	}
	
	public function getUsername(): ?string
	{
		return $this -> getUserIdentifier();
	}
	
	public function setUsername( string $username ): void
	{
		$this -> username = $username;
	}
	
	public function getEmail(): ?string
	{
		return $this -> email;
	}
	
	public function setEmail( string $email ): void
	{
		$this -> email = $email;
	}
	
	public function getPassword(): ?string
	{
		return $this -> password;
	}
	
	public function setPassword( string $password ): void
	{
		$this -> password = $password;
	}
	
	public function getRoles(): array
	{
		$roles = $this -> roles;
		
		if ( empty ( $roles ) )
		{
			$roles[] = 'ROLE_USER';
		}

		return array_unique ( $roles );
	}

	public function setRoles( array $roles ): void
	{
		$this -> roles = $roles;
	}
	
	public function getSalt(): ?string
	{
		// We're using bcrypt in security.yaml to encode the password, so
		// the salt value is built-in and and you don't have to generate one
		// See https://en.wikipedia.org/wiki/Bcrypt
		
		return null;
	}
	
	public function eraseCredentials(): void
	{
		// if you had a plainPassword property, you'd nullify it here
		// $this->plainPassword = null;
	}

	/* public function __serialize(): array
	{
		// add $this->salt too if you don't use Bcrypt or Argon2i
		return [ $this -> id, $this -> username, $this -> password ];
	}

	public function __unserialize( array $data ): void
	{
		// add $this->salt too if you don't use Bcrypt or Argon2i
		[ $this -> id, $this -> username, $this -> password ] = $data;
	} */
}