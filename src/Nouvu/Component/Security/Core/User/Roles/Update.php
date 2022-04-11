<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\User\Roles;

use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Framework\Component\Validator\Exception\ViolationsException;
use Nouvu\Framework\Component\Config\Repository;
use Nouvu\Framework\Component\Database\{ DatabaseManager, StatementInterface };
use Nouvu\Framework\Component\Security\Core\User\Roles;
use Nouvu\Resources\Entity\{ Request\Role AS Request, Role };

class Update
{
	public function __construct ( 
		private DatabaseManager $databaseManager, 
		private Request $request 
	)
	{}
	
	public function validation( Roles $roles, int $id ): void
	{
		$failed = new ViolationsException( 'Validation failed' );
		
		if ( array_diff ( $this -> request -> getRoleHierarchy(), $roles -> getListId() ) )
		{
			$failed -> setErrors( [ 'role_hierarchy' => 'Выбранный элемент роли, отсутствует в существующем списке' ] );
			
			throw $failed;
		}
		
		if ( ! $roles -> isRole( $id ) )
		{
			$failed -> setErrors( [ 'role' => 'Выбранная роль не существует' ] );
			
			throw $failed;
		}
		
		$role_id = $roles -> metaRole( $this -> request -> getRole() );
		
		if ( ! is_null ( $role_id ) && $role_id != $id )
		{
			$failed -> setErrors( [ 'role' => sprintf ( 'Тег роли %s уже существует', $this -> request -> getRole() ) ] );
			
			throw $failed;
		}
	}
	
	public function getRole( Role $role ): Role
	{
		$new = clone $role;
		
		$new -> setName( $this -> request -> getName() );
		
		$new -> setRole( $this -> request -> getRole() );
		
		$new -> setRoleHierarchy( $this -> request -> getRoleHierarchy() );
		
		return $new;
	}
	
	public function recursiveReplaceRole( Roles $roles, Role $before, Role $after ): iterable
	{
		foreach ( $roles -> metaRoleHierarchy( $before -> getRole() ) AS $role_hierarchy_id )
		{
			$data = $roles -> get( $role_hierarchy_id );
			
			$diff = array_diff ( $data -> getRoleHierarchy(), [ $before -> getRole() ] );
			
			$data -> setRoleHierarchy( array_unique ( [ ...$diff, $after -> getRole() ] ) );
			
			yield $data;
		}
	}
	
	public function dataRoles( Role ...$roles ): void
	{
		$this -> databaseManager -> update( \Roles :: class )( $roles );
	}
	
	public function dataPermission( array ...$attributes ): void
	{
		$this -> databaseManager -> update( \Permissions\Role\By\Role :: class )( $attributes );
	}
	
	public function dataUsers( UserInterface ...$users ): void
	{
		$this -> databaseManager -> update( \Users :: class )( $users );
	}
}