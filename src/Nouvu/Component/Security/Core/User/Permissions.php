<?php

declare ( strict_types = 1 );

namespace Nouvu\Framework\Component\Security\Core\User;

use Nouvu\Framework\Component\Config\Repository;
use Nouvu\Framework\Component\Database\{ DatabaseManager, StatementInterface };
use Nouvu\Resources\Entity\{ Permission, User };

class Permissions
{
	private Repository $data;
	
	public function __construct ( 
		private DatabaseManager $databaseManager, 
		private Roles $roles,
		private Permissions\Add $add,
		private Permissions\Update $update,
		private Permissions\Remove $remove,
		private \Closure $entityPermission,
	)
	{
		$this -> data = new Repository( [ 'meta' => [ 'role' => [], 'permission' => [] ] ] );
		
		$closure = function ( object $std ): iterable
		{
			$permission = new Permission;
			
			$this -> entityPermission -> call( $permission, $std );
			
			$this -> data -> add( 'meta.role', [ $permission -> getRole() => $permission -> getId() ] );
			
			$name = str_replace ( '.', '|', $permission -> getPermission() );
			
			$this -> data -> add( 'meta.permission', [ $name => $permission -> getId() ] );
			
			yield $permission -> getId() => $permission;
		};
		
		$data = new Repository( $this -> selectDataPermissions( $closure ) );
		
		$data -> set( 'meta', $this -> data -> get( 'meta' ) );
		
		$this -> data = $data;
	}
	
	public function isPermission( int $id ): bool
	{
		return $this -> data -> has( $id );
	}
	
	public function getList(): iterable
	{
		foreach ( $this -> data -> all() AS $id => $permission )
		{
			if ( $id !== 'meta' )
			{
				yield $id => $permission;
			}
		}
	}
	
	public function getListId(): array
	{
		$permissions = $this -> data -> all();
		
		unset ( $permissions['meta'] );
		
		return array_keys ( $permissions );
	}
	
	public function get( int $id ): Permission
	{
		return $this -> data -> get( $id );
	}
	
	public function metaRole( string $name ): ?int
	{
		return $this -> data -> get( 'meta.role.' . $name );
	}
	
	public function metaPermission( string $name ): ?int
	{
		return $this -> data -> get( 'meta.permission.' . str_replace ( '.', '|', $name ) );
	}
	
	protected function selectDataPermissions( \Closure $closure ): array
	{
		return $this -> databaseManager -> select( \Permissions :: class )() -> all( StatementInterface :: FETCH_FUNC, $closure );
	}
	
	public function add(): void
	{
		$this -> add -> validation( $this, $this -> roles );
		
		$permission = $this -> add -> getPermission();
		
		$permission -> setRole( $this -> roles -> get( ( int ) $permission -> getRole() ) -> getRole() );
		
		$this -> add -> dataPermissions( $permission );
	}
	
	public function update( int $id ): void
	{
		$this -> update -> validation( $this, $this -> roles, $id );
		
		$newPermission = $this -> update -> getPermission( $this -> get( $id ) );
		
		$newPermission -> setRole( $this -> roles -> get( ( int ) $newPermission -> getRole() ) -> getRole() );
		
		$this -> update -> dataPermissions( $newPermission );
	}
	
	public function remove( int $id ): void
	{
		$this -> remove -> validation( $this, $id );
		
		$this -> remove -> dataPermissions( $id );
	}
}