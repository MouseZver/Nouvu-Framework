<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\User\Roles;

use Symfony\Component\Security\Core\User\UserInterface;
use Nouvu\Framework\Component\Validator\Exception\ViolationsException;
use Nouvu\Framework\Component\Config\Repository;
use Nouvu\Framework\Component\Database\{ DatabaseManager, StatementInterface };
use Nouvu\Framework\Component\Security\Core\User\Roles;
use Nouvu\Resources\Entity\{ Request\Role AS Request, Role };

class Remove
{
	public function __construct ( 
		private DatabaseManager $databaseManager, 
		private Request $request 
	)
	{}
	
	public function validation( Roles $roles, int $id ): void
	{
		if ( ! $roles -> isRole( $id ) )
		{
			$failed = new ViolationsException( 'Validation failed' );
			
			$failed -> setErrors( [ 'role' => 'Роль не найдена, или была ранее удалена' ] );
			
			throw $failed;
		}
	}
	
	public function recursiveRemoveRole( Roles $roles, Role $remove ): iterable
	{
		foreach ( $roles -> metaRoleHierarchy( $remove -> getRole() ) AS $role_hierarchy_id )
		{
			$data = $roles -> get( $role_hierarchy_id );
			
			$diff = array_diff ( $data -> getRoleHierarchy(), [ $remove -> getRole() ] );
			
			$data -> setRoleHierarchy( $diff );
			
			yield $data;
		}
	}
	
	public function dataRoles( int ...$ids ): void
	{
		$map = array_map ( fn ( int $id ): array => [ $id ], $ids );
		
		$this -> databaseManager -> delete( \Roles :: class )( $map );
	}
	
	public function dataRememberMeToken( int ...$ids ): void
	{
		$map = array_map ( fn ( int $id ): array => [ $id ], $ids );
		
		$this -> databaseManager -> delete( \RememberMeToken\By\UserId :: class )( $map );
	}
	
	public function dataUsersRole( int ...$roles_id ): void
	{
		$this -> databaseManager -> delete( \UsersRole\By\RoleId :: class )( $roles_id );
	}
}