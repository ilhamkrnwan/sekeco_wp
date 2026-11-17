<?php
/**
 * Title: Sign-off
 * Slug: sekeco/signoff
 * Categories: sekeco-cta, call-to-action
 * Description: A compact closing line with one action, for pages that should not end on the big closing field.
 * Keywords: contact, enquiry, closing, cta
 * Viewport Width: 1400
 * Text Domain: sekeco
 */
?>
<!-- wp:group {"className":"sk-signoff sk-wrap","layout":{"type":"default"}} -->
<div class="wp-block-group sk-signoff sk-wrap">
<!-- wp:group {"className":"sk-signoff__row","layout":{"type":"default"}} -->
<div class="wp-block-group sk-signoff__row">
<!-- wp:heading {"className":"sk-signoff__title"} -->
<h2 class="wp-block-heading sk-signoff__title"><?php echo wp_kses( __( 'Got a project? <em>Let\'s talk.</em>', 'sekeco' ), array( 'em' => array() ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Let\'s Collaborate', 'sekeco' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
