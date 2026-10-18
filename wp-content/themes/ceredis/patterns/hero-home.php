<?php
/**
 * Title: CEREDIS — Hero
 * Slug: ceredis/hero-home
 * Categories: ceredis
 * Inserter: yes
 */
?>

<!-- wp:group {"align":"full","className":"ceredis-hero","backgroundColor":"ceredis-blue","textColor":"ceredis-white","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull ceredis-hero has-ceredis-white-color has-ceredis-blue-background-color has-text-color has-background">

	<!-- wp:columns {"align":"wide","verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"clamp(2rem,5vw,4.5rem)"}}}} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">

		<!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">

			<!-- wp:group {"className":"ceredis-hero-content","style":{"spacing":{"blockGap":"1.5rem"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group ceredis-hero-content">

				<!-- wp:paragraph {"className":"ceredis-eyebrow","textColor":"ceredis-cyan","fontSize":"eyebrow"} -->
				<p class="ceredis-eyebrow has-ceredis-cyan-color has-text-color has-eyebrow-font-size">PLATEFORMES D'APPRENTISSAGE GRATUITES · RALLYE SCOLAIRE DU CONGO</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"fontSize":"hero","textColor":"ceredis-white"} -->
				<h1 class="wp-block-heading has-ceredis-white-color has-text-color has-hero-font-size">Aidez chaque élève à réussir, quelle que soit son école.</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"fontSize":"lead"} -->
				<p class="has-lead-font-size">CEREDIS met gratuitement à la disposition des établissements du Congo des plateformes d'apprentissage et un Rallye scolaire. Vous préparez vos élèves, vous suivez leurs progrès, ils se dépassent.</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"1rem"},"blockGap":"0.875rem"}}} -->
				<div class="wp-block-buttons" style="margin-top:1rem">

					<!-- wp:button {"backgroundColor":"ceredis-fuchsia","textColor":"ceredis-white","className":"ceredis-hero-primary"} -->
					<div class="wp-block-button ceredis-hero-primary">
						<a class="wp-block-button__link has-ceredis-white-color has-ceredis-fuchsia-background-color has-text-color has-background wp-element-button" href="/#inscription">Inscrire mon établissement</a>
					</div>
					<!-- /wp:button -->

					<!-- wp:button {"backgroundColor":"ceredis-white","textColor":"ceredis-blue","className":"ceredis-hero-sponsor"} -->
					<div class="wp-block-button ceredis-hero-sponsor">
						<a class="wp-block-button__link has-ceredis-blue-color has-ceredis-white-background-color has-text-color has-background wp-element-button" href="/#sponsors">Devenir sponsor</a>
					</div>
					<!-- /wp:button -->

				</div>
				<!-- /wp:buttons -->

				<!-- wp:group {"className":"ceredis-hero-pills","layout":{"type":"flex","flexWrap":"wrap"}} -->
				<div class="wp-block-group ceredis-hero-pills">
					<!-- wp:paragraph {"className":"ceredis-pill"} -->
					<p class="ceredis-pill">1<sup>re</sup> édition : janvier 2027</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"ceredis-pill"} -->
					<p class="ceredis-pill">Seconde et Première · Français</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"ceredis-pill"} -->
					<p class="ceredis-pill">Gratuit pour les établissements</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->

			</div>
			<!-- /wp:group -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">

			<div class="ceredis-hero-visual">
				<img class="ceredis-hero-img" src="<?php echo esc_url( get_theme_file_uri( 'assets/images/Apprentissage collaboratif autour d’un ordinateur portable-3.png' ) ); ?>" alt="Trois élèves africains collaborent autour d’un ordinateur portable dans une activité pédagogique" width="1672" height="941" fetchpriority="high" decoding="async"/>
			</div>

		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
