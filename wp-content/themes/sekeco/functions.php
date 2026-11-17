<?php
/**
 * Sekeco theme functions and definitions.
 *
 * @package Sekeco
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme setup.
 *
 * @return void
 */
function sekeco_setup() {
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'quote', 'video' ) );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );

	// Same tokens and component parts inside the editor.
	add_editor_style( array( 'style.css', 'assets/css/buttons.css', 'assets/css/pages.css', 'assets/css/solar-icons.css' ) );
}
add_action( 'after_setup_theme', 'sekeco_setup' );

/**
 * Enqueue the theme stylesheets on the frontend.
 *
 * @return void
 */
function sekeco_enqueue_styles() {
	// Version each file by its modified time so an edit is never hidden by a cached copy.
	$ver = static function ( $file ) {
		$path = get_parent_theme_file_path( $file );
		return wp_get_theme()->get( 'Version' ) . '.' . ( file_exists( $path ) ? filemtime( $path ) : 0 );
	};

	wp_enqueue_style( 'sekeco-style', get_parent_theme_file_uri( 'style.css' ), array(), $ver( 'style.css' ) );
	wp_style_add_data( 'sekeco-style', 'path', get_parent_theme_file_path( 'style.css' ) );

	wp_enqueue_style( 'sekeco-buttons', get_parent_theme_file_uri( 'assets/css/buttons.css' ), array( 'sekeco-style' ), $ver( 'assets/css/buttons.css' ) );

	wp_enqueue_style( 'sekeco-pages', get_parent_theme_file_uri( 'assets/css/pages.css' ), array( 'sekeco-style' ), $ver( 'assets/css/pages.css' ) );

	wp_enqueue_style( 'sekeco-icons', get_parent_theme_file_uri( 'assets/css/solar-icons.css' ), array( 'sekeco-style' ), $ver( 'assets/css/solar-icons.css' ) );
}
add_action( 'wp_enqueue_scripts', 'sekeco_enqueue_styles' );

/**
 * Set the colour mode and the motion flag before first paint.
 *
 * The stored choice wins; first-time visitors get the dark mode, as in the reference.
 * The theme toggle block keeps data-theme and localStorage in sync.
 *
 * @return void
 */
function sekeco_print_mode_script() {
	?>
	<script>
	(function(){var d=document.documentElement;d.classList.add('sekeco-js');try{var t=localStorage.getItem('sekeco-theme');if(t!=='light'&&t!=='dark'){t='dark';}d.setAttribute('data-theme',t);d.style.colorScheme=t;}catch(e){}})();
	</script>
	<?php
}
add_action( 'wp_head', 'sekeco_print_mode_script', 0 );

/**
 * Use the brand mark as the favicon until a site icon is set.
 *
 * @return void
 */
function sekeco_brand_favicon() {
	if ( has_site_icon() ) {
		return;
	}

	printf(
		'<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
		esc_url( get_parent_theme_file_uri( 'assets/images/sekeco-mark.svg' ) )
	);
}
add_action( 'wp_head', 'sekeco_brand_favicon', 2 );

/**
 * Preload the variable fonts declared in theme.json.
 *
 * @return void
 */
function sekeco_preload_fonts() {
	$fonts = array(
		'assets/fonts/dm-sans-latin-wght-normal.woff2',
		'assets/fonts/bricolage-grotesque-latin-wght-normal.woff2',
	);

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_parent_theme_file_uri( $font ) )
		);
	}
}
add_action( 'wp_head', 'sekeco_preload_fonts', 1 );

/**
 * Hide buttons whose bound URL is empty.
 *
 * Project, product, and team templates bind a button URL to post meta. When the
 * meta is missing the button would render with an empty href, so hide it
 * instead of shipping a dead link.
 *
 * @param string $block_content Rendered button markup.
 * @param array  $block         Parsed block.
 * @return string
 */
function sekeco_hide_empty_bound_buttons( $block_content, $block ) {
	$bindings = isset( $block['attrs']['metadata']['bindings'] )
		? $block['attrs']['metadata']['bindings']
		: array();

	if ( ! isset( $bindings['url'] ) ) {
		return $block_content;
	}

	return false === strpos( $block_content, 'href=""' ) ? $block_content : '';
}
add_filter( 'render_block_core/button', 'sekeco_hide_empty_bound_buttons', 10, 2 );

/**
 * Register the pattern category and block styles.
 *
 * @return void
 */
function sekeco_register_theme_extras() {
	$pattern_categories = array(
		'sekeco'         => array( __( 'Sekeco', 'sekeco' ), __( 'Sections designed for the Sekeco theme.', 'sekeco' ) ),
		'sekeco-hero'    => array( __( 'Sekeco: Hero', 'sekeco' ), __( 'Page openers.', 'sekeco' ) ),
		'sekeco-cta'     => array( __( 'Sekeco: Calls to action', 'sekeco' ), __( 'Closing bands that lead to the enquiry form.', 'sekeco' ) ),
		'sekeco-content' => array( __( 'Sekeco: Content', 'sekeco' ), __( 'Statements, services, process, questions and forms.', 'sekeco' ) ),
		'sekeco-work'    => array( __( 'Sekeco: Work and team', 'sekeco' ), __( 'Works, portfolio products and the team, from published content.', 'sekeco' ) ),
	);

	foreach ( $pattern_categories as $slug => $category ) {
		register_block_pattern_category(
			$slug,
			array(
				'label'       => $category[0],
				'description' => $category[1],
			)
		);
	}

	// Button styles: Fill and Outline come from core; these add six gradient looks.
	// Sizes are a separate Size control (a class), added by the Sekeco plugin.
	$button_styles = array(
		'gradient'         => __( 'Gradient', 'sekeco' ),
		'gradient-soft'    => __( 'Gradient soft', 'sekeco' ),
		'gradient-outline' => __( 'Gradient outline', 'sekeco' ),
		'gradient-aurora'  => __( 'Gradient aurora', 'sekeco' ),
		'gradient-arrow'   => __( 'Gradient arrow', 'sekeco' ),
		'gradient-sheen'   => __( 'Gradient sheen', 'sekeco' ),
	);

	foreach ( $button_styles as $name => $label ) {
		register_block_style(
			'core/button',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}

	register_block_style(
		'core/group',
		array(
			'name'  => 'sekeco-surface',
			'label' => __( 'Surface', 'sekeco' ),
		)
	);
}
add_action( 'init', 'sekeco_register_theme_extras' );

/**
 * Whether a parsed block carries a class.
 *
 * @param array  $block Parsed block.
 * @param string $class Class name.
 * @return bool
 */
function sekeco_block_has_class( $block, $class ) {
	return ! empty( $block['attrs']['className'] ) && in_array( $class, preg_split( '/\s+/', (string) $block['attrs']['className'] ), true );
}

/**
 * Set a "Name: detail" title as a name with its detail line beneath.
 *
 * Works and Portfolio titles follow that convention, and the cards already
 * split it. A Post Title block with the class `sk-split-title` does the same
 * on the single templates; the document title and the permalink are untouched.
 *
 * @param string   $block_content Rendered Post Title markup.
 * @param array    $block         Parsed block.
 * @param WP_Block $instance      Block instance.
 * @return string
 */
function sekeco_split_post_title( $block_content, $block, $instance ) {
	if ( ! sekeco_block_has_class( $block, 'sk-split-title' ) || ! function_exists( 'sekeco_split_title' ) ) {
		return $block_content;
	}

	$post_id = isset( $instance->context['postId'] ) ? (int) $instance->context['postId'] : (int) get_the_ID();

	list( $name, $detail ) = sekeco_split_title( get_the_title( $post_id ) );

	if ( '' === $detail || ! preg_match( '#^(\s*<h[1-6][^>]*>)(.*)(</h[1-6]>\s*)$#s', $block_content, $match ) ) {
		return $block_content;
	}

	return $match[1] . esc_html( $name ) . '<span class="screen-reader-text">: </span><span class="sk-split-title__detail">' . esc_html( $detail ) . '</span>' . $match[3];
}
add_filter( 'render_block_core/post-title', 'sekeco_split_post_title', 10, 3 );

/**
 * Show a Post Excerpt marked `sk-lead` only when the editor wrote an excerpt.
 *
 * Otherwise WordPress would invent one from the first words of the post and
 * the lead would repeat the opening paragraph.
 *
 * @param string $block_content Rendered Post Excerpt markup.
 * @param array  $block         Parsed block.
 * @param WP_Block $instance    Block instance.
 * @return string
 */
function sekeco_manual_excerpt_only( $block_content, $block, $instance ) {
	if ( ! sekeco_block_has_class( $block, 'sk-lead' ) ) {
		return $block_content;
	}

	$post_id = isset( $instance->context['postId'] ) ? (int) $instance->context['postId'] : (int) get_the_ID();

	return has_excerpt( $post_id ) ? $block_content : '';
}
add_filter( 'render_block_core/post-excerpt', 'sekeco_manual_excerpt_only', 10, 3 );

/**
 * Point a Query Loop whose query sets `sekecoRelated` at the entries most like this one:
 * the same type and category, never the entry itself. When fewer than the
 * wanted number share a category it falls back to the latest.
 *
 * @param array    $query Query vars.
 * @param WP_Block $block Query block.
 * @return array
 */
function sekeco_related_query_vars( $query, $block ) {
	// The Query block marks itself in its query attribute; the Post Template
	// inside it receives that object as context.
	if ( empty( $block->context['query']['sekecoRelated'] ) || ! is_singular() ) {
		return $query;
	}

	$post                 = get_queried_object();
	$query['post__not_in'] = array( $post->ID );
	$categories           = wp_get_post_categories( $post->ID );

	if ( $categories ) {
		$wanted = isset( $query['posts_per_page'] ) ? (int) $query['posts_per_page'] : 3;
		$same   = get_posts(
			array(
				'post_type'      => $post->post_type,
				'category__in'   => $categories,
				'post__not_in'   => array( $post->ID ),
				'posts_per_page' => $wanted,
				'fields'         => 'ids',
			)
		);

		if ( count( $same ) >= $wanted ) {
			$query['category__in'] = $categories;
		}
	}

	return $query;
}
add_filter( 'query_loop_block_query_vars', 'sekeco_related_query_vars', 10, 2 );

/**
 * Hide a Post Author block whose public name is only the login (such as
 * "admin"): a username is not a byline. Set a display name on the user to show it.
 *
 * @param string   $block_content Rendered Post Author markup.
 * @param array    $block         Parsed block.
 * @param WP_Block $instance      Block instance.
 * @return string
 */
function sekeco_hide_login_author( $block_content, $block, $instance ) {
	$post_id = isset( $instance->context['postId'] ) ? (int) $instance->context['postId'] : (int) get_the_ID();
	$user    = get_userdata( (int) get_post_field( 'post_author', $post_id ) );

	return ( $user && $user->display_name === $user->user_login ) ? '' : $block_content;
}
add_filter( 'render_block_core/post-author', 'sekeco_hide_login_author', 10, 3 );
