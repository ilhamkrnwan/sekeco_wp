<?php
/**
 * Component blocks and their shared render helpers.
 *
 * Every component is a dynamic block built to `build/blocks/<name>`; this file
 * registers them from one list and holds the helpers that more than one
 * render.php needs (project cards, queries, icons in markup).
 *
 * @package Sekeco
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block directories (under build/blocks) registered by this module.
 *
 * @return string[]
 */
function sekeco_component_blocks() {
	return apply_filters(
		'sekeco_component_blocks',
		array(
			'section',
			'section-header',
			'hero',
			'statement',
			'marquee',
			'stats',
			'stat',
			'capabilities',
			'capability',
			'process',
			'process-step',
			'faq',
			'faq-item',
			'testimonials',
			'testimonial',
			'cta',
			'project-grid',
			'facts',
			'theme-toggle',
			'search',
			'scroll-top',
			'toc',
		)
	);
}

/**
 * Register the component blocks from their build metadata.
 *
 * @return void
 */
function sekeco_register_component_blocks() {
	foreach ( sekeco_component_blocks() as $name ) {
		$dir = SEKECO_PATH . 'build/blocks/' . $name;

		if ( file_exists( $dir . '/block.json' ) ) {
			register_block_type( $dir );
		}
	}
}
add_action( 'init', 'sekeco_register_component_blocks' );

/**
 * Version a block's assets by its build time instead of the fixed block.json
 * version, so a rebuilt stylesheet is never hidden by a cached copy.
 *
 * @param array $metadata Block metadata.
 * @return array
 */
function sekeco_block_asset_version( $metadata ) {
	if ( isset( $metadata['name'], $metadata['file'] ) && 0 === strpos( $metadata['name'], 'sekeco/' ) ) {
		$dir   = dirname( $metadata['file'] );
		$built = max( (int) @filemtime( $metadata['file'] ), (int) @filemtime( $dir . '/style-index.css' ), (int) @filemtime( $dir . '/index.js' ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		if ( $built ) {
			$metadata['version'] = SEKECO_VERSION . '.' . $built;
		}
	}

	return $metadata;
}
add_filter( 'block_type_metadata', 'sekeco_block_asset_version' );

/**
 * Post types the project grid can list.
 *
 * @return string[]
 */
function sekeco_project_post_types() {
	return array( 'portfolio', 'product', 'team' );
}

/**
 * Split "Client: Descriptor" titles into a name and a descriptor.
 *
 * @param string $title Post title.
 * @return string[] Two items: name, descriptor.
 */
function sekeco_split_title( $title ) {
	$title = wp_strip_all_tags( html_entity_decode( (string) $title, ENT_QUOTES, 'UTF-8' ) );
	$pos   = strpos( $title, ':' );

	if ( false === $pos || $pos < 2 ) {
		return array( trim( $title ), '' );
	}

	return array( trim( substr( $title, 0, $pos ) ), trim( substr( $title, $pos + 1 ) ) );
}

/**
 * The facts of one entry, in reading order, with empty fields left out.
 *
 * Each fact is array( label, type, value, ... ): type `text`, `status`, `chips`
 * (items), or `link` (url, value = host, label = what the link is).
 *
 * @param WP_Post $post Entry.
 * @return array<int,array<string,mixed>>
 */
function sekeco_entry_facts( $post ) {
	$meta  = static function ( $key ) use ( $post ) {
		return trim( (string) get_post_meta( $post->ID, $key, true ) );
	};
	$list  = static function ( $value ) {
		return array_values( array_filter( array_map( 'trim', preg_split( '/[,\/|·]+/', (string) $value ) ) ) );
	};
	$facts = array();
	$text  = static function ( $label, $value, $type = 'text' ) use ( &$facts ) {
		if ( '' !== $value ) {
			$facts[] = array(
				'label' => $label,
				'type'  => $type,
				'value' => $value,
			);
		}
	};
	$chips = static function ( $label, $items ) use ( &$facts ) {
		if ( $items ) {
			$facts[] = array(
				'label' => $label,
				'type'  => 'chips',
				'value' => implode( ', ', $items ),
				'items' => $items,
			);
		}
	};
	$link  = static function ( $label, $url ) use ( &$facts ) {
		$url  = esc_url_raw( $url );
		$host = $url ? preg_replace( '#^www\.#', '', (string) wp_parse_url( $url, PHP_URL_HOST ) ) : '';

		if ( '' !== $url && '' !== $host ) {
			$facts[] = array(
				'label' => $label,
				'type'  => 'link',
				'value' => $host,
				'url'   => $url,
			);
		}
	};

	switch ( $post->post_type ) {
		case 'portfolio':
			$text( __( 'Client', 'sekeco' ), $meta( 'sekeco_portfolio_client' ) );
			$text( __( 'Industry', 'sekeco' ), $meta( 'sekeco_portfolio_industry' ) );
			$chips( __( 'Services', 'sekeco' ), $list( $meta( 'sekeco_portfolio_services' ) ) );
			$text( __( 'Role', 'sekeco' ), $meta( 'sekeco_portfolio_role' ) );

			$people = array();

			foreach ( $list( $meta( 'sekeco_portfolio_team' ) ) as $name ) {
				$member   = get_page_by_title( $name, OBJECT, 'team' );
				$people[] = array(
					'name' => $name,
					'url'  => $member && 'publish' === $member->post_status ? get_permalink( $member ) : '',
				);
			}

			if ( $people ) {
				$facts[] = array(
					'label' => __( 'Team', 'sekeco' ),
					'type'  => 'people',
					'value' => implode( ', ', wp_list_pluck( $people, 'name' ) ),
					'items' => $people,
				);
			}

			$text( __( 'Duration', 'sekeco' ), $meta( 'sekeco_portfolio_duration' ) );
			$text( __( 'Year', 'sekeco' ), $meta( 'sekeco_portfolio_year' ) );
			$chips( __( 'Stack', 'sekeco' ), $list( $meta( 'sekeco_portfolio_stack' ) ) );
			$text( __( 'Outcome', 'sekeco' ), $meta( 'sekeco_portfolio_outcome' ), 'note' );
			$link( __( 'Project', 'sekeco' ), $meta( 'sekeco_portfolio_url' ) );
			$link( __( 'Case study', 'sekeco' ), $meta( 'sekeco_portfolio_case_study' ) );
			break;

		case 'product':
			$text( __( 'Type', 'sekeco' ), $meta( 'sekeco_product_type' ) );
			$text( __( 'Status', 'sekeco' ), $meta( 'sekeco_product_status' ), 'status' );
			$text( __( 'Version', 'sekeco' ), $meta( 'sekeco_product_version' ) );
			$chips( __( 'Platform', 'sekeco' ), $list( $meta( 'sekeco_product_platform' ) ) );
			$chips( __( 'Stack', 'sekeco' ), $list( $meta( 'sekeco_product_stack' ) ) );
			$text( __( 'Year', 'sekeco' ), $meta( 'sekeco_product_year' ) );
			$text( __( 'Repository', 'sekeco' ), $meta( 'sekeco_product_repo' ) );
			$link( __( 'Website', 'sekeco' ), $meta( 'sekeco_product_url' ) );
			$link( __( 'Docs', 'sekeco' ), $meta( 'sekeco_product_docs' ) );
			$link( __( 'Demo', 'sekeco' ), $meta( 'sekeco_product_demo' ) );
			$link( __( 'Download', 'sekeco' ), $meta( 'sekeco_product_download' ) );
			$link( __( 'Changelog', 'sekeco' ), $meta( 'sekeco_product_changelog' ) );
			break;

		case 'team':
			$link( 'GitHub', $meta( 'sekeco_team_github' ) );
			$link( 'LinkedIn', $meta( 'sekeco_team_linkedin' ) );
			$link( 'Instagram', $meta( 'sekeco_team_instagram' ) );
			break;
	}

	return $facts;
}

/**
 * One plain sentence about an entry: its excerpt, or the opening paragraph.
 *
 * @param WP_Post $post  Entry.
 * @param int     $words Maximum words.
 * @return string
 */
function sekeco_entry_summary( $post, $words = 22 ) {
	if ( post_password_required( $post ) ) {
		return '';
	}

	if ( has_excerpt( $post ) ) {
		$text = $post->post_excerpt;
	} elseif ( preg_match( '#<p[^>]*>(.*?)</p>#is', (string) $post->post_content, $match ) ) {
		$text = $match[1];
	} else {
		$text = '';
	}

	$text = trim( preg_replace( '/\s+/', ' ', html_entity_decode( wp_strip_all_tags( strip_shortcodes( $text ) ), ENT_QUOTES, 'UTF-8' ) ) );

	return wp_trim_words( $text, $words, '…' );
}

/**
 * Markup of one catalog row: name and what it does, type, status, year.
 *
 * @param WP_Post $post Entry.
 * @return string
 */
function sekeco_render_catalog_row( $post ) {
	list( $name, $detail ) = sekeco_split_title( get_the_title( $post ) );

	$is_product = 'product' === $post->post_type;
	$type       = $is_product ? (string) get_post_meta( $post->ID, 'sekeco_product_type', true ) : '';
	$status     = $is_product ? (string) get_post_meta( $post->ID, 'sekeco_product_status', true ) : '';
	$year       = (string) get_post_meta( $post->ID, $is_product ? 'sekeco_product_year' : 'sekeco_portfolio_year', true );
	$summary    = '' !== $detail ? $detail : sekeco_entry_summary( $post );

	ob_start();
	?>
	<a class="sk-row sk-row--catalog" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
		<span class="sk-catalog__main">
			<span class="sk-row__name"><?php echo esc_html( $name ); ?></span>
			<?php if ( '' !== $summary ) : ?>
				<span class="sk-catalog__summary"><?php echo esc_html( $summary ); ?></span>
			<?php endif; ?>
		</span>
		<span class="sk-catalog__type"><?php echo esc_html( $type ); ?></span>
		<span class="sk-catalog__status<?php echo 'live' === strtolower( $status ) ? ' is-live' : ''; ?>"><?php echo esc_html( $status ); ?></span>
		<span class="sk-row__year"><?php echo esc_html( $year ); ?></span>
		<span class="sk-row__go"><?php echo sekeco_icon( 'arrow-right-up', array( 'size' => 22 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	</a>
	<?php
	return (string) ob_get_clean();
}

/**
 * Width class for a block's Content width: narrow (theme contentSize), default,
 * wide (theme wideSize) or full (the whole screen). Default adds no class.
 *
 * @param array $attributes Block attributes.
 * @return string Class name with a leading space, or an empty string.
 */
function sekeco_width_class( $attributes ) {
	$width = isset( $attributes['width'] ) ? $attributes['width'] : 'default';

	return in_array( $width, array( 'narrow', 'wide', 'full' ), true ) ? ' sk-w--' . $width : '';
}

/**
 * Background picture of a Hero or CTA: the class that marks it and the canvas
 * the front-end script paints on. Only the pictures the editor offers are used.
 *
 * @param array $attributes Block attributes.
 * @return array{0:string,1:string} Class name with a leading space, canvas markup.
 */
function sekeco_background( $attributes ) {
	$bg = isset( $attributes['bg'] ) ? $attributes['bg'] : 'none';

	if ( ! in_array( $bg, array( 'satin', 'dusk', 'aurora', 'nebula' ), true ) ) {
		return array( '', '' );
	}

	return array(
		' sk-bg-on sk-bg--' . $bg,
		'<canvas class="sk-bg" data-sk-bg="' . esc_attr( $bg ) . '" aria-hidden="true"></canvas>',
	);
}

/**
 * Stable 1-4 tone index so a post keeps the same cover colour.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function sekeco_card_tone( $post_id ) {
	return ( (int) $post_id % 4 ) + 1;
}

/**
 * Initials used as the cover mark of a person without a portrait.
 *
 * @param string $name Full name.
 * @return string
 */
function sekeco_initials( $name ) {
	$words    = preg_split( '/\s+/', trim( $name ) );
	$initials = '';

	foreach ( array_slice( (array) $words, 0, 2 ) as $word ) {
		$initials .= function_exists( 'mb_substr' ) ? mb_substr( $word, 0, 1 ) : substr( $word, 0, 1 );
	}

	return strtoupper( $initials );
}

/**
 * Query published entries for a project grid.
 *
 * @param string $post_type Post type slug.
 * @param int    $count     Number of entries.
 * @param int    $term_id   Optional category ID.
 * @return WP_Post[]
 */
function sekeco_query_projects( $post_type, $count, $term_id = 0 ) {
	if ( ! in_array( $post_type, sekeco_project_post_types(), true ) ) {
		return array();
	}

	$args = array(
		'post_type'           => $post_type,
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, min( 200, (int) $count ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	// On the entry's own page, "more" never lists the entry itself.
	if ( is_singular( $post_type ) ) {
		$args['post__not_in'] = array( get_queried_object_id() );
	}

	if ( 'team' === $post_type ) {
		$args['orderby'] = array(
			'menu_order' => 'ASC',
			'date'       => 'ASC',
		);
	}

	if ( $term_id > 0 ) {
		$args['cat'] = (int) $term_id;
	}

	return get_posts( $args );
}

/**
 * Whether the featured image file of a post is still present.
 *
 * Only local uploads are checked; offloaded or remote media is assumed present.
 *
 * @param WP_Post $post Post object.
 * @return bool
 */
function sekeco_thumbnail_file_exists( $post ) {
	$id = get_post_thumbnail_id( $post );
	$url = $id ? wp_get_attachment_url( $id ) : '';

	if ( ! $url ) {
		return false;
	}

	$uploads = wp_get_upload_dir();

	if ( 0 !== strpos( $url, $uploads['baseurl'] ) ) {
		return true;
	}

	$file = get_attached_file( $id );

	return $file && file_exists( $file );
}

/**
 * Markup of one project, product, or team card.
 *
 * Entries without a featured image get a typographic cover: the name set large
 * on one of four palette fields, so a grid never shows empty boxes.
 *
 * @param WP_Post|int $post Post object or ID.
 * @param array       $args Optional: eager (bool, also raises fetch priority), size (string), sizes (string, the img sizes attribute for the card's layout), variant ('cover' keeps a team member on the overlay cover card instead of the portrait card).
 * @return string
 */
function sekeco_render_project_card( $post, $args = array() ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'eager'   => false,
			'size'    => 'large',
			'variant' => '',
			'sizes'   => '',
		)
	);

	$type                  = $post->post_type;
	list( $name, $detail ) = sekeco_split_title( get_the_title( $post ) );
	$thumb_attr            = array(
		'class'    => 'sk-card__img',
		'alt'      => '',
		'loading'  => $args['eager'] ? 'eager' : 'lazy',
		'decoding' => 'async',
	);

	if ( '' !== $args['sizes'] ) {
		$thumb_attr['sizes'] = $args['sizes'];
	}

	if ( $args['eager'] ) {
		$thumb_attr['fetchpriority'] = 'high';
	}

	$thumb_html = get_the_post_thumbnail( $post, $args['size'], $thumb_attr );
	// A thumbnail whose file is gone from local uploads is not an image: fall back to the cover.
	$has_image = '' !== $thumb_html && sekeco_thumbnail_file_exists( $post );
	$tone                  = sekeco_card_tone( $post->ID );

	$chips = array();
	$meta  = '';

	if ( 'team' === $type ) {
		$meta = (string) get_post_meta( $post->ID, 'sekeco_team_role', true );
	} else {
		foreach ( array_slice( (array) get_the_category( $post->ID ), 0, 2 ) as $term ) {
			if ( 'uncategorized' !== $term->slug ) {
				$chips[] = $term->name;
			}
		}

		$year_key = 'portfolio' === $type ? 'sekeco_portfolio_year' : 'sekeco_product_year';
		$meta     = (string) get_post_meta( $post->ID, $year_key, true );
	}

	if ( 'portfolio' === $type ) {
		$client = (string) get_post_meta( $post->ID, 'sekeco_portfolio_client', true );
		$name   = '' !== $client ? $client : $name;
	}

	if ( '' === $detail && has_excerpt( $post ) ) {
		$detail = wp_trim_words( get_the_excerpt( $post ), 12, '…' );
	}

	$classes = array(
		'sk-card',
		'sk-card--' . $type,
		'sk-card--tone-' . $tone,
		$has_image ? 'sk-card--has-image' : 'sk-card--typographic',
	);

	if ( 'cover' === $args['variant'] ) {
		$classes[] = 'sk-card--cover';
	}

	ob_start();
	?>
	<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<a class="sk-card__link sk-glow" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
			<span class="sk-card__media"<?php echo 'portfolio' === $type ? ' data-sk-parallax="0.1"' : ''; ?>>
				<?php if ( $has_image ) : ?>
					<?php echo $thumb_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php elseif ( 'team' === $type ) : ?>
					<span class="sk-card__mark" aria-hidden="true"><?php echo esc_html( sekeco_initials( $name ) ); ?></span>
				<?php endif; ?>
			</span>
			<span class="sk-card__body">
				<?php if ( $chips ) : ?>
					<span class="sk-card__tags"><?php echo esc_html( implode( ', ', $chips ) ); ?></span>
				<?php endif; ?>
				<span class="sk-card__title"><?php echo esc_html( $name ); ?></span>
				<?php if ( '' !== $detail || '' !== $meta ) : ?>
					<span class="sk-card__detail">
						<?php echo esc_html( 'team' === $type ? $meta : trim( $detail ) ); ?>
					</span>
				<?php endif; ?>
			</span>
			<span class="sk-card__go">
				<?php echo sekeco_icon( 'arrow-right-up', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="screen-reader-text"><?php echo esc_html( sprintf( /* translators: %s: project name */ __( 'View %s', 'sekeco' ), $name ) ); ?></span>
			</span>
		</a>
	</article>
	<?php
	return (string) ob_get_clean();
}

/**
 * Compact team entry used beside the lead card: round portrait, name, role.
 *
 * @param WP_Post|int $post Post object or ID.
 * @return string
 */
function sekeco_render_crew_item( $post ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	list( $name ) = sekeco_split_title( get_the_title( $post ) );
	$role         = (string) get_post_meta( $post->ID, 'sekeco_team_role', true );
	$thumb        = get_the_post_thumbnail(
		$post,
		'thumbnail',
		array(
			'class'    => 'sk-crew__img',
			'alt'      => '',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);
	$has_image    = '' !== $thumb && sekeco_thumbnail_file_exists( $post );

	ob_start();
	?>
	<a class="sk-crew__item sk-glow" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
		<?php if ( $has_image ) : ?>
			<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<span class="sk-crew__mark" aria-hidden="true"><?php echo esc_html( sekeco_initials( $name ) ); ?></span>
		<?php endif; ?>
		<span class="sk-crew__text">
			<span class="sk-crew__name"><?php echo esc_html( $name ); ?></span>
			<?php if ( '' !== $role ) : ?>
				<span class="sk-crew__role"><?php echo esc_html( $role ); ?></span>
			<?php endif; ?>
		</span>
	</a>
	<?php
	return (string) ob_get_clean();
}

/**
 * Restrict rich text attributes to inline formatting.
 *
 * @param string $html Rich text value.
 * @return string
 */
function sekeco_kses_inline( $html ) {
	return wp_kses(
		(string) $html,
		array(
			'br'     => array(),
			'em'     => array(),
			'i'      => array(),
			'strong' => array(),
			'b'      => array(),
			'span'   => array( 'class' => array() ),
			'a'      => array(
				'href'   => array(),
				'rel'    => array(),
				'target' => array(),
			),
		)
	);
}
