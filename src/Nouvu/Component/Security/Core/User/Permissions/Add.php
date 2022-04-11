<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\User\Permissions;

use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Framework\Component\Validator\Exception\ViolationsException;
use Nouvu\Framework\Component\Config\Repository;
use Nouvu\Framework\Component\Database\{ DatabaseManager };
use Nouvu\Framework\Component\Security\Core\User\{ Permissions, Roles };
use Nouvu\Resources\Entity\{ Request\Permission AS Request, Permission };

class Add
{
	public function __construct ( 
		private DatabaseManager $databaseManager, 
		private Request $request 
	)
	{}
	
	public function validation( Permissions $permissions, Roles $roles ): void
	{
		$failed = new ViolationsException( 'Validation failed' );
		
		if ( $permissions -> metaPermission( $this -> request -> getPermission() ) )
		{
			$failed -> setErrors( [ 'permission' => sprintf ( 'Разрешение %s уже существует', $this -> request -> getPermission() ) ] );
			
			throw $failed;
		}
		
		if ( $roles -> metaRole( $this -> request -> getRole() ) )
		{
			$failed -> setErrors( [ 'role' => 'Выбранная роль не существует в списке' ] );
			
			throw $failed;
		}
	}
	
	public function getPermission(): Permission
	{
		$permission = new Permission;
		
		$permission -> setName( $this -> request -> getName() );
		
		$permission -> setPermission( $this -> request -> getPermission() );
		
		$permission -> setRole( $this -> request -> getRole() );
		
		return $permission;
	}
	
	public function dataPermissions( Permission $permission ): void
	{
		$this -> databaseManager -> insert( \Permissions :: class )( $permission -> getName(), $permission -> getPermission(), $permission -> getRole() );
	}
}