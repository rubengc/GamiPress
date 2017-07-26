<?php
/**
 * RNL AUTOLOADER
 * Looks in /code to see if a file matching the classname exists
 *
 * @param $class
 *
 * @since 1.2    Added test to see if function has been declared already.
 * @since 1.0.0
 */
if( ! function_exists( 'rnl_autoloader' ) ) {
	
	function rnl_autoloader( $class ) {
		
		$base_dir = __DIR__;
		$class    = strtolower( $class );
		$file     = $base_dir . '/code/' . $class . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
}