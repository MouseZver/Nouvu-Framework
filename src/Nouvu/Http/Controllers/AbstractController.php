<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http\Controllers;

use Nouvu\Web\Foundation\Application AS App;
use Nouvu\Web\Foundation\User\AbstractModel;
use Nouvu\Web\View\Repository\CommitRepository;

class AbstractController
{
	private $nameModel;
	
	public function __construct ( protected App $app )
	{
		$this -> nameModel = str_replace ( \Controller :: class, \Model :: class, static :: class );
	}
	
	protected function getModel(): AbstractModel
	{
		return $app -> make( $this -> nameModel, [ $app ] );
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
	
	// {<{locale}>}
	public function getLocale(): string
	{
		return $this -> app -> getLocale();
	}

// -------------------------------------------- NEW
	protected function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }
	
	protected function isGranted($attribute, $subject = null): bool
    {
        if (!$this->container->has('security.authorization_checker')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        return $this->container->get('security.authorization_checker')->isGranted($attribute, $subject);
    }
}
