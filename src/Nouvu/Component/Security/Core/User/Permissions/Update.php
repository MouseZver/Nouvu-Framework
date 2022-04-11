<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\User\Permissions;

use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Framework\Component\Validator\Exception\ViolationsException;
use Nouvu\Framework\Component\Config\Repository;
use Nouvu\Framework\Component\Database\{ DatabaseManager, StatementInterface };
use Nouvu\Framework\Component\Security\Core\User\{ Permissions, Roles };
use Nouvu\Resources\Entity\{ Request\Permission AS Request, Permission };

class Update
{
	public function __construct ( 
		private DatabaseManager $databaseManager, 
		private Request $request 
	)
	{}
	
	public function validation( Permissions $permissions, Roles $roles, int $id ): void
	{
		$failed = new ViolationsException( 'Validation failed' );
		
		if ( $roles -> metaRole( $this -> request -> getRole() ) )
		{
			$failed -> setErrors( [ 'role' => 'Выбранная роль не существует в списке' ] );
			
			throw $failed;
		}
		
		if ( ! $permissions -> isPermission( $id ) )
		{
			$failed -> setErrors( [ 'permission' => 'Разрешение не найдено, или было ранее удалено' ] );
			
			throw $failed;
		}
		
		$permission_id = $permissions -> metaPermission( $this -> request -> getPermission() );
		
		if ( ! is_null ( $permission_id ) && $permission_id != $id )
		{
			$failed -> setErrors( [ 'permission' => sprintf ( 'Тег роли %s уже существует', $this -> request -> getPermission() ) ] );
			
			throw $failed;
		}
	}
	
	public function getPermission( Permission $permission ): Permission
	{
		$new = clone $permission;
		
		$new -> setName( $this -> request -> getName() );
		
		$new -> setPermission( $this -> request -> getPermission() );
		
		$new -> setRole( $this -> request -> getRole() );
		
		return $new;
	}
	
	public function dataPermissions( Permission ...$roles ): void
	{
		$this -> databaseManager -> update( \Permissions :: class )( $roles );
	}
}