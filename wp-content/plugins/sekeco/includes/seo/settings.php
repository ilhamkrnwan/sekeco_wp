<?php
/**
 * SEO settings: site-wide fallbacks for the description and share image.
 *
 * @package Sekeco
 */

defined( 'ABSPATH' ) || exit;

/**
 * Stored SEO options merged with defaults.
 *
 * @return array{description:string,image_id:int,twitter:string}
 */
function sekeco_seo_options() {
	$stored = get_option( 'sekeco_seo', array() );

	return wp_parse_args(
		is_array( $stored ) ? $stored : array(),
		array(
			'description' => '',
			'image_id'    => 0,
			'twitter'     => '',
		)
	);
}

/**
 * Sanitize the SEO options on save.
 *
 * @param mixed $input Raw submitted value.
 * @return array
 */
function sekeco_seo_sanitize( $input ) {
	$input   = is_array( $input ) ? $input : array();
	$twitter = isset( $input['twitter'] ) ? sanitize_text_field( $input['twitter'] ) : '';

	return array(
		'description' => isset( $input['description'] ) ? sanitize_textarea_field( $input['description'] ) : '',
		'image_id'    => isset( $input['image_id'] ) ? absint( $input['image_id'] ) : 0,
		'twitter'     => ltrim( $twitter, '@' ),
	);
}

/**
 * Register the setting and its page under Settings.
 *
 * @return void
 */
function sekeco_seo_register_settings() {
	register_setting(
		'sekeco_seo',
		'sekeco_seo',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'sekeco_seo_sanitize',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'sekeco_seo_register_settings' );

/**
 * Add the Settings → Sekeco SEO page.
 *
 * @return void
 */
function sekeco_seo_menu() {
	add_options_page(
		__( 'Sekeco SEO', 'sekeco' ),
		__( 'Sekeco SEO', 'sekeco' ),
		'manage_options',
		'sekeco-seo',
		'sekeco_seo_render_page'
	);
}
add_action( 'admin_menu', 'sekeco_seo_menu' );

/**
 * Load the media library on the SEO page only.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function sekeco_seo_admin_assets( $hook ) {
	if ( 'settings_page_sekeco-seo' === $hook ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'sekeco_seo_admin_assets' );

/**
 * Render the settings page.
 *
 * @return void
 */
function sekeco_seo_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$options = sekeco_seo_options();
	$preview = $options['image_id'] ? wp_get_attachment_image_url( $options['image_id'], 'medium' ) : '';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Sekeco SEO', 'sekeco' ); ?></h1>
		<p><?php esc_html_e( 'Fallbacks for the description and the share image when a page has none of its own.', 'sekeco' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'sekeco_seo' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="sekeco-seo-description"><?php esc_html_e( 'Default description', 'sekeco' ); ?></label></th>
					<td>
						<textarea id="sekeco-seo-description" name="sekeco_seo[description]" rows="3" class="large-text"><?php echo esc_textarea( $options['description'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Leave empty to use the site tagline.', 'sekeco' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Default share image', 'sekeco' ); ?></th>
					<td>
						<input type="hidden" id="sekeco-seo-image" name="sekeco_seo[image_id]" value="<?php echo esc_attr( (string) $options['image_id'] ); ?>">
						<p><img id="sekeco-seo-preview" src="<?php echo esc_url( $preview ); ?>" alt="" style="max-width:320px;height:auto;<?php echo $preview ? '' : 'display:none;'; ?>"></p>
						<button type="button" class="button" id="sekeco-seo-pick"><?php esc_html_e( 'Choose image', 'sekeco' ); ?></button>
						<button type="button" class="button-link" id="sekeco-seo-clear"><?php esc_html_e( 'Remove', 'sekeco' ); ?></button>
						<p class="description"><?php esc_html_e( 'Recommended 1200 × 630 px. Falls back to the site icon.', 'sekeco' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sekeco-seo-twitter"><?php esc_html_e( 'X / Twitter handle', 'sekeco' ); ?></label></th>
					<td><input type="text" id="sekeco-seo-twitter" name="sekeco_seo[twitter]" value="<?php echo esc_attr( $options['twitter'] ); ?>" class="regular-text" placeholder="sekeco"></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	( function () {
		var field = document.getElementById( 'sekeco-seo-image' );
		var preview = document.getElementById( 'sekeco-seo-preview' );
		var frame;

		document.getElementById( 'sekeco-seo-pick' ).addEventListener( 'click', function () {
			if ( ! frame ) {
				frame = wp.media( { library: { type: 'image' }, multiple: false } );
				frame.on( 'select', function () {
					var item = frame.state().get( 'selection' ).first().toJSON();
					field.value = item.id;
					preview.src = ( item.sizes && item.sizes.medium ? item.sizes.medium.url : item.url );
					preview.style.display = '';
				} );
			}
			frame.open();
		} );

		document.getElementById( 'sekeco-seo-clear' ).addEventListener( 'click', function () {
			field.value = '';
			preview.removeAttribute( 'src' );
			preview.style.display = 'none';
		} );
	}() );
	</script>
	<?php
}
