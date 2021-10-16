<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Form\FormInterface;
use Nouvu\Web\Foundation\{ Application, ApplicationTrait };
use Nouvu\Web\View\Repository\CommitRepository;
//use Nouvu\Web\Resources\Model\AbstractModel;

class AbstractController
{
	use ApplicationTrait;
	
	public function __construct ( protected Application $app )
	{
		$model = str_replace ( \Controller :: class, \Model :: class, static :: class );
		
		$this -> app -> request -> attributes -> set( '_model', $model );
	}
	
	protected function getModel()
	{
		return $this -> make( $this -> app -> request -> attributes -> get( '_model' ), [ $this -> app ] );
	}
	
	private function getCommitInstance( array $data ): CommitRepository
	{
		$name = $this -> app -> request -> attributes -> get( '_model' );
		
		if ( ! is_null ( $name ) && $this -> app -> container -> has( $name ) )
		{
			$data['model'] = $this -> getModel();
		}
		
		$data['controller'] = $this;
		
		return new CommitRepository( $data );
	}
	
	protected function isAjax(): bool
	{
		return $this -> app -> request -> isXmlHttpRequest();
	}
	
	protected function title( array $title, bool $replace = false ): void
	{
		if ( $replace )
		{
			$this -> app -> view -> title -> reset( 'list', $title );
			
			return;
		}
		
		$this -> app -> view -> title -> add( 'list', $title, true );
	}
	
	protected function header( string ...$head ): void
	{
		$this -> app -> view -> head -> add( 'selected', $head );
	}
	
	protected function render( string $content = '', string | null $layout = null ): CommitRepository
	{
		$commit = $this -> getCommitInstance( compact ( 'content', 'layout' ) );
		
		$this -> app -> view -> render( $commit );
		
		return $commit;
	}
	
	protected function redirect( string $path ): CommitRepository
	{
		$commit = $this -> getCommitInstance( compact ( 'path' ) );
		
		$this -> app -> view -> redirect( $commit );
		
		return $commit;
	}
	
	protected function json( string $content = '', string | null $layout = null ): CommitRepository
	{
		$commit = $this -> getCommitInstance( compact ( 'content', 'layout' ) );
		
		$this -> app -> view -> json( $commit );
		
		return $commit;
	}
	
	protected function customJson( array $content ): CommitRepository
	{
		$commit = $this -> getCommitInstance( [ 'closure' => function ( Response $response ) use ( $content ): void
		{
			$response -> headers -> set( 'Content-Type', 'application/json' );
			
			$response -> setContent( json_encode ( $content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
		} ] );
		
		$this -> app -> view -> custom( $commit );
		
		return $commit;
	}
	
// -------------------------------------------- NEW
	public function __invoke()
	{
		return $this -> app -> repository -> get( 'viewer.include' );
	}
	
	protected function getPost(): array
	{
		return $this -> app -> request -> request -> all();
	}
	
	protected function getEncoder( UserInterface $user ): PasswordEncoderInterface
	{
		return $this -> app -> container -> get( 'encoder.factory' ) -> getEncoder( $user );
	}
	
// -------------------------------------------- NEW
	/* protected function createForm( string $type, mixed $data = null, array $options = [] ): FormInterface
	{
		return $this -> app -> container -> get( 'form.factory' ) -> getFormFactory() -> create( $type, $data, $options );
	} */

	protected function isGranted( /* ?????? */ $attribute, $subject = null ): bool
	{
		try
		{
			return $this -> app -> security -> isGranted( $attribute, $subject );
		}
		catch ( \Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException )
		{
			return false;
		}
		//return $this -> app -> container -> get( 'security.authorization_checker' ) -> isGranted( $attribute, $subject );
	}
}
