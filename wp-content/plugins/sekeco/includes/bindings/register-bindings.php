<?php
/**
 * Sekeco meta box binding source.
 *
 * A small wrapper around registered post meta that also works in singular
 * templates, where the block context may not include a postId.
 *
 * @package Sekeco
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve a registered post meta value for block bindings.
 *
 * @param array    $source_args    Source arguments, e.g. array( 'key' => 'meta_key' ).
 * @param WP_Block $block_instance Block instance.
 * @return string|null
 */
function sekeco_binding_meta_get_value( array $source_args, $block_instance ) {
	if ( empty( $source_args['key'] ) ) {
		return null;
	}

	$post_id = $block_instance->context['postId'] ?? get_the_ID();

	if ( ! $post_id ) {
		return null;
	}

	$post = get_post( $post_id );

	if (
		! $post ||
		( ! is_post_publicly_viewable( $post ) && ! current_user_can( 'read_post', $post_id ) ) ||
		post_password_required( $post )
	) {
		return null;
	}

	$key = $source_args['key'];

	if ( is_protected_meta( $key, 'post' ) ) {
		return null;
	}

	$registered_meta = array_merge(
		get_registered_meta_keys( 'post', $post->post_type ),
		get_registered_meta_keys( 'post', '' )
	);

	if ( empty( $registered_meta[ $key ]['show_in_rest'] ) ) {
		return null;
	}

	$value = get_post_meta( $post_id, $key, true );

	if ( ! is_scalar( $value ) ) {
		return '';
	}

	return (string) $value;
}

/**
 * Register the binding source.
 *
 * @return void
 */
function sekeco_register_bindings() {
	register_block_bindings_source(
		'sekeco/meta',
		array(
			'label'              => __( 'Sekeco post meta', 'sekeco' ),
			'get_value_callback' => 'sekeco_binding_meta_get_value',
			'uses_context'       => array( 'postId', 'postType' ),
		)
	);
}
add_action( 'init', 'sekeco_register_bindings' );

/**
 * Label of the entry's content type: Works, Portfolio, Team, Journal or Page.
 *
 * Lets a template name what kind of thing a result is, e.g. in search, where
 * one list mixes every public type.
 *
 * @param array    $source_args    Unused.
 * @param WP_Block $block_instance Block instance.
 * @return string|null
 */
function sekeco_binding_post_type_get_value( array $source_args, $block_instance ) {
	$post_id = $block_instance->context['postId'] ?? get_the_ID();
	$type    = $post_id ? get_post_type( $post_id ) : '';
	$labels  = function_exists( 'sekeco_search_types' ) ? sekeco_search_types() : array();

	return isset( $labels[ $type ] ) ? $labels[ $type ] : null;
}

/**
 * Register the post type label binding source.
 *
 * @return void
 */
function sekeco_register_post_type_binding() {
	register_block_bindings_source(
		'sekeco/post-type',
		array(
			'label'              => __( 'Sekeco content type', 'sekeco' ),
			'get_value_callback' => 'sekeco_binding_post_type_get_value',
			'uses_context'       => array( 'postId' ),
		)
	);
}
add_action( 'init', 'sekeco_register_post_type_binding' );
