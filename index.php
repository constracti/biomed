<?php

/*
 * Plugin Name: BIOMED
 * Plugin URI: https://github.com/constracti/biomed
 * Description: BIOMED customizations.
 * Version: 0.4
 * Requires PHP: 8.0
 * Author: constracti
 * Author URI: https://github.com/constracti
 * Licence: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( !defined( 'ABSPATH' ) )
	exit;

function biomed_dir( string $dir ): string {
	return plugin_dir_path( __FILE__ ) . $dir;
}

( function(): void {
	$files = glob( biomed_dir( '*.php' ) );
	foreach ( $files as $file ) {
		if ( $file !== __FILE__ )
			require_once( $file );
	}
} )();

add_post_type_support( 'page', 'excerpt' );

add_action( 'pre_get_posts', function( WP_Query $query ): void {
	if ( is_admin() )
		return;
	if ( !$query->is_main_query() )
		return;
	$cats = [
		'people',
		'professors',
		'teaching-staff',
		'researchers',
		'phd-candidates',
		'administrative-support',
		'prosopiko',
		'kathigites',
		'didaktiko-prosopiko',
		'erevnites',
		'ypopsifioi-didaktores',
		'diacheiristiki-ypostirixi',
	];
	if ( !$query->is_tax( 'portfolio_category', $cats ) )
		return;
	$query->set( 'orderby', 'title' );
	$query->set( 'order', 'ASC' );
} );
