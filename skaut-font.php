<?php

/**
 * Plugin Name:       Skautské fonty
 * Plugin URI:        https://wordpress.org/plugins/skaut-font
 * Description:       Tento plugin přidává na web skautské fonty SKAUT Bold a TheMix
 * Version:           1.0.1
 * Author:            Junák - český skaut, Michal Janata
 * Author URI:        https://dobryweb.skauting.cz/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       skaut-font
  */

function lynt_skauting_styles() {
    wp_enqueue_style( 'skauting-style', plugins_url( '/style.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'lynt_skauting_styles', 11 );
