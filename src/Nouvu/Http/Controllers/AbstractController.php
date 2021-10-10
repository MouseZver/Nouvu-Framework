<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http\Controllers;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Form\FormInterface;
use Nouvu\Web\Foundation\{ Application, ApplicationTrait };
use Nouvu\Web\View\Repository\CommitRepository;
use Nouvu\Web\Resources\Model\AbstractModel;

class AbstractController
{
	private $nameModel;
	
	use ApplicationTrait;
	
	public function __construct ( protected Application $app )
	{
		$this -> nameModel = str_replace ( \Controller :: class, \Model :: class, static :: class );
	}
	
	protected function getModel(): AbstractModel
	{
		return $this -> make( $this -> nameModel, [ $this -> app ] );
	}
	
	private function getCommitInstance( array $data ): CommitRepository
	{
		if ( $this -> app -> container -> has( $this -> nameModel ) )
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
	
// -------------------------------------------- NEW
	public function __invoke()
	{
		return $this -> app -> repository -> get( 'viewer.include' );
	}
	
	protected function getPost()//: ???
	{
		return $this -> app -> request -> request;
	}
	
	protected function getEncoder( UserInterface $user ): PasswordEncoderInterface
	{
		return $this -> app -> container -> get( 'encoder.factory' ) -> getEncoder( $user );
	}
	
// -------------------------------------------- NEW
	protected function createForm( string $type, mixed $data = null, array $options = [] ): FormInterface
	{
		return $this -> app -> container -> get( 'form.factory' ) -> getFormFactory() -> create( $type, $data, $options );
	}

	protected function isGranted( /* ?????? */ $attribute, $subject = null ): bool
	{
		return $this -> app -> container -> get( 'security.authorization_checker' ) -> isGranted( $attribute, $subject );
	}
}
