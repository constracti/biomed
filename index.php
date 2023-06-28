<?php

/*
 * Plugin Name: BIOMED
 * Plugin URI: https://github.com/constracti/biomed
 * Description: BIOMED customizations.
 * Version: 0.5
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

add_action( 'admin_menu', function(): void {
	add_management_page( 'BIOMED', 'BIOMED', 'manage_options', 'biomed', function(): void {
		$tab_curr = isset( $_GET['tab'] ) ? $_GET['tab'] : NULL;
		$tab_init = NULL;
		echo '<div class="wrap">' . "\n";
		echo sprintf( '<h1>%s</h1>', esc_html( 'BIOMED' ) ) . "\n";
		echo '<h2 class="nav-tab-wrapper">' . "\n";
		foreach ( apply_filters( 'biomed_tab_list', [] ) as $tab_slug => $tab_name ) {
			if ( is_null( $tab_init ) )
				$tab_init = $tab_slug;
			if ( is_null( $tab_curr ) )
				$tab_curr = $tab_init;
			$class = [];
			$class[] = 'nav-tab';
			if ( $tab_slug === $tab_curr )
				$class[] = 'nav-tab-active';
			$class = implode( ' ', $class );
			$href = menu_page_url( 'biomed', FALSE );
			if ( $tab_slug !== $tab_init )
				$href = add_query_arg( 'tab', $tab_slug, $href );
			echo sprintf( '<a class="%s" href="%s">%s</a>', esc_attr( $class ), esc_url_raw( $href ), esc_html( $tab_name ) ) . "\n";
		}
		echo '</h2>' . "\n";
		if ( $tab_curr !== NULL )
			do_action( 'biomed_tab_html_' . $tab_curr );
		echo '</div>' . "\n";
	} );
} );

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

// https://www.biomed.ntua.gr/new/wp-admin/admin-ajax.php?action=biomed
add_action( 'wp_ajax_biomed', function(): void {
	if ( !current_user_can( 'manage_options' ) )
		exit( 'role' );
	exit( 'ok' );
} );
