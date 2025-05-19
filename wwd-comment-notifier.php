<?php
namespace whatwedo\CommentNotifier;

/*
	Plugin Name: Whatwedo Comment Notifier
	Description: Very simple subscribe to Comments and get notified. Nothing more.
	Version: 1.0.0
	Author: Whatwedo (Marc Wieland) - August 2018
	Author URI: https://whatwedo.ch
*/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Constants
 */

define( 'WWDCN_NAME', 			'wwd-comment-notifier' );

// Path
define( 'WWDCN_DIR', 			plugin_dir_path( __FILE__ ) );
define( 'WWDCN_DIR_URL', 		plugin_dir_url( __FILE__ ) );

// On activation hook - create db table
register_activation_hook( __FILE__, array( Bootstrap::class, 'activate' ) );

/*
	SPL Autoloading (included in PHP)
 */

spl_autoload_register( function( $class ) {

	$namespace = 'whatwedo\CommentNotifier\\';
    if (strpos($class, $namespace) !== 0) {
        return;
    }

    $path = explode('\\', strtolower(str_replace('whatwedo\\CommentNotifier\\', '', $class)));
    $path[] = 'class-' . array_pop($path);

    $file = plugin_dir_path( __FILE__ ) . 'class/' . implode(DIRECTORY_SEPARATOR, $path) . '.php';

	require( $file );

});


/**
 * Init Classes
 */

new Bootstrap();
new WP();
