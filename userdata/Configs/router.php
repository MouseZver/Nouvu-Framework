<?php

use Nouvu\Web\Foundation\Application AS App;

return [
	'file' => 'configs/system/routing.json',
	
	'closure' => static function ( App $app ): array
	{
		$file = $app -> path( 'userdata' ) . $app -> repository -> get( 'router.file' );
		
		if ( file_exists ( $file ) )
		{
			return json_decode ( file_get_contents ( $file ), true );
		}
		
		return \Nouvu\Resources\System\RecreateRouting :: create( $file );
	},
];
