<?php

/**
 * Plugin Name:       Skautské fonty
 * Plugin URI:        https://github.com/skaut/skaut-font/
 * Description:       Tento plugin přidává na web skautské fonty SKAUT Bold a TheMix
 * Version:           1.2
 * Author:            David Odehnal
 * Author URI:        https://davidodehnal.cz/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       skaut-font
 */

namespace Skautfont;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SKAUTFONT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SKAUTFONT_PATH', plugin_dir_path( __FILE__ ) );
define( 'SKAUTFONT_URL', plugin_dir_url( __FILE__ ) );
define( 'SKAUTFONT_NAME', 'skaut-font' );
define( 'SKAUTFONT_VERSION', '1.0.1' );

require SKAUTFONT_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class Skautfont {

	public function __construct() {
		$this->initHooks();

		// if incompatible version of WP / PHP or deactivating plugin right now => don´t init
		if ( ! $this->isCompatibleVersionOfWp() ||
		     ! $this->isCompatibleVersionOfPhp() ||
		     ( isset( $_GET['action'], $_GET['plugin'] ) &&
		       'deactivate' == $_GET['action'] &&
		       SKAUTFONT_PLUGIN_BASENAME == $_GET['plugin'] )
		) {
			return;
		}
		$this->init();
	}

	protected function initHooks() {
		add_action( 'admin_init', [ $this, 'checkVersionAndPossiblyDeactivatePlugin' ] );

		register_activation_hook( __FILE__, [ $this, 'activation' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivation' ] );
		register_uninstall_hook( __FILE__, [ __CLASS__, 'uninstall' ] );
	}

	protected function init() {
		$frontend = new Frontend( [
			'themix'    => __( 'TheMix', 'skaut-font' ),
			'skautbold' => __( 'SKAUT Bold', 'skaut-font' )
		], [
			'body'      => __( 'Výchozí', 'skaut-font' ),
			'titles'    => __( 'Nadpisy', 'skaut-font' ),
			'site-desc' => __( 'Popis webu', 'skaut-font' )
		] );

		if ( is_admin() ) {
			( new Admin( $frontend ) );
		}
	}

	protected function isCompatibleVersionOfWp() {
		if ( isset( $GLOBALS['wp_version'] ) && version_compare( $GLOBALS['wp_version'], '4.8', '>=' ) ) {
			return true;
		}

		return false;
	}

	protected function isCompatibleVersionOfPhp() {
		if ( version_compare( PHP_VERSION, '7.0', '>=' ) ) {
			return true;
		}

		return false;
	}

	public function activation() {
		if ( ! $this->isCompatibleVersionOfWp() ) {
			deactivate_plugins( SKAUTFONT_PLUGIN_BASENAME );
			wp_die( __( 'Plugin skaut-font vyžaduje verzi WordPress 4.8 nebo vyšší!', 'skaut-font' ) );
		}

		if ( ! $this->isCompatibleVersionOfPhp() ) {
			deactivate_plugins( SKAUTFONT_PLUGIN_BASENAME );
			wp_die( __( 'Plugin skaut-font vyžaduje verzi PHP 7.0 nebo vyšší!', 'skautis-integration' ) );
		}

		if ( ! get_option( SKAUTFONT_NAME . '_style_body' ) ) {
			add_option( SKAUTFONT_NAME . '_style_body', 'themix' );
		}

		if ( ! get_option( SKAUTFONT_NAME . '_style_titles' ) ) {
			add_option( SKAUTFONT_NAME . '_style_titles', 'skautbold' );
		}

		if ( ! get_option( SKAUTFONT_NAME . '_style_site-desc' ) ) {
			add_option( SKAUTFONT_NAME . '_style_site-desc', 'themix' );
		}
	}

	public function deactivation() {
		return true;
	}

	public static function uninstall() {
		global $wpdb;
		$options = $wpdb->get_results( $wpdb->prepare( "
SELECT `option_name`
FROM $wpdb->options
WHERE `option_name` LIKE %s
", SKAUTFONT_NAME . '_%' ) );
		foreach ( $options as $option ) {
			delete_option( $option->option_name );
		}

		return true;
	}

	public function checkVersionAndPossiblyDeactivatePlugin() {
		if ( ! $this->isCompatibleVersionOfWp() ) {
			if ( is_plugin_active( SKAUTFONT_PLUGIN_BASENAME ) ) {

				deactivate_plugins( SKAUTFONT_PLUGIN_BASENAME );

				Helpers::showAdminNotice( esc_html__( 'Plugin skaut-font vyžaduje verzi WordPress 4.8 nebo vyšší!', 'skaut-font' ), 'warning' );

				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}

		if ( ! $this->isCompatibleVersionOfPhp() ) {
			if ( is_plugin_active( SKAUTFONT_PLUGIN_BASENAME ) ) {

				deactivate_plugins( SKAUTFONT_PLUGIN_BASENAME );

				Helpers::showAdminNotice( esc_html__( 'Plugin skaut-font vyžaduje verzi PHP 7.0 nebo vyšší!', 'skaut-font' ), 'warning' );

				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}
	}

}

global $skautfont;
$skautfont = new Skautfont();
