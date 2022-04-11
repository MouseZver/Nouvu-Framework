<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\User\Roles;

use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Framework\Component\Validator\Exception\ViolationsException;
use Nouvu\Framework\Component\Config\Repository;
use Nouvu\Framework\Component\Database\{ DatabaseManager };
use Nouvu\Framework\Component\Security\Core\User\Roles;
use Nouvu\Resources\Entity\{ Request\Role AS Request, Role };

class Add
{
	public function __construct ( 
		private DatabaseManager $databaseManager, 
		private Request $request 
	)
	{}
	
	public function validation( Roles $roles ): void
	{
		$failed = new ViolationsException( 'Validation failed' );
		
		if ( $roles -> metaRole( $this -> request -> getRole() ) )
		{
			$failed -> setErrors( [ 'role' => sprintf ( 'Тег %s уже существует', $this -> request -> getRole() ) ] );
			
			throw $failed;
		}
		
		if ( array_diff ( $this -> request -> getRoleHierarchy(), $roles -> getListId() ) )
		{
			$failed -> setErrors( [ 'role_hierarchy' => 'Выбранный элемент роли, отсутствует в существующем списке' ] );
			
			throw $failed;
		}
		
		if ( $this -> request -> getRole() == 'ROLE_ZERO' )
		{
			$failed -> setErrors( [ 'role' => 'Тег ROLE_ZERO используется системой' ] );
			
			throw $failed;
		}
	}
	
	public function getRole(): Role
	{
		$role = new Role;
		
		$role -> setName( $this -> request -> getName() );
		
		$role -> setRole( $this -> request -> getRole() );
		
		$role -> setRoleHierarchy( $this -> request -> getRoleHierarchy() );
		
		return $role;
	}
	
	public function dataRoles( Role $role ): void
	{
		$this -> databaseManager -> insert( \Roles :: class )( $role -> getName(), $role -> getRole(), json_encode ( $role -> getRoleHierarchy() ) );
	}
}