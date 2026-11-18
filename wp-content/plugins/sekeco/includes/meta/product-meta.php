<?php
/**
 * Product post meta.
 *
 * @package Sekeco
 */

defined( 'ABSPATH' ) || exit;

/**
 * The kinds of product Sekeco makes. Filter `sekeco_product_types` to add more.
 *
 * @return string[]
 */
function sekeco_product_types() {
	return apply_filters(
		'sekeco_product_types',
		array( 'SaaS', 'Plugin', 'Tool', 'Library', 'App', 'Template', 'Other' )
	);
}

/**
 * Keep the product type to one of the known kinds, or empty.
 *
 * @param mixed $value Submitted value.
 * @return string
 */
function sekeco_sanitize_product_type( $value ) {
	$value = sanitize_text_field( (string) $value );

	foreach ( sekeco_product_types() as $type ) {
		if ( 0 === strcasecmp( $type, $value ) ) {
			return $type;
		}
	}

	return '';
}

/**
 * Register Product meta fields.
 *
 * @return void
 */
function sekeco_register_product_meta() {
	$fields = array(
		'sekeco_product_url'    => array(
			'type'        => 'string',
			'description' => __( 'Product or repository URL', 'sekeco' ),
			'sanitize'    => 'esc_url_raw',
		),
		'sekeco_product_status' => array(
			'type'        => 'string',
			'description' => __( 'Status, e.g. Live or Open source', 'sekeco' ),
			'sanitize'    => 'sanitize_text_field',
		),
		'sekeco_product_year'   => array(
			'type'        => 'string',
			'description' => __( 'Year or year range', 'sekeco' ),
			'sanitize'    => 'sanitize_text_field',
		),
		'sekeco_product_stack'  => array(
			'type'        => 'string',
			'description' => __( 'Tech stack', 'sekeco' ),
			'sanitize'    => 'sanitize_text_field',
		),
		'sekeco_product_repo'   => array(
			'type'        => 'string',
			'description' => __( 'Repository name', 'sekeco' ),
			'sanitize'    => 'sanitize_text_field',
		),
		'sekeco_product_type'      => array(
			'type'        => 'string',
			'description' => __( 'Kind of product: SaaS, Plugin, Tool, Library, App...', 'sekeco' ),
			'sanitize'    => 'sekeco_sanitize_product_type',
		),
		'sekeco_product_version'   => array(
			'type'        => 'string',
			'description' => __( 'Latest version', 'sekeco' ),
			'sanitize'    => 'sanitize_text_field',
		),
		'sekeco_product_platform'  => array(
			'type'        => 'string',
			'description' => __( 'Platforms, comma separated: Web, WordPress, CLI, iOS...', 'sekeco' ),
			'sanitize'    => 'sanitize_text_field',
		),
		'sekeco_product_docs'      => array(
			'type'        => 'string',
			'description' => __( 'Documentation URL', 'sekeco' ),
			'sanitize'    => 'esc_url_raw',
		),
		'sekeco_product_demo'      => array(
			'type'        => 'string',
			'description' => __( 'Live demo URL', 'sekeco' ),
			'sanitize'    => 'esc_url_raw',
		),
		'sekeco_product_download'  => array(
			'type'        => 'string',
			'description' => __( 'Download URL', 'sekeco' ),
			'sanitize'    => 'esc_url_raw',
		),
		'sekeco_product_changelog' => array(
			'type'        => 'string',
			'description' => __( 'Changelog URL', 'sekeco' ),
			'sanitize'    => 'esc_url_raw',
		),
	);

	foreach ( $fields as $key => $field ) {
		register_post_meta(
			'product',
			$key,
			array(
				'type'              => $field['type'],
				'description'       => $field['description'],
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => $field['sanitize'],
				'auth_callback'     => 'sekeco_meta_auth_callback',
			)
		);
	}
}
add_action( 'init', 'sekeco_register_product_meta', 20 );

/**
 * Give the editor the list of product types for the Type select.
 *
 * @return void
 */
function sekeco_expose_product_types() {
	wp_add_inline_script(
		'sekeco-icons',
		'window.sekecoProductTypes=' . wp_json_encode( array_values( sekeco_product_types() ) ) . ';'
	);
}
add_action( 'enqueue_block_editor_assets', 'sekeco_expose_product_types', 2 );
