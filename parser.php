<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'biomed_tab_list', function( array $tabs ): array {
	if ( !defined( 'AVADA_VERSION' ) )
		return $tabs;
	$tabs['parser'] = 'Parser';
	return $tabs;
} );

function biomed_parser_months(): array {
	return [
		// english
		'January' => 1,
		'February' => 2,
		'March' => 3,
		'April' => 4,
		'May' => 5,
		'June' => 6,
		'July' => 7,
		'August' => 8,
		'September' => 9,
		'October' => 10,
		'November' => 11,
		'December' => 12,
		// french
		'Janvier' => 1,
		'Février' => 2,
		'Mars' => 3,
		'Avril' => 4,
		'Mai' => 5,
		'Juin' => 6,
		'Juillet' => 7,
		'Août' => 8,
		'Septembre' => 9,
		'Octobre' => 10,
		'Novembre' => 11,
		'Décembre' => 12,
		// greek
		'Ιανουάριος' => 1,
		'Φεβρουάριος' => 2,
		'Μάρτιος' => 3,
		'Απρίλιος' => 4,
		'Μάιος' => 5,
		'Ιούνιος' => 6,
		'Ιούλιος' => 7,
		'Αύγουστος' => 8,
		'Σεπτέμβριος' => 9,
		'Οκτώβριος' => 10,
		'Νοέμβριος' => 11,
		'Δεκέμβριος' => 12,
		// english short
		'Jan' => 1,
		'Feb' => 2,
		'Mar' => 3,
		'Apr' => 4,
		'May' => 5,
		'Jun' => 6,
		'Jul' => 7,
		'Aug' => 8,
		'Sep' => 9,
		'Oct' => 10,
		'Nov' => 11,
		'Dec' => 12,
		// greek genitive
		'Ιανουαρίου' => 1,
		'Φεβρουαρίου' => 2,
		'Μαρτίου' => 3,
		'Απριλίου' => 4,
		'Μαΐου' => 5,
		'Ιουνίου' => 6,
		'Ιουλίου' => 7,
		'Αυγούστου' => 8,
		'Σεπτεμβρίου' => 9,
		'Οκτωβρίου' => 10,
		'Νοεμβρίου' => 11,
		'Δεκεμβρίου' => 12,
		// greek short
		'Ιαν' => 1,
		'Φεβ' => 2,
		'Μαρ' => 3,
		'Απρ' => 4,
		'Μαι' => 5,
		'Μαϊ' => 5,
		'Ιον' => 6,
		'Ιουν' => 6,
		'Ιολ' => 7,
		'Ιουλ' => 7,
		'Αυγ' => 8,
		'Σεπ' => 9,
		'Οκτ' => 10,
		'Νοε' => 11,
		'Δεκ' => 12,
	];
}

function biomed_parser_algorithms(): array {
       return [
		'publications' => [
			'name' => 'Publications',
			'function' => function( string $row ): array {
				$months = biomed_parser_months();
				$month_pattern = implode( '|', array_keys( $months ) );
				$title = NULL;
				$month = NULL;
				$year = NULL;
				$date = NULL;
				if ( mb_ereg( '“(.*?)”', $row, $m ) )
					$title = $m[1];
				if ( is_null( $title ) && mb_ereg( '«(.*?)»', $row, $m ) )
					$title = $m[1];
				if ( is_null( $title ) && mb_ereg( '"(.*?)"', $row, $m ) )
					$title = $m[1];
				if ( is_null( $title ) && mb_ereg( '["“”«»](.*?)["“”«»]', $row, $m ) )
					$title = $m[1];
				if ( mb_ereg( '[, ]\s*(' . $month_pattern . ')[, ]?\s*(\d{4})[,. \n\]]', $row, $m ) ) {
					$month = $m[1];
					$year = $m[2];
				}
				if ( is_null( $month ) && mb_ereg( '[, ]\s*(' . $month_pattern . ')[,. \n-]', $row, $m ) ) {
					$month = $m[1];
				}
				if ( is_null( $year ) && mb_ereg( '[, (]\s*(19\d{2}|20\d{2})[,. \n)]', $row, $m ) ) {
					$year = $m[1];
				}
				if ( !is_null( $year ) ) {
					if ( is_null( $month ) )
						$month = 1;
					else
						$month = $months[$arr['month']];
					$date = sprintf( '%04d-%02d-01 00:00:00', $year, $month );
					$date = DateTime::createFromFormat( 'Y-m-d H:i:s', $date, wp_timezone() );
					$date = $date->format( 'Y-m-d H:i:s' );
				}
				return [
					'title' => $title,
					'date' => $date,
					'excerpt' => trim( $row ),
				];
			},
		],
		'dkoutsou_theses' => [
			'name' => 'Koutsouris Theses',
			'function' => function( string $row ): array {
				$title = NULL;
				$year = NULL;
				$date = NULL;
				$parts = mb_split( '[,.]', trim( $row ) );
				$p = count( $parts );
				foreach ( array_reverse( $parts ) as $part ) {
					$words = array_filter( mb_split( '\s+', $part ) );
					if ( count( $words ) > 3 )
						break;
					$p--;
				}
				if ( $p === 0 )
					$p = 1;
				$title = implode( ',', array_slice( $parts, 0, $p ) );
				$title = mb_ereg_replace( '\s*\([^)]*\)\s*$', '', $title );
				if ( mb_ereg( '[, ]\s*(\d{4})[.\n]', $row, $m ) ) {
					$year = $m[1];
				}
				if ( !is_null( $year ) ) {
					$date = sprintf( '%04d-%01-01 00:00:00', $year );
					$date = DateTime::createFromFormat( 'Y-m-d H:i:s', $date, wp_timezone() );
					$date = $date->format( 'Y-m-d H:i:s' );
				}
				return [
					'title' => $title,
					'date' => $date,
					'excerpt' => trim( $row ),
				];
			},
		],
		'gmatso_theses' => [
			'name' => 'Matsopoulos Theses',
			'function' => function( string $row ): array {
				$title = NULL;
				$year = NULL;
				$date = NULL;
				if ( mb_ereg( '“(.*?)”', $row, $m ) )
					$title = $m[1];
				if ( is_null( $title ) && mb_ereg( '"(.*?)"', $row, $m ) )
					$title = $m[1];
				if ( is_null( $title ) && mb_ereg( '["“”«»‘’](.*?)["“”«»‘’]', $row, $m ) )
					$title = $m[1];
				if ( mb_ereg( '[, ]\s*(\d{4})\.?\n', $row, $m ) ) {
					$year = $m[1];
				}
				if ( !is_null( $year ) ) {
					$date = sprintf( '%04d-%01-01 00:00:00', $year );
					$date = DateTime::createFromFormat( 'Y-m-d H:i:s', $date, wp_timezone() );
					$date = $date->format( 'Y-m-d H:i:s' );
				}
				return [
					'title' => $title,
					'date' => $date,
					'excerpt' => trim( $row ),
				];
			},
		],
		'dkoutsou_projects' => [
			'name' => 'Koutsouris Projects',
			'function' => function( string $row ): array {
				$title = NULL;
				$excerpt = NULL;
				$date = NULL;
				$meta = [];
				$row = mb_substr( $row, 0, mb_strlen( $row ) - 1 );
				$parts = mb_split( '\t', $row, 5 );
				if ( $parts[0] !== '' )
					$title = $parts[0];
				if ( $parts[1] !== '' )
					$excerpt = $parts[1];
				if ( $parts[2] !== '' )
					$meta['project_auth'] = $parts[2];
				if ( $parts[4] !== '' && mb_ereg( '(\d{2})/(\d{2})/(\d{2})-(\d{2})/(\d{2})/(\d{2})', $parts[4], $m ) ) {
					if ( intval( $m[3] ) < 90 )
						$m[3] = '20' . $m[3];
					else
						$m[3] = '19' . $m[3];
					if ( intval( $m[6] ) < 90 )
						$m[6] = '20' . $m[6];
					else
						$m[6] = '19' . $m[6];
					$start = sprintf( '%04d-%02d-%02d 00:00:00', $m[3], $m[2], $m[1] );
					$start = DateTime::createFromFormat( 'Y-m-d H:i:s', $start, wp_timezone() );
					$end = sprintf( '%04d-%02d-%02d 00:00:00', $m[6], $m[5], $m[4] );
					$end = DateTime::createFromFormat( 'Y-m-d H:i:s', $end, wp_timezone() );
					$date = $start->format( 'Y-m-d H:i:s' );
					$meta['project_date'] = $start->format( 'd/m/Y' ) . ' - ' . $end->format( 'd/m/Y' );
				}
				return [
					'title' => $title,
					'date' => $date,
					'excerpt' => $excerpt,
					'meta' => $meta,
				];
			}
		],
	];
}

add_action( 'biomed_tab_html_parser', function(): void {
	$action = NULL;
	$term = NULL;
	$rows = NULL;
	$algorithm = NULL;
	$exclude = FALSE;
	$excludeperc = NULL;
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		if ( isset( $_POST['parse'] ) )
			$action = 'parse';
		elseif ( isset( $_POST['import'] ) )
			$action = 'import';
		else
			wp_die( 'action' );
		$term = get_term( $_POST['category'], 'portfolio_category' );
		if ( is_null( $term ) )
			wp_die( 'category' );
		$rows = $_POST['rows'];
		if ( $_POST['unslash'] !== "'" )
			$rows = wp_unslash( $rows );
		$rows = mb_ereg_replace( "\r\n", "\n", $rows );
		$rows = mb_ereg_replace( "\r", "\n", $rows );
		if ( isset( $_POST['algorithm'] ) )
			$algorithm = $_POST['algorithm'];
		if ( is_null( $algorithm ) || !array_key_exists( $algorithm, biomed_parser_algorithms() ) )
			wp_die( 'algorithm' );
		if ( isset( $_POST['exclude'] ) && $_POST['exclude'] === 'on' )
			$exclude = TRUE;
		if ( !isset( $_POST['excludeperc'] ) )
			wp_die( 'excludeperc' );
		$excludeperc = intval( $_POST['excludeperc'] );
		if ( strval( $excludeperc ) !== $_POST['excludeperc'] || $excludeperc < 0 || $excludeperc > 100 )
			$excludeperc = 90;
	}
	echo '<form method="post">' . "\n";
	echo '<table class="form-table" role="presentation">' . "\n";
	echo '<tbody>' . "\n";
	echo '<tr>' . "\n";
	echo '<th scope="row">' . "\n";
	echo sprintf( '<label for="biomed_parser_category">%s</label>', esc_html( 'Category' ) ) . "\n";
	echo '</th>' . "\n";
	echo '<td>' . "\n";
	wp_dropdown_categories( [
		'show_option_none' => '&mdash;',
		'option_none_value' => '',
		'orderby' => 'name',
		'show_count' => TRUE,
		'hierarchical' => TRUE,
		'name' => 'category',
		'id' => 'biomed_parser_category',
		'selected' => $term?->term_id,
		'taxonomy' => 'portfolio_category',
		'required' => TRUE,
		'hide_empty' => FALSE,
	] );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo '<th scope="row">' . "\n";
	echo sprintf( '<label for="biomed_parser_rows">%s</label>', esc_html( 'Rows' ) ) . "\n";
	echo '</th>' . "\n";
	echo '<td>' . "\n";
	echo sprintf( '<textarea name="rows" id="biomed_parser_rows" class="large-text" rows="10">%s</textarea>', esc_html( $rows ) ) . "\n";
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo '<th scope="row">' . "\n";
	echo sprintf( '<label for="biomed_parser_algorithm">%s</label>', esc_html( 'Algorithm' ) ) . "\n";
	echo '</th>' . "\n";
	echo '<td>' . "\n";
	echo '<select name="algorithm" id="biomed_parser_algorithm" required="required">' . "\n";
	echo sprintf( '<option value="">%s</option>', esc_html( '&mdash;' ) ) . "\n";
	foreach ( biomed_parser_algorithms() as $p => $parr )
		echo sprintf( '<option value="%s"%s>%s</option>', esc_attr( $p ), selected( $p === $algorithm, display: FALSE ), esc_html( $parr['name'] ) ) . "\n";
	echo '</select>' . "\n";
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo sprintf( '<th scope="row">%s</th>', esc_html( 'Testing' ) ) . "\n";
	echo '<td>' . "\n";
	echo '<fieldset>' . "\n";
	echo '<label for="biomed_parser_exclude">' . "\n";
	echo sprintf( '<input type="checkbox" name="exclude" id="biomed_parser_exclude" value="on"%s>', checked( $exclude, display: FALSE ) ) . "\n";
	echo sprintf( '<span>%s</span>', esc_html( 'Exclude Similar Rows' ) ) . "\n";
	echo '</label>' . "\n";
	echo '<label for="biomed_parser_excludeperc">' . "\n";
	echo sprintf( '<span>%s</span>', esc_html( 'with Minimum Percentage' ) ) . "\n";
	echo sprintf( '<input type="number" name="excludeperc" id="biomed_parser_excludeperc" value="%d" min="0" max="100">', $excludeperc ) . "\n";
	echo '</label>' . "\n";
	echo '</fieldset>' . "\n";
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tbody>' . "\n";
	echo '</table>' . "\n";
	echo '<input type="hidden" name="unslash" value="\'">' . "\n";
	echo '<p>' . "\n";
	submit_button( esc_html( 'Parse' ),  name: 'parse',  wrap: FALSE );
	echo "\n";
	submit_button( esc_html( 'Import' ), name: 'import', wrap: FALSE );
	echo '</p>' . "\n";
	echo '</form>' . "\n";
	if ( $action === 'parse' || $action === 'import' ) {
		$posts = NULL;
		if ( $exclude ) {
			$posts = get_posts( [
				'post_type' => 'avada_portfolio',
				'tax_query' => [
					[
						'taxonomy' => 'portfolio_category',
						'field' => 'term_id',
						'terms' => $term->term_id,
					],
				],
				'nopaging' => TRUE,
			] );
		}
		echo '<table class="wp-list-table widefat fixed striped">' . "\n";
		echo '<thead>' . "\n";
		echo '<tr>' . "\n";
		echo sprintf( '<th class="biomed_parser_title" scope="col">%s</th>', esc_html( 'Title' ) ) . "\n";
		echo sprintf( '<th class="biomed_parser_date" scope="col">%s</th>', esc_html( 'Date' ) ) . "\n";
		echo sprintf( '<th class="biomed_parser_excerpt" scope="col">%s</th>', esc_html( 'Excerpt' ) ) . "\n";
		if ( $exclude )
			echo sprintf( '<th class="biomed_parser_similarity" scope="col">%s</th>', esc_html( 'Similarity' ) ) . "\n";
		echo sprintf( '<th class="biomed_parser_result" scope="col">%s</th>', esc_html( 'Result' ) ) . "\n";
		echo '</tr>' . "\n";
		echo '</thead>' . "\n";
		echo '<tbody>' . "\n";
		foreach ( mb_split( "\n", $rows ) as $row ) {
			if ( !str_ends_with( $row, "\n" ) )
				$row .= "\n";
			if ( $row === "\n" )
				continue;
			$arr = biomed_parser_algorithms()[$algorithm]['function']( $row );
			echo '<tr>' . "\n";
			echo sprintf( '<td class="biomed_parser_title">%s</td>', esc_html( $arr['title'] ?? '&mdash;' ) ) . "\n";
			echo sprintf( '<td class="biomed_parser_date">%s</td>', esc_html( isset( $arr['date'] ) ? explode( ' ', $arr['date'] )[0] : 	'&mdash;' ) ) . "\n";
			echo sprintf( '<td class="biomed_parser_excerpt">%s</td>', esc_html( $arr['excerpt'] ?? '&mdash;' ) ) . "\n";
			$similar = FALSE;
			if ( $exclude ) {
				echo '<td class="biomed_parser_similarity">' . "\n";
				foreach ( $posts as $post ) {
					similar_text( $arr['excerpt'], $post->post_excerpt, $perc );
					if ( $perc > $excludeperc ) {
						echo sprintf( '<a href="%s">%.0f%%</a>', get_permalink( $post ), $perc ) . "\n";
						$similar = TRUE;
					}
				}
				if ( !$similar )
					echo sprintf( '<span>%s</span>', esc_html( 'none' ) ) . "\n";
				echo '</td>' . "\n";
			}
			echo '<td class="biomed_parser_result">' . "\n";
			if ( $similar ) {
				echo sprintf( '<span>%s</span>', esc_html( 'excluded' ) ) . "\n";
			} elseif ( $action === 'import' ) {
				$p = [];
				if ( isset( $arr['title'] ) )
					$p['post_title'] = $arr['title'];
				if ( isset( $arr['date'] ) )
					$p['post_date'] = $arr['date'];
				if ( isset( $arr['excerpt'] ) )
					$p['post_excerpt'] = $arr['excerpt'];
				if ( isset( $arr['meta'] ) )
					$p['meta_input'] = $arr['meta'];
				$p['post_status'] = 'publish';
				$p['post_type'] = 'avada_portfolio';
				$p['tax_input'] = [
					'portfolio_category' => [ $term->term_id ],
				];
				wp_insert_post( $p );
				echo sprintf( '<span>%s</span>', esc_html( 'imported' ) ) . "\n";
			} else {
				echo sprintf( '<span>%s</span>', esc_html( 'ok' ) ) . "\n";
			}
			echo '</td>' . "\n";
			echo '</tr>' . "\n";
		}
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '<style>' . "\n";
		echo '.biomed_parser_title { width: 30%; }' . "\n";
		echo '.biomed_parser_date { width: 100px; }' . "\n";
		if ( $exclude )
			echo '.biomed_parser_similarity { width: 100px; }' . "\n";
		echo '.biomed_parser_result { width: 100px; }' . "\n";
		echo '</style>' . "\n";
	}
} );
