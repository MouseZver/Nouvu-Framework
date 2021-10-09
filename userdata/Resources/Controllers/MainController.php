<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\Controllers;

use Nouvu\Web\Http\Controllers\AbstractController;
use Nouvu\Web\View\Repository\CommitRepository;

final class MainController extends AbstractController
{
	public function index(): CommitRepository
	{
		$this -> title( [ 'Главная', 'Не главная' ], true );
		
		if ( $this -> isAjax() )
		{
			return $this -> json( 'index' );
		}
		
		return $this -> render( 'index' );
	}
	
	public function welcome( $slug = null ): CommitRepository
	{
		$this -> title( [ 'Welcome' ] );
		
		return $this -> render( 'welcome' );
	}
	
	public function err404(): CommitRepository
	{
		$this -> title( [ 'Страница не найдена' ], true );
		
		return $this -> render( 'error.404', 'error-template' );
	}
	
	public function err500(): CommitRepository
	{
		$this -> title( [ 'Ошибка сервера' ], true );
		
		return $this -> render( 'error.500', 'error-template' );
	}
	
	public function testError(): CommitRepository
	{
		require '1.php'; // Test error
		
		return $this -> render( 'index' );
	}
}
