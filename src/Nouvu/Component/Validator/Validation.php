<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\{ Constraints as Assert, Validator\ValidatorInterface, ConstraintViolationList };
use Nouvu\Web\Component\Config\Repository;

class Validation
{
	private mixed $recursive;
	
	private array $pathNames;
	
	public function __construct ( protected Request $request, protected ValidatorInterface $validation )
	{}
	
	public function validate( string $method, array $rules ): void
	{
		$this -> methodValid( $method );
		
		$map = $this -> buildMap( $rules );
		
		$this -> loadMapRecursive( $map );
		
		$input = new Repository( [], '.' );
		
		foreach ( $this -> iteratorWorkKeys( $this -> getInput( $method ) ) AS $key => $val )
		{
			$input -> reset( $key, $val );
		}
		
		$groups = new Assert\GroupSequence( [ 'Default', 'custom' ] );
		
		$violations = $this -> validation -> validate( $input -> all(), new Assert\Collection( $map ), $groups );
		
		if ( $violations -> count() )
		{
			$failed = new Exception\ViolationsException( 'Validation failed' );
			
			$failed -> setErrors( iterator_to_array ( $this -> iteratorViolations( $violations ) ) );
			
			throw $failed;
		}
	}
	
	protected function iteratorViolations( ConstraintViolationList $violations ): \Generator
	{
		foreach ( $violations AS $e )
		{
			$key = str_replace ( '][', '.', trim ( $e -> getPropertyPath(), '[]' ) );
			
			yield $key => $e -> getMessage();
		}
	}
	
	protected function iteratorWorkKeys( array $input ): \Generator
	{
		$repositoryInput = new Repository( $input, '.' );
		
		foreach ( $this -> pathNames AS $key => $_ )
		{
			if ( $repositoryInput -> has( $key ) )
			{
				yield $key => $repositoryInput -> get( $key );
			}
		}
	}
	
	protected function getInput( string $method ): array
	{
		return match( strtoupper ( $method ) )
		{
			'POST' => $this -> request -> request -> all(),
			'GET' => $this -> request -> query -> all(),
			default => throw new \InvalidArgumentException( 'Undefined request method - ' . $method ),
		};
	}
	
	protected function methodValid( string $method ): void
	{
		if ( ! $this -> request -> isMethod( $method ) )
		{
			throw new Exception\InvalidMethodException( $method );
		}
	}
	
	protected function parseNames( array $segments ): void
	{
		$namesSegment = explode ( '.', $segments[0] );
		
		foreach ( $namesSegment AS $key => $nameRowSegment )
		{
			$this -> recursive = &$this -> recursive[$nameRowSegment];
			
			if ( isset ( $namesSegment[++$key] ) )
			{
				$collection = Assert :: class . '\\Collection';
				
				match( true )
				{
					! ( $this -> recursive instanceof $collection ) => $this -> recursive['assert'] = $collection,
					
					default => false,
				};
				
				$this -> recursive = &$this -> recursive['values'];
			}
		}
	}
	
	protected function parseAttributes( array $segments ): void
	{
		if ( isset ( $segments[1] ) )
		{
			$values = [];
			
			if ( isset ( $segments[2] ) )
			{
				if ( is_array ( $segments[2] ) )
				{
					$values = $segments[2];
				}
				else if ( is_string ( $segments[2] ) )
				{
					foreach ( explode ( ',', $segments[2] ) AS $attributes )
					{
						[ $key, $val ] = explode ( ':', $attributes );
						
						$values[$key] = $val;
					}
				}
				else
				{
					throw new \InvalidArgumentException( 'Invalid type parse in argument 2: ' . gettype ( $segments[2] ) );
				}
			}
			
			$this -> recursive[] = [ 'assert' => Assert :: class . '\\' . $segments[1], 'values' => $values ];
		}
	}
	
	protected function buildMap( array $rules ): array
	{
		$this -> pathNames = $map = [];
		
		foreach ( $rules AS $rule )
		{
			$this -> recursive = &$map;
			
			$segments = is_string ( $rule ) ? explode ( '|', $rule ) : $rule;
			
			$this -> parseNames( $segments );
			
			$this -> parseAttributes( $segments );
			
			$this -> recursive ??= [];
			
			$this -> pathNames[$segments[0]] ??= true;
		}
		
		return $map;
	}
	
	protected function loadMapRecursive( array &$segment )
	{
		foreach ( $segment AS &$recursion )
		{
			if ( isset ( $recursion['assert'] ) )
			{
				$this -> loadMapRecursive( $recursion['values'] );
				
				$recursion = new $recursion['assert']( $recursion['values'] );
			}
			else if ( is_array ( $recursion ) )
			{
				$this -> loadMapRecursive( $recursion );
			}
		}
	}
}