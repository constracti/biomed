<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'biomed_tab_list', function( array $tabs ): array {
	if ( !defined( 'AVADA_VERSION' ) )
		return $tabs;
	$tabs['publications'] = 'Publications';
	return $tabs;
} );

function biomed_publications_months(): array {
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

function biomed_publications_parsers(): array {
       return [
		'publications' => [
			'name' => 'Publications',
			'function' => function( string $row ): array {
				$title = NULL;
				$month = NULL;
				$year = NULL;
				$m = NULL;
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
				return [
					'title' => $title,
					'month' => $month,
					'year' => $year,
					'excerpt' => trim( $row ),
				];
			},
		],
		'dkoutsou_theses' => [
			'name' => 'Koutsouris Theses',
			'function' => function( string $row ): array {
				$title = NULL;
				$year = NULL;
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
				return [
					'title' => $title,
					'month' => NULL,
					'year' => $year,
					'excerpt' => trim( $row ),
				];
			},
		],
		'gmatso_theses' => [
			'name' => 'Matsopoulos Theses',
			'function' => function( string $row ): array {
				$title = NULL;
				$year = NULL;
				if ( mb_ereg( '“(.*?)”', $row, $m ) )
					$title = $m[1];
				if ( is_null( $title ) && mb_ereg( '"(.*?)"', $row, $m ) )
					$title = $m[1];
				if ( is_null( $title ) && mb_ereg( '["“”«»‘’](.*?)["“”«»‘’]', $row, $m ) )
					$title = $m[1];
				if ( mb_ereg( '[, ]\s*(\d{4})\.?\n', $row, $m ) ) {
					$year = $m[1];
				}
				return [
					'title' => $title,
					'month' => NULL,
					'year' => $year,
					'excerpt' => trim( $row ),
				];
			},
		],
	];
}

add_action( 'biomed_tab_html_publications', function(): void {
	$action = NULL;
	$term = NULL;
	$rows = NULL;
	$parser = NULL;
	$exclude = FALSE;
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
		if ( isset( $_POST['parser'] ) )
			$parser = $_POST['parser'];
		if ( is_null( $parser ) || !array_key_exists( $parser, biomed_publications_parsers() ) )
			wp_die( 'parser' );
		if ( isset( $_POST['exclude'] ) && $_POST['exclude'] === 'on' )
			$exclude = TRUE;
	}
	echo '<form method="post">' . "\n";
	echo '<table class="form-table" role="presentation">' . "\n";
	echo '<tbody>' . "\n";
	echo '<tr>' . "\n";
	echo '<th scope="row">' . "\n";
	echo sprintf( '<label for="biomed_publications_category">%s</label>', esc_html( 'Category' ) ) . "\n";
	echo '</th>' . "\n";
	echo '<td>' . "\n";
	wp_dropdown_categories( [
		'show_option_none' => '&mdash;',
		'option_none_value' => '',
		'orderby' => 'name',
		'show_count' => TRUE,
		'hierarchical' => TRUE,
		'name' => 'category',
		'id' => 'biomed_publications_category',
		'selected' => $term?->term_id,
		'taxonomy' => 'portfolio_category',
		'required' => TRUE,
		'hide_empty' => FALSE,
	] );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo '<th scope="row">' . "\n";
	echo sprintf( '<label for="biomed_publications_rows">%s</label>', esc_html( 'Rows' ) ) . "\n";
	echo '</th>' . "\n";
	echo '<td>' . "\n";
	echo sprintf( '<textarea name="rows" id="biomed_publications_rows" class="large-text" rows="10">%s</textarea>', esc_html( $rows ) ) . "\n";
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo '<th scope="row">' . "\n";
	echo sprintf( '<label for="biomed_publications_parser">%s</label>', esc_html( 'Parser' ) ) . "\n";
	echo '</th>' . "\n";
	echo '<td>' . "\n";
	echo '<select name="parser" id="biomed_publications_parser" required="required">' . "\n";
	echo sprintf( '<option value="">%s</option>', esc_html( '&mdash;' ) ) . "\n";
	foreach ( biomed_publications_parsers() as $p => $parr )
		echo sprintf( '<option value="%s"%s>%s</option>', esc_attr( $p ), selected( $p === $parser, display: FALSE ), esc_html( $parr['name'] ) ) . "\n";
	echo '</select>' . "\n";
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo sprintf( '<th scope="row">%s</th>', esc_html( 'Testing' ) ) . "\n";
	echo '<td>' . "\n";
	echo '<fieldset>' . "\n";
	echo '<label for="biomed_publications_exclude">' . "\n";
	echo sprintf( '<input type="checkbox" name="exclude" id="biomed_publications_exclude" value="on"%s>', checked( $exclude, display: FALSE ) ) . "\n";
	echo sprintf( '<span>%s</span>', esc_html( 'Exclude Similar Rows' ) ) . "\n";
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
		echo sprintf( '<th class="biomed_publications_title" scope="col">%s</th>', esc_html( 'Title' ) ) . "\n";
		echo sprintf( '<th class="biomed_publications_month" scope="col">%s</th>', esc_html( 'Month' ) ) . "\n";
		echo sprintf( '<th class="biomed_publications_year" scope="col">%s</th>', esc_html( 'Year' ) ) . "\n";
		echo sprintf( '<th class="biomed_publications_excerpt" scope="col">%s</th>', esc_html( 'Excerpt' ) ) . "\n";
		if ( $exclude )
			echo sprintf( '<th class="biomed_publications_similarity" scope="col">%s</th>', esc_html( 'Similarity' ) ) . "\n";
		echo sprintf( '<th class="biomed_publications_result" scope="col">%s</th>', esc_html( 'Result' ) ) . "\n";
		echo '</tr>' . "\n";
		echo '</thead>' . "\n";
		echo '<tbody>' . "\n";
		$months = biomed_publications_months();
		$month_pattern = implode( '|', array_keys( $months ) );
		foreach ( mb_split( "\n", $rows ) as $row ) {
			$row = trim( $row );
			if ( $row === '' )
				continue;
			$row .= "\n";
			$arr = biomed_publications_parsers()[$parser]['function']( $row );
			echo '<tr>' . "\n";
			echo sprintf( '<td class="biomed_publications_title">%s</td>', esc_html( $arr['title'] ?? '&mdash;' ) ) . "\n";
			echo sprintf( '<td class="biomed_publications_month">%s</td>', esc_html( $arr['month'] ?? '&mdash;' ) ) . "\n";
			echo sprintf( '<td class="biomed_publications_year">%s</td>', esc_html( $arr['year'] ?? '&mdash;' ) ) . "\n";
			echo sprintf( '<td class="biomed_publications_excerpt">%s</td>', esc_html( $arr['excerpt'] ) ) . "\n";
			$similar = FALSE;
			if ( $exclude ) {
				echo '<td class="biomed_publications_similarity">' . "\n";
				foreach ( $posts as $post ) {
					similar_text( $arr['excerpt'], $post->post_excerpt, $perc );
					if ( $perc > 95 ) {
						echo sprintf( '<a href="%s">%.0f%%</a>', get_permalink( $post ), $perc ) . "\n";
						$similar = TRUE;
					}
				}
				if ( !$similar )
					echo sprintf( '<span>%s</span>', esc_html( 'none' ) ) . "\n";
				echo '</td>' . "\n";
			}
			echo '<td class="biomed_publications_result">' . "\n";
			if ( $similar ) {
				echo sprintf( '<span>%s</span>', esc_html( 'excluded' ) ) . "\n";
			} elseif ( $action === 'import' ) {
				$p = [];
				if ( !is_null( $arr['year'] ) ) {
					if ( is_null( $arr['month'] ) )
						$arr['month'] = 1;
					else
						$arr['month'] = $months[$arr['month']];
					$dt = sprintf( '%04d-%02d-01 00:00:00', $arr['year'], $arr['month'] );
					$dt = DateTime::createFromFormat( 'Y-m-d H:i:s', $dt, wp_timezone() );
					$p['post_date'] = $dt->format( 'Y-m-d H:i:s' );
				}
				$p['post_excerpt'] = $arr['excerpt'];
				if ( !is_null( $arr['title'] ) )
					$p['post_title'] = $arr['title'];
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
		echo '.biomed_publications_title { width: 30%; }' . "\n";
		echo '.biomed_publications_month { width: 100px; }' . "\n";
		echo '.biomed_publications_year { width: 50px; }' . "\n";
		if ( $exclude )
			echo '.biomed_publications_similarity { width: 100px; }' . "\n";
		echo '.biomed_publications_result { width: 100px; }' . "\n";
		echo '</style>' . "\n";
	}
} );
