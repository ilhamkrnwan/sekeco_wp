<?php
/**
 * Title: Journal index
 * Slug: sekeco/post-grid
 * Categories: sekeco-content, posts
 * Description: Posts as an index: the newest leads with its picture, the rest follow as dated rows, with pagination. For the Journal, archives and categories.
 * Keywords: journal, posts, blog, archive
 * Template Types: home, archive, category
 * Viewport Width: 1400
 * Inserter: no
 * Text Domain: sekeco
 */
?>
<!-- wp:query {"queryId":1,"query":{"perPage":9,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"className":"sk-journal","layout":{"type":"default"}} -->
<div class="wp-block-query sk-journal">
	<!-- wp:post-template -->
		<!-- wp:group {"className":"sk-entry","layout":{"type":"default"}} -->
		<div class="wp-block-group sk-entry">
			<!-- wp:post-date {"className":"sk-entry__when"} /-->

			<!-- wp:group {"className":"sk-entry__text","layout":{"type":"default"}} -->
			<div class="wp-block-group sk-entry__text">
				<!-- wp:post-title {"level":2,"isLink":true} /-->

				<!-- wp:post-excerpt {"moreText":"","excerptLength":28} /-->

				<!-- wp:post-terms {"term":"category","separator":"","className":"sk-pills"} /-->
			</div>
			<!-- /wp:group -->

			<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3"} /-->
		</div>
		<!-- /wp:group -->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-numbers /-->
		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->

	<!-- wp:query-no-results -->
		<!-- wp:group {"className":"is-style-sekeco-surface","layout":{"type":"constrained"}} -->
		<div class="wp-block-group is-style-sekeco-surface">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><?php esc_html_e( 'Nothing here yet.', 'sekeco' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"textColor":"muted"} -->
			<p class="has-muted-color has-text-color"><?php esc_html_e( 'Nothing is published here yet. The Journal and our work are a click away.', 'sekeco' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:sekeco/project-grid {"postType":"portfolio","layout":"list","perPage":4} /-->
		</div>
		<!-- /wp:group -->
	<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
