<?php
/**
 * Title: CEREDIS — Derniers contenus
 * Slug: ceredis/latest-content
 * Categories: ceredis
 * Inserter: yes
 */
?>

<!-- wp:group {"align":"full","className":"ceredis-section","style":{"spacing":{"padding":{"top":"clamp(4rem,8vw,7rem)","bottom":"clamp(4rem,8vw,7rem)"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull ceredis-section">

	<!-- wp:paragraph {"textColor":"ceredis-fuchsia","fontSize":"eyebrow"} -->
	<p class="has-ceredis-fuchsia-color has-text-color has-eyebrow-font-size">ACTUALITÉS &amp; PUBLICATIONS</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2} -->
	<h2 class="wp-block-heading">Les dernières ressources de CEREDIS</h2>
	<!-- /wp:heading -->

	<!-- wp:query {"query":{"perPage":3,"postType":"post","order":"desc","orderBy":"date","inherit":false},"displayLayout":{"type":"flex","columns":3}} -->
	<div class="wp-block-query">

		<!-- wp:post-template -->
			<!-- wp:group {"className":"ceredis-card","layout":{"type":"constrained"}} -->
			<div class="wp-block-group ceredis-card">

				<!-- wp:post-date {"fontSize":"small"} /-->

				<!-- wp:post-title {"isLink":true,"level":3} /-->

				<!-- wp:post-excerpt {"moreText":"Lire la suite →"} /-->

			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->

	</div>
	<!-- /wp:query -->

</div>
<!-- /wp:group -->
