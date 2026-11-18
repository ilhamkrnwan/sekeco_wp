<?php
/**
 * Server render for the Sekeco Facts block.
 *
 * @package Sekeco
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Serialized inner blocks.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$layout = isset( $attributes['layout'] ) ? $attributes['layout'] : 'ledger';
$layout = in_array( $layout, array( 'ledger', 'strip', 'links' ), true ) ? $layout : 'ledger';
$post   = get_post( isset( $block->context['postId'] ) ? (int) $block->context['postId'] : 0 );
$post   = $post ? $post : get_post();

if ( ! $post || post_password_required( $post ) ) {
	return;
}

$facts = sekeco_entry_facts( $post );

// Links are actions: the links layout shows only them, and the strip leaves
// them out (a page puts them in its buttons).
if ( 'links' === $layout || 'strip' === $layout ) {
	$facts = array_filter(
		$facts,
		static function ( $fact ) use ( $layout ) {
			return ( 'link' === $fact['type'] ) === ( 'links' === $layout );
		}
	);
}

if ( ! $facts ) {
	return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'sk-facts sk-facts--' . $layout ) );

if ( 'links' === $layout ) :
	?>
	<ul <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php foreach ( $facts as $fact ) : ?>
			<li>
				<a class="sk-facts__pill" href="<?php echo esc_url( $fact['url'] ); ?>" target="_blank" rel="noopener noreferrer">
					<span><?php echo esc_html( $fact['label'] ); ?></span>
					<?php echo sekeco_icon( 'arrow-right-up', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
	return;
endif;
?>
<dl <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php foreach ( $facts as $fact ) : ?>
		<div class="sk-facts__row<?php echo 'note' === $fact['type'] ? ' sk-facts__row--note' : ''; ?>">
			<dt><?php echo esc_html( $fact['label'] ); ?></dt>
			<dd>
				<?php if ( 'link' === $fact['type'] ) : ?>
					<a class="sk-facts__link" href="<?php echo esc_url( $fact['url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<span><?php echo esc_html( $fact['value'] ); ?></span>
						<?php echo sekeco_icon( 'arrow-right-up', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php elseif ( 'chips' === $fact['type'] ) : ?>
					<ul class="sk-facts__chips">
						<?php foreach ( $fact['items'] as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ( 'people' === $fact['type'] ) : ?>
					<ul class="sk-facts__people">
						<?php foreach ( $fact['items'] as $person ) : ?>
							<li>
								<?php if ( '' !== $person['url'] ) : ?>
									<a class="sk-facts__link" href="<?php echo esc_url( $person['url'] ); ?>"><?php echo esc_html( $person['name'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $person['name'] ); ?>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ( 'status' === $fact['type'] ) : ?>
					<span class="sk-facts__status<?php echo 'live' === strtolower( $fact['value'] ) ? ' is-live' : ''; ?>"><?php echo esc_html( $fact['value'] ); ?></span>
				<?php else : ?>
					<?php echo esc_html( $fact['value'] ); ?>
				<?php endif; ?>
			</dd>
		</div>
	<?php endforeach; ?>
</dl>
