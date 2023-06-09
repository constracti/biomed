<?php

/*
 * Plugin Name: BIOMED
 * Plugin URI: https://github.com/constracti/biomed
 * Description: BIOMED customizations.
 * Version: 1.0
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

// display english portfolio category archives even in greek pages
add_action( 'parse_term_query', function( WP_Term_Query $query ): void {
	if ( !defined( 'POLYLANG_VERSION' ) )
		return;
	$qv = &$query->query_vars;
	if ( !isset( $qv['taxonomy'] ) || is_array( $qv['taxonomy'] ) && !in_array( 'portfolio_category', $qv['taxonomy'], TRUE ) )
		return;
	$qv['lang'] = '';
} );

// use post cards of any language in any page
add_action( 'parse_query', function( WP_Query $query ): void {
	if ( !defined( 'POLYLANG_VERSION' ) )
		return;
	if ( !$query->is_tax( 'element_category', 'post_cards' ) )
		return;
	$query->set( 'lang', '' );
}, 5 ); // priority 5 to precede polylang/frontend parse_query

// search for posts of any language
add_action( 'parse_query', function( WP_Query $query ): void {
	if ( !defined( 'POLYLANG_VERSION' ) )
		return;
	if ( !$query->is_search() )
		return;
	$query->set( 'lang', '' );
} );

// replace breadcrumbs home label
add_filter( 'fusion_breadcrumbs_defaults', function( array $defaults ): array {
	$home = get_option( 'page_on_front' );
	$title = get_the_title( $home );
	$defaults['home_label'] = $title;
	return $defaults;
} );

// tidy up sitemap
add_filter( 'wp_sitemaps_add_provider', function(  mixed $provider, string $name ):  mixed {
	if ( $name === 'users' )
		return NULL;
	return $provider;
}, 10, 2 );
add_filter( 'wp_sitemaps_post_types', function( array $post_types ): array {
	if ( isset( $post_types['avada_portfolio'] ) )
		unset( $post_types['avada_portfolio'] );
	return $post_types;
} );
add_filter( 'wp_sitemaps_taxonomies', function( array $taxonomies ): array {
	if ( isset( $taxonomies['post_format'] ) )
		unset( $taxonomies['post_format'] );
	if ( isset( $taxonomies['post_tag'] ) )
		unset( $taxonomies['post_tag'] );
	if ( isset( $taxonomies['portfolio_category'] ) )
		unset( $taxonomies['portfolio_category'] );
	if ( isset( $taxonomies['portfolio_skills'] ) )
		unset( $taxonomies['portfolio_skills'] );
	if ( isset( $taxonomies['portfolio_tags'] ) )
		unset( $taxonomies['portfolio_tags'] );
	return $taxonomies;
} );

// https://www.biomed.ntua.gr/new/wp-admin/admin-ajax.php?action=biomed
add_action( 'wp_ajax_biomed', function(): void {
	if ( !current_user_can( 'manage_options' ) )
		exit( 'role' );
	exit( 'ok' );
} );
