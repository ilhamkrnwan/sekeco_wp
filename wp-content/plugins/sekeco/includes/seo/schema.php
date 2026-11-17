<?php
/**
 * JSON-LD structured data.
 *
 * One @graph per page: Organization and WebSite everywhere, plus the entity
 * that matches the current content (CreativeWork, SoftwareApplication, Person,
 * Article) and a single FAQPage built from the page's FAQ blocks.
 *
 * @package Sekeco
 */

defined( 'ABSPATH' ) || exit;

/**
 * Question and answer pairs collected from FAQ item blocks while rendering.
 *
 * @param array|null $item Pair to add, or null to read the list.
 * @return array<int,array{question:string,answer:string}>
 */
function sekeco_schema_faq_items( $item = null ) {
	static $items = array();

	if ( null !== $item ) {
		$items[] = $item;
	}

	return $items;
}

/**
 * Collect each rendered FAQ item. Block content renders before wp_head in a
 * block theme, so the list is complete when the graph is printed.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block         Parsed block.
 * @return string Unchanged markup.
 */
function sekeco_schema_collect_faq( $block_content, $block ) {
	$question = isset( $block['attrs']['question'] ) ? trim( wp_strip_all_tags( $block['attrs']['question'] ) ) : '';

	if ( '' === $question || '' === trim( $block_content ) ) {
		return $block_content;
	}

	if ( preg_match( '#<div class="sk-faq-item__answer">(.*)</div>\s*</details>#s', $block_content, $match ) ) {
		$answer = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $match[1] ) ) );

		if ( '' !== $answer ) {
			sekeco_schema_faq_items(
				array(
					'question' => $question,
					'answer'   => $answer,
				)
			);
		}
	}

	return $block_content;
}
add_filter( 'render_block_sekeco/faq-item', 'sekeco_schema_collect_faq', 10, 2 );

/**
 * Image URL of a post's featured image, or an empty string.
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function sekeco_schema_post_image( $post ) {
	if ( function_exists( 'sekeco_thumbnail_file_exists' ) && sekeco_thumbnail_file_exists( $post ) ) {
		$url = get_the_post_thumbnail_url( $post, 'full' );

		return $url ? $url : '';
	}

	return '';
}

/**
 * Keep only non-empty values.
 *
 * @param array $data Node properties.
 * @return array
 */
function sekeco_schema_filter( $data ) {
	return array_filter(
		$data,
		static function ( $value ) {
			return ! ( '' === $value || null === $value || array() === $value );
		}
	);
}

/**
 * Build the structured-data graph for the current request.
 *
 * @return array Graph nodes; empty when nothing should be printed.
 */
function sekeco_schema_graph() {
	$ctx     = sekeco_seo_context();
	$org_id  = home_url( '/#organization' );
	$site_id = home_url( '/#website' );
	$logo    = get_site_icon_url( 512 );
	$graph   = array();

	$graph[] = sekeco_schema_filter(
		array(
			'@type'       => 'Organization',
			'@id'         => $org_id,
			'name'        => $ctx['site'],
			'url'         => home_url( '/' ),
			'description' => wp_strip_all_tags( get_bloginfo( 'description' ) ),
			'logo'        => $logo ? array(
				'@type' => 'ImageObject',
				'url'   => $logo,
			) : '',
		)
	);

	$graph[] = array(
		'@type'           => 'WebSite',
		'@id'             => $site_id,
		'url'             => home_url( '/' ),
		'name'            => $ctx['site'],
		'inLanguage'      => get_bloginfo( 'language' ),
		'publisher'       => array( '@id' => $org_id ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	$post = is_singular() ? get_queried_object() : null;

	if ( $post instanceof WP_Post && ! post_password_required( $post ) ) {
		$node = sekeco_schema_entity( $post, $ctx, $org_id );

		if ( $node ) {
			$graph[] = $node;
		}
	}

	$faq = sekeco_schema_faq_items();

	if ( $faq ) {
		$graph[] = array(
			'@type'      => 'FAQPage',
			'@id'        => trailingslashit( $ctx['url'] ) . '#faq',
			'url'        => $ctx['url'],
			'mainEntity' => array_map(
				static function ( $item ) {
					return array(
						'@type'          => 'Question',
						'name'           => $item['question'],
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => $item['answer'],
						),
					);
				},
				$faq
			),
		);
	}

	/**
	 * Filter the JSON-LD graph nodes.
	 *
	 * @param array $graph Nodes.
	 * @param array $ctx   SEO context.
	 */
	return apply_filters( 'sekeco_schema_graph', $graph, $ctx );
}

/**
 * Entity node for a singular post.
 *
 * @param WP_Post $post   Post object.
 * @param array   $ctx    SEO context.
 * @param string  $org_id Organization node id.
 * @return array|null
 */
function sekeco_schema_entity( $post, $ctx, $org_id ) {
	$image    = sekeco_schema_post_image( $post );
	$base     = array(
		'@id'           => $ctx['url'] . '#entity',
		'url'           => $ctx['url'],
		'name'          => $ctx['title'],
		'description'   => $ctx['description'],
		'image'         => $image,
		'datePublished' => get_post_time( 'c', true, $post ),
		'dateModified'  => get_post_modified_time( 'c', true, $post ),
		'inLanguage'    => get_bloginfo( 'language' ),
	);
	$keywords = wp_get_post_terms( $post->ID, 'category', array( 'fields' => 'names' ) );
	$keywords = is_wp_error( $keywords ) ? array() : $keywords;

	switch ( $post->post_type ) {
		case 'portfolio':
			return sekeco_schema_filter(
				array_merge(
					$base,
					array(
						'@type'     => 'CreativeWork',
						'author'    => array( '@id' => $org_id ),
						'publisher' => array( '@id' => $org_id ),
						'keywords'  => implode( ', ', $keywords ),
						'sameAs'    => (string) get_post_meta( $post->ID, 'sekeco_portfolio_url', true ),
					)
				)
			);

		case 'product':
			$url = (string) get_post_meta( $post->ID, 'sekeco_product_url', true );

			return sekeco_schema_filter(
				array_merge(
					$base,
					array(
						'@type'     => 'SoftwareApplication',
						'author'    => array( '@id' => $org_id ),
						'publisher' => array( '@id' => $org_id ),
						'keywords'  => implode( ', ', $keywords ),
						'sameAs'    => $url,
					)
				)
			);

		case 'team':
			list( $name ) = function_exists( 'sekeco_split_title' ) ? sekeco_split_title( $post->post_title ) : array( $post->post_title );
			$same         = array();

			foreach ( array( 'github', 'linkedin', 'instagram' ) as $network ) {
				$link = (string) get_post_meta( $post->ID, 'sekeco_team_' . $network, true );

				if ( '' !== $link ) {
					$same[] = $link;
				}
			}

			return sekeco_schema_filter(
				array(
					'@type'    => 'Person',
					'@id'      => $ctx['url'] . '#entity',
					'url'      => $ctx['url'],
					'name'     => $name,
					'jobTitle' => (string) get_post_meta( $post->ID, 'sekeco_team_role', true ),
					'image'    => $image,
					'sameAs'   => $same,
					'worksFor' => array( '@id' => $org_id ),
				)
			);

		case 'post':
			return sekeco_schema_filter(
				array_merge(
					$base,
					array(
						'@type'     => 'Article',
						'headline'  => $ctx['title'],
						'author'    => array(
							'@type' => 'Person',
							'name'  => get_the_author_meta( 'display_name', (int) $post->post_author ),
						),
						'publisher' => array( '@id' => $org_id ),
					)
				)
			);
	}

	return null;
}

/**
 * Print the JSON-LD script in the head.
 *
 * @return void
 */
function sekeco_schema_print() {
	if ( is_404() || is_feed() || is_embed() || is_search() || ! get_option( 'blog_public' ) ) {
		return;
	}

	$graph = sekeco_schema_graph();

	if ( ! $graph ) {
		return;
	}

	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
	);

	echo '<script type="application/ld+json">' . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-encoded with HEX_TAG.
}
add_action( 'wp_head', 'sekeco_schema_print', 6 );
