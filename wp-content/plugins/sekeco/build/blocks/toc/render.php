<?php
/**
 * Server render for the Sekeco On this page block.
 *
 * @package Sekeco
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Serialized inner blocks.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$post = get_post( isset( $block->context['postId'] ) ? (int) $block->context['postId'] : 0 );
$post = $post ? $post : get_post();

if ( ! $post || post_password_required( $post ) ) {
	return;
}

$headings = sekeco_toc_headings( $post );

// A short entry has nothing to navigate.
if ( count( $headings ) < 3 ) {
	return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'sk-toc' ) );
?>
<details <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> open data-sk-toc>
	<summary class="sk-toc__title">
		<span><?php esc_html_e( 'On this page', 'sekeco' ); ?></span>
		<?php echo sekeco_icon( 'alt-arrow-down', array( 'size' => 20, 'class' => 'sk-toc__chevron' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</summary>
	<nav aria-label="<?php esc_attr_e( 'On this page', 'sekeco' ); ?>">
		<ol class="sk-toc__list">
			<?php foreach ( $headings as $heading ) : ?>
				<li class="sk-toc__item sk-toc__item--h<?php echo (int) $heading['level']; ?>">
					<a href="#<?php echo esc_attr( $heading['id'] ); ?>"><?php echo esc_html( $heading['text'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
</details>
