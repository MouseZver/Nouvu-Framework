<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\User\Permissions;

use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Framework\Component\Validator\Exception\ViolationsException;
use Nouvu\Framework\Component\Config\Repository;
use Nouvu\Framework\Component\Database\{ DatabaseManager, StatementInterface };
use Nouvu\Framework\Component\Security\Core\User\Permissions;
use Nouvu\Resources\Entity\{ Request\Permission AS Request, Permission };

class Remove
{
	public function __construct ( 
		private DatabaseManager $databaseManager, 
		private Request $request 
	)
	{}
	
	public function validation( Permissions $permissions, int $id ): void
	{
		if ( ! $permissions -> isPermission( $id ) )
		{
			$failed = new ViolationsException( 'Validation failed' );
			
			$failed -> setErrors( [ 'permission' => 'Разрешение не найдено, или было ранее удалено' ] );
			
			throw $failed;
		}
	}
	
	/*public function recursiveRemoveRole( Roles $roles, Role $remove ): iterable
	{
		foreach ( $roles -> metaRoleHierarchy( $remove -> getRole() ) AS $role_hierarchy_id )
		{
			$data = $roles -> getRole( $role_hierarchy_id );
			
			$diff = array_diff ( $data -> getRoleHierarchy(), [ $remove -> getRole() ] );
			
			$data -> setRoleHierarchy( $diff );
			
			yield $data;
		}
	}*/
	
	public function dataPermissions( int ...$ids ): void
	{
		$map = array_map ( fn ( int $id ): array => [ $id ], $ids );
		
		$this -> databaseManager -> delete( \Permissions :: class )( $map );
	}
	
	/*public function dataRememberMeToken( int ...$ids ): void
	{
		$map = array_map ( fn ( int $id ): array => [ $id ], $ids );
		
		$this -> databaseManager -> delete( \RememberMeToken\By\UserId :: class )( $map );
	}*/
	
	/*public function dataUsersRole( int ...$roles_id ): void
	{
		$this -> databaseManager -> delete( \UsersRole\By\RoleId :: class )( $roles_id );
	}*/
}