<?php
/**
 * Headings and anchors behind the On this page block.
 *
 * The block lists the H2 and H3 headings of the entry; this module gives each
 * of those headings an id when the editor did not set an anchor, using the
 * same rule in the same order so the links land on their headings.
 *
 * @package Sekeco
 */

defined( 'ABSPATH' ) || exit;

/**
 * Next unused anchor for a heading text within one entry.
 *
 * @param string               $text Heading text.
 * @param array<string,int>    $seen Anchors already used, passed by reference.
 * @return string
 */
function sekeco_toc_anchor( $text, array &$seen ) {
	$base = sanitize_title( $text );
	$base = '' !== $base ? $base : 'section';

	$seen[ $base ] = isset( $seen[ $base ] ) ? $seen[ $base ] + 1 : 1;

	return 1 === $seen[ $base ] ? $base : $base . '-' . $seen[ $base ];
}

/**
 * The H2 and H3 headings of an entry, in order, with their anchors.
 *
 * @param WP_Post $post Entry.
 * @return array<int,array{level:int,text:string,id:string}>
 */
function sekeco_toc_headings( $post ) {
	$found = array();
	$seen  = array();
	$walk  = static function ( $blocks ) use ( &$walk, &$found, &$seen ) {
		foreach ( $blocks as $block ) {
			if ( 'core/heading' === $block['blockName'] ) {
				$level = isset( $block['attrs']['level'] ) ? (int) $block['attrs']['level'] : 2;
				$text  = trim( wp_strip_all_tags( $block['innerHTML'] ) );

				if ( '' !== $text && $level >= 2 && $level <= 3 ) {
					$id = ! empty( $block['attrs']['anchor'] ) ? (string) $block['attrs']['anchor'] : sekeco_toc_anchor( $text, $seen );

					$found[] = array(
						'level' => $level,
						'text'  => $text,
						'id'    => $id,
					);
				} elseif ( '' !== $text && empty( $block['attrs']['anchor'] ) ) {
					// Other levels still take part in the unique-anchor sequence.
					sekeco_toc_anchor( $text, $seen );
				}
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};

	$walk( parse_blocks( $post->post_content ) );

	return $found;
}

/**
 * Whether the page is rendering the entry's own content (not template headings).
 *
 * @param bool|null $set New state, or null to read it.
 * @return bool
 */
function sekeco_toc_in_content( $set = null ) {
	static $state = false;

	if ( null !== $set ) {
		$state = $set;
	}

	return $state;
}

/**
 * Mark the start of the Post Content block.
 *
 * @param string|null $pre   Pre-rendered markup.
 * @param array       $block Parsed block.
 * @return string|null
 */
function sekeco_toc_enter_content( $pre, $block ) {
	if ( 'core/post-content' === $block['blockName'] ) {
		sekeco_toc_in_content( true );
	}

	return $pre;
}
add_filter( 'pre_render_block', 'sekeco_toc_enter_content', 10, 2 );

/**
 * Mark the end of the Post Content block.
 *
 * @param string $content Rendered markup.
 * @return string
 */
function sekeco_toc_leave_content( $content ) {
	sekeco_toc_in_content( false );

	return $content;
}
add_filter( 'render_block_core/post-content', 'sekeco_toc_leave_content', 100 );

/**
 * Give a heading inside the entry's content an id when it has none.
 *
 * @param string $block_content Rendered Heading markup.
 * @param array  $block         Parsed block.
 * @return string
 */
function sekeco_toc_heading_ids( $block_content, $block ) {
	static $seen = array();
	static $for  = 0;

	if ( ! sekeco_toc_in_content() || ! empty( $block['attrs']['anchor'] ) ) {
		return $block_content;
	}

	$post_id = (int) get_the_ID();

	if ( $post_id !== $for ) {
		$seen = array();
		$for  = $post_id;
	}

	$text = trim( wp_strip_all_tags( $block_content ) );

	if ( '' === $text ) {
		return $block_content;
	}

	$id = sekeco_toc_anchor( $text, $seen );

	if ( preg_match( '#^\s*<h[1-6][^>]*\sid=#i', $block_content ) ) {
		return $block_content;
	}

	return preg_replace( '#^(\s*<h[1-6])#i', '$1 id="' . esc_attr( $id ) . '"', $block_content, 1 );
}
add_filter( 'render_block_core/heading', 'sekeco_toc_heading_ids', 10, 2 );
