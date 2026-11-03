<?php
/**
 * Title: CEREDIS — Derniers contenus
 * Slug: ceredis/latest-content
 * Categories: ceredis
 * Inserter: yes
 */
?>

<!-- wp:group {"align":"full","className":"ceredis-section ceredis-latest","style":{"spacing":{"padding":{"top":"clamp(4rem,7vw,6.5rem)","bottom":"clamp(4rem,7vw,6.5rem)"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull ceredis-section ceredis-latest" id="actualites">

	<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"1rem","margin":{"bottom":"3rem"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignwide" style="margin-bottom:3rem">

		<!-- wp:paragraph {"textColor":"ceredis-fuchsia","fontSize":"eyebrow"} -->
		<p class="has-ceredis-fuchsia-color has-text-color has-eyebrow-font-size">LES RÉFLEXIONS DE CEREDIS</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":2} -->
		<h2 class="wp-block-heading">Le blog CEREDIS — Comprendre et transformer l’éducation</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>Analyses et perspectives sur les transformations de l'éducation, l'innovation pédagogique et les apports des sciences cognitives de l'apprentissage.</p>
		<!-- /wp:paragraph -->

	</div>
	<!-- /wp:group -->

	<!-- wp:query {"query":{"perPage":3,"postType":"post","order":"desc","orderBy":"date","inherit":false},"displayLayout":{"type":"flex","columns":3},"align":"wide"} -->
	<div class="wp-block-query alignwide">

		<!-- wp:post-template -->
			<!-- wp:group {"className":"ceredis-card","layout":{"type":"constrained"}} -->
			<div class="wp-block-group ceredis-card">

				<!-- wp:post-terms {"term":"category","className":"ceredis-post-category"} /-->

				<!-- wp:post-date {"fontSize":"small"} /-->

				<!-- wp:post-title {"isLink":true,"level":3} /-->

				<!-- wp:post-excerpt /-->

				<!-- wp:read-more {"content":"Lire l’article"} /-->

			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
			<!-- wp:group {"className":"ceredis-blog-empty","layout":{"type":"constrained"}} -->
			<div class="wp-block-group ceredis-blog-empty">
				<!-- wp:paragraph -->
				<p>Nos premières publications seront bientôt disponibles.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		<!-- /wp:query-no-results -->

	</div>
	<!-- /wp:query -->

	<!-- wp:buttons {"align":"wide","layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons alignwide">
		<!-- wp:button {"className":"is-style-outline","textColor":"ceredis-blue"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-ceredis-blue-color has-text-color wp-element-button" href="/blog/">Tous les articles</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:group -->
