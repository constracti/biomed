<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'manage_avada_portfolio_posts_columns', function( array $post_columns ): array {
	$columns = [];
	foreach ( $post_columns as $key => $val ) {
		$columns[$key] = $val;
		if ( $key === 'author' )
			$columns['portfolio_category'] = esc_html__( 'Portfolio Categories', 'fusion-core' );
	}
	return $columns;
} );

add_action( 'manage_avada_portfolio_posts_custom_column', function( string $column_name, int $post_id ): void {
	if ( $column_name !== 'portfolio_category' )
		return;
	$term_list = get_the_terms( $post_id, $column_name );
	if ( $term_list === FALSE )
		return;
	if ( is_wp_error( $term_list ) )
		return;
	$term_list = array_map( function( WP_Term $term ): string {
		return sprintf( '<a href="%s">%s</a>', admin_url( add_query_arg( [
			'post_type' => 'avada_portfolio',
			$term->taxonomy => $term->slug,
		], 'edit.php' ) ), esc_html( $term->name ) );
	}, $term_list );
	echo implode( ', ', $term_list );
}, 10, 2 );

add_action( 'restrict_manage_posts', function( string $post_type, string $which ): void {
	if ( $post_type !== 'avada_portfolio' )
		return;
	wp_dropdown_categories( [
		'show_option_all' => esc_html( 'All Portfolio Categories', 'biomed' ),
		'orderby' => 'name',
		'show_count' => TRUE,
		'hierarchical' => TRUE,
		'name' => 'portfolio_category',
		'selected' => isset( $_GET['portfolio_category'] ) ? $_GET['portfolio_category'] : 0,
		'value_field' => 'slug',
		'taxonomy' => 'portfolio_category',
		'hide_empty' => FALSE,
	] );
}, 10, 2 );
