<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

class User implements UserInterface, EncoderAwareInterface
{
	private int | null $id = null;
	private string | null $username = null;
	private string | null $email = null;
	private string | null $password = null;
	private string | null $plainPassword = null;
	private array $roles = [];
	private bool $confirmed = false;
	private string | null $create_at = null;
	
	public function getId(): int | null
	{
		return $this -> id;
	}
	
	public function getUserIdentifier(): string | null
	{
		return $this -> username;
	}
	
	public function getUsername(): string | null
	{
		return $this -> getUserIdentifier();
	}
	
	public function setUsername( string $username ): void
	{
		$this -> username = $username;
	}
	
	public function getEmail(): string | null
	{
		return $this -> email;
	}
	
	public function setEmail( string $email ): void
	{
		$this -> email = $email;
	}
	
	public function getPassword(): string | null
	{
		return $this -> password;
	}
	
	public function setPassword( string $password ): void
	{
		$this -> password = $password;
	}
	
	public function getPlainPassword(): string | null
	{
		return $this -> plainPassword;
	}
	
	public function setPlainPassword( string $password ): void
	{
		$this -> plainPassword = $password;
	}
	
	public function getCreatedAt(): string | null
    {
		$this -> create_at;
	}
	
	public function setCreatedAt( string $create_at ): void
    {
		$this -> create_at = $create_at;
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
	
	public function getConfirmed(): bool
	{
		return $this -> confirmed;
	}
	
	public function setConfirmed( bool $confirmed ): void
	{
		$this -> confirmed = $confirmed;
	}
	
	public function getSalt(): string | null
	{
		// We're using bcrypt in security.yaml to encode the password, so
		// the salt value is built-in and and you don't have to generate one
		// See https://en.wikipedia.org/wiki/Bcrypt
		
		return null;
	}
	
	public function getEncoderName(): string | null
	{
		return \Nouvu\Resources\Entity\User :: class;
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