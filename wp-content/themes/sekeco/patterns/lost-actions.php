<?php
/**
 * Title: Lost actions
 * Slug: sekeco/lost-actions
 * Categories: sekeco-cta
 * Description: Two ways out of a dead end: the homepage and the work.
 * Inserter: no
 * Text Domain: sekeco
 */
?>
<!-- wp:buttons {"className":"sk-lost__actions"} -->
<div class="wp-block-buttons sk-lost__actions"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the homepage', 'sekeco' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/works/' ) ); ?>"><?php esc_html_e( 'See our work', 'sekeco' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
