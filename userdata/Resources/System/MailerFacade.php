<?php

declare ( strict_types = 1 );

namespace Nouvu\Resources\System;

use Nouvu\Web\Foundation\Application AS App;
use PHPMailer\PHPMailer\{ SMTP, PHPMailer };

final class MailerFacade
{
	public function __construct ( App $app )
	{
		$this -> mail = new PHPMailer( true );
		
		if ( $app -> repository -> get( 'config.mailer.debug' ) )
		{
			$this -> mail -> SMTPDebug = SMTP :: DEBUG_SERVER;
		}
		
		if ( $app -> repository -> get( 'config.mailer.smtp' ) )
		{
			$this -> mail -> isSMTP();
		}
		
		foreach ( $app -> repository -> get( 'config.mailer.host' ) AS $key => $value )
		{
			$this -> mail -> {$key} = $value;
		}
		
		$this -> mail -> isHTML( $app -> repository -> get( 'config.mailer.html' ) );
		
		if ( $app -> repository -> has( 'config.mailer.from' ) )
		{
			$this -> setFrom( ...$app -> repository -> get( 'config.mailer.from' ) );
		}
	}
	
	public function setFrom( string ...$string ): void
	{
		$this -> mail -> setFrom( ...$string );
	}
	
	public function setTo( string ...$string ): void
	{
		$this -> mail -> addCC( ...$string );
	}
	
	public function setSubject( string $name ): void
	{
		$this -> mail -> Subject = $name;
	}
	
	public function setContent( string $content ): void
	{
		$this -> mail -> Body = $content;
	}
	
	public function send(): bool
	{
		return $this -> mail -> send();
	}
	
	public function getInstance(): PHPMailer
	{
		return $this -> mail;
	}
}