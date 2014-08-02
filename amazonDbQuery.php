<?php
/**
 * Amazon DB Query plugin
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   amazonDbQuery
 * @author    Collective Fruitions <info@collectivefruitions.com>
 * @license   GPL-3.0+
 * @link      http://www.collectivefruitions.com/
 *
 * @wordpress-plugin
 * Plugin Name:       Amazon DB Query plugin
 * Plugin URI:        http://www.collectivefruitions.com/
 * Version:           0.0.1
 * Author:            Collective Fruitions
 * Author URI:        http://www.collectivefruitions.com/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'public/amazonDbQuery-public.php' );

register_activation_hook( __FILE__, array( 'amazonDbQuery', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'amazonDbQuery', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'amazonDbQuery', 'get_instance' ) );
