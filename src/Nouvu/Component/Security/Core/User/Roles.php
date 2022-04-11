<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\User;

use Nouvu\Framework\Component\Config\Repository;
use Nouvu\Framework\Component\Database\{ DatabaseManager, StatementInterface };
use Nouvu\Resources\Entity\{ Role, User };

class Roles
{
	private Repository $data;
	
	public function __construct ( 
		private DatabaseManager $databaseManager, 
		private Roles\Add $add,
		private Roles\Update $update,
		private Roles\Remove $remove,
		private \Closure $entityRole,
		private \Closure $entityUser,
	)
	{
		$this -> data = new Repository( [ 'meta' => [ 'role' => [], 'hierarchy' => [] ] ] );
		
		$closure = function ( object $std ): iterable
		{
			$role = new Role;
			
			$this -> entityRole -> call( $role, $std );
			
			$this -> data -> add( 'meta.role', [ $role -> getRole() => $role -> getId() ] );
			
			foreach ( $role -> getRoleHierarchy() AS $role_hierarchy )
			{
				if ( $this -> data -> has( 'meta.hierarchy.' . $role_hierarchy ) )
				{
					$this -> data -> add( 'meta.hierarchy.' . $role_hierarchy, [ $role -> getId() ] );
				}
				else
				{
					$this -> data -> set( 'meta.hierarchy.' . $role_hierarchy, [ $role -> getId() ] );
				}
			}
			
			yield $role -> getId() => $role;
		};
		
		$roles = new Repository( $this -> selectDataRoles( $closure ) );
		
		$roles -> set( 'meta', $this -> data -> get( 'meta' ) );
		
		$this -> data = $roles;
	}
	
	public function isRole( int $id ): bool
	{
		return $this -> data -> has( $id );
	}
	
	public function getList(): iterable
	{
		foreach ( $this -> data -> all() AS $id => $role )
		{
			if ( $id !== 'meta' )
			{
				yield $id => $role;
			}
		}
	}
	
	public function getListId(): array
	{
		$roles = $this -> data -> all();
		
		unset ( $roles['meta'] );
		
		return array_keys ( $roles );
	}
	
	public function get( int $id ): Role
	{
		return $this -> data -> get( $id );
	}
	
	public function metaRole( string $name ): ?int
	{
		return $this -> data -> get( 'meta.role.' . $name );
	}
	
	public function metaRoleHierarchy( string $name ): array
	{
		return $this -> data -> get( 'meta.hierarchy.' . $name, [] );
	}
	
	public function identifierToName( int ...$list ): iterable
	{
		foreach ( $list AS $role_id )
		{
			if ( $this -> isRole( $role_id ) )
			{
				yield $this -> get( $role_id ) -> getRole();
			}
		}
	}
	
	protected function selectDataRoles( \Closure $closure ): array
	{
		return $this -> databaseManager -> select( \Roles :: class )() -> all( StatementInterface :: FETCH_FUNC, $closure );
	}
	
	protected function selectDataUsersRole( int ...$roles_id ): array
	{
		$map = array_map ( fn ( int $id ): array => [ $id ], $roles_id );
		
		return $this -> databaseManager -> select( \UsersRole\UserId\By\RoleId :: class )( $map ) 
			-> all( StatementInterface :: FETCH_COLUMN );
	}
	
	protected function selectDataUsers( \Closure $closure, array $data ): array
	{
		return $this -> databaseManager -> select( \Users\By\Id :: class )( $data ) -> all( StatementInterface :: FETCH_FUNC, $closure );
	}
	
	public function add(): void
	{
		$this -> add -> validation( $this );
		
		$role = $this -> add -> getRole();
		
		
		$hierarchy = iterator_to_array ( $this -> identifierToName( ...$role -> getRoleHierarchy() ) );
		
		$role -> setRoleHierarchy( $hierarchy );
		
		
		$this -> add -> dataRoles( $role );
	}
	
	public function update( int $id ): void
	{
		$this -> update -> validation( $this, $id );
		
		$newRole = $this -> update -> getRole( $this -> get( $id ) );
		
		
		$hierarchy = iterator_to_array ( $this -> identifierToName( ...$newRole -> getRoleHierarchy() ) );
		
		$newRole -> setRoleHierarchy( $hierarchy );
		
		
		$iterableRoles = iterator_to_array ( $this -> update -> recursiveReplaceRole( $this, $this -> get( $id ), $newRole ) );
		
		$this -> update -> dataRoles( $newRole, ...$iterableRoles );
		
		
		$replace = [ $newRole -> getRole(), $this -> get( $id ) -> getRole() ];
		
		$this -> update -> dataPermission( $replace );
		
		
		$users_id = $this -> selectDataUsersRole( $id );
		
		if ( $users_id )
		{
			$closure = function ( object $std ) use ( $newRole ): User
			{
				$user = new User;
				
				$this -> entityUser -> call( $user, $std );
				
				$diff = array_diff ( $user -> getRoles(), [ $this -> getRole( $newRole -> getId() ) -> getRole() ] );
				
				$user -> setRoles( array_unique ( [ ...$diff, $newRole -> getRole() ] ) );
				
				return $user;
			};
			
			$updateUsers = $this -> selectDataUsers( $closure, $users_id );
			
			$this -> update -> dataUsers( ...$updateUsers );
			
			$this -> remove -> dataRememberMeToken( ...$users_id );
		}
	}
	
	public function remove( int $id ): void
	{
		$this -> remove -> validation( $this, $id );
		
		$this -> remove -> dataRoles( $id );
		
		
		$replace = [ 'ROLE_ZERO', $this -> get( $id ) -> getRole() ];
		
		$this -> update -> dataPermission( $replace );
		
		
		$iterableRoles = $this -> remove -> recursiveRemoveRole( $this, $this -> get( $id ) );
		
		$this -> update -> dataRoles( ...iterator_to_array ( $iterableRoles ) );
		
		
		$users_id = $this -> selectDataUsersRole( $id );
		
		if ( $users_id )
		{
			$role = $this -> get( $id );
			
			$closure = function ( object $std ) use ( $role ): User
			{
				$user = new User;
				
				$this -> entityUser -> call( $user, $std );
				
				$diff = array_diff ( $user -> getRoles(), [ $role -> getRole() ] );
				
				$user -> setRoles( array_unique ( $diff ) );
				
				return $user;
			};
			
			$updateUsers = $this -> selectDataUsers( $closure, $users_id );
			
			$this -> update -> dataUsers( ...$updateUsers );
			
			$this -> remove -> dataUsersRole( $id );
			
			$this -> remove -> dataRememberMeToken( ...$users_id );
		}
	}
}