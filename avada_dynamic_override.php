<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'fusion_dynamic_override', function( array $out, $dynamic_arg, string $id, $shortcode, string $value ): array {
	if ( $id !== 'background_image' )
		return $out;
	if ( $dynamic_arg['data'] !== 'user_avatar' )
		return $out;
	if ( $dynamic_arg['size'] !== 'biomed' )
		return $out;
	$value = do_shortcode( $dynamic_arg['fallback'] );
	$out[$id] = $value;
	return $out;
}, 10, 5 );
