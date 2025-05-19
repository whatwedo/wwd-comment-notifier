<?php
namespace whatwedo\CommentNotifier;

/**
 * Read config
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class Config {

	const DBTABLE = 'wwdcn_comment_notifier';
	const CHECKBOX_ID = 'wwdcn-checkbox-subscription';
	const PREFIX = 'wwdcn';

	protected static $config = null;

	/*
		Get
	 */

	public static function get_dir_type( $type = null ) {

		switch ($type) {
			case 'asset':
				return WWDCN_DIR . 'assets/';
				break;

			case 'class':
				return WWDCN_DIR . 'class/';
				break;

			case 'config':
				return WWDCN_DIR . 'config/';
				break;

			case 'inc':
				return WWDCN_DIR . 'inc/';
				break;

			default:
				return WWDCN_DIR;
				break;
		}

	}


	/*
		Get config
	 */

	public static function get( $key, $uncached = false ) {

		// Check if settings are forced to load uncached
		if ( $uncached === true ) {

			$config = self::load_config( true );

		} elseif ( empty( $config ) ) {

			$config = self::load_config();

		}

		$parts = self::dot_notation( $key );

		// Load according config data
		if ( sizeof( $parts ) > 1 ) {
			$config_data = self::nested_object( $config, $parts );
		} else {
			$config_data = $config->$key;
		}

		return $config_data;

	}


	/*
		Main point to load config from json
	 */

	public static function load_config( $cache = true ) {

		// Check if config was not loaded before or cache is disabed
		if ( $cache === false ) {

			return self::load_from_file();

		// When config array doesn't exists read from file
		} elseif ( is_null( static::$config ) ) {

			$config = self::load_from_file();
			static::$config = $config;

		}

		// Load from runtime
		return static::$config;

	}


	/*
		Load from json file
	 */

	protected static function load_from_file() {

		$default_config = self::prepare_config_data( self::get_dir_type( 'config' ) . 'config-' . self::PREFIX . '.json' );

		// Add second json file to override settings via hook
		$main_config = [];
		$main_config_path = apply_filters( self::PREFIX . '_config_path', false );
		if ( $main_config_path ) {
			$main_config = self::prepare_config_data( $main_config_path );
		}

		return self::merge_config( $default_config, $main_config );

	}


	/*
		Merge default config with custom once
	 */

	protected static function merge_config( $default, $main ) {

		$array_merged = array_replace_recursive( $default, $main );

		return $array_merged;

	}


	/*
		Load and json decode config
	 */

	protected static function prepare_config_data( $file ) {

		$options = file_get_contents( $file );
		$decoded = json_decode( $options, true );

		if ( is_null( $decoded ) ) {
			// Log Error
			wp_die( 'Config could not be encoded' );
		} else {

			return $decoded;

		}

	}


	/*
		Create array from dot notated string
	 */

	protected static function dot_notation( $string ) {

		return explode( '.', $string );

	}


	/*
		Look inside nested object
	 */

	protected static function nested_object( $object, $parts ) {

		foreach ( $parts as $part ) {

			// Check if key exists
			if ( isset( $object[ $part ] ) ) {
				$object = $object[ $part ];
			} else {
				// Return null when undefined - not existent
				return null;
			}

		}

		return $object;

	}

}
