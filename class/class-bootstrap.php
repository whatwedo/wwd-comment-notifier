<?php
namespace whatwedo\CommentNotifier;

/**
 * Bootstrap
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class Bootstrap {

	public function __construct() {

		add_action( 'init', array( $this, 'config_loader' ) );

	}


	/*
		Config loader
	 */

	public function config_loader() {

		Config::load_config();

	}


	/*
		Upon activation
	 */

	public static function activate() {

		self::create_db_table();

	}


	/*
		Setup the database table
	 */

	protected static function create_db_table() {

		global $wpdb;

		// Create table unless it exists from plugin
		$sql = 'CREATE TABLE if NOT EXISTS ' . $wpdb->prefix . Config::DBTABLE . ' (
			id int unsigned NOT NULL AUTO_INCREMENT,
			post_id int unsigned NOT NULL DEFAULT 0,
			name varchar(100) NOT NULL DEFAULT \'\',
			email varchar(100) NOT NULL DEFAULT \'\',
			token varchar(50) NOT NULL DEFAULT \'\',
			submitted timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY post_id_email (post_id,email),
			KEY token (token)
		)';

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
       	dbDelta($sql);

	}


	/*
		Default settings
	 */

	protected function default_settings() {

	}

}
