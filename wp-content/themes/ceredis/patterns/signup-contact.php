<?php
/**
 * Title: CEREDIS — Inscription et contact
 * Slug: ceredis/signup-contact
 * Categories: ceredis
 * Inserter: yes
 */
$contact_subject = rawurlencode( 'Échange autour du Rallye scolaire' );
$contact_mailto  = 'mailto:contact@ceredis.net?subject=' . $contact_subject;
?>

<!-- wp:group {"anchor":"inscription","align":"full","className":"ceredis-section ceredis-section--deep ceredis-signup-contact","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull ceredis-section ceredis-section--deep ceredis-signup-contact" id="inscription">
	<!-- wp:group {"className":"ceredis-section__inner","layout":{"type":"constrained"}} -->
	<div class="wp-block-group ceredis-section__inner">
		<!-- wp:group {"className":"ceredis-signup-contact__grid","layout":{"type":"grid"}} -->
		<div class="wp-block-group ceredis-signup-contact__grid">
			<!-- wp:group {"className":"ceredis-signup-contact__signup","layout":{"type":"constrained"}} -->
			<div class="wp-block-group ceredis-signup-contact__signup">
				<!-- wp:paragraph {"className":"ceredis-eyebrow"} -->
				<p class="ceredis-eyebrow">PARTICIPER</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2} -->
				<h2 class="wp-block-heading">Inscrivez votre établissement.</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"ceredis-lead"} -->
				<p class="ceredis-lead">Gratuit. Remplissez ce formulaire&nbsp;: nous revenons vers vous avec la suite.</p>
				<!-- /wp:paragraph -->

				<!-- wp:ceredis/school-preregistration /-->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"anchor":"contact","className":"ceredis-signup-contact__contact","layout":{"type":"constrained"}} -->
			<div class="wp-block-group ceredis-signup-contact__contact" id="contact">
				<!-- wp:paragraph {"className":"ceredis-eyebrow"} -->
				<p class="ceredis-eyebrow">ÉCHANGER AVEC CEREDIS</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2} -->
				<h2 class="wp-block-heading">Vous représentez une entreprise ou une institution&nbsp;?</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Découvrez les formules de partenariat, les engagements de CEREDIS et le dossier adapté à votre profil.</p>
				<!-- /wp:paragraph -->
				<!-- wp:buttons {"className":"ceredis-signup-contact__actions"} -->
				<div class="wp-block-buttons ceredis-signup-contact__actions">
					<!-- wp:button {"className":"ceredis-button--cyan"} -->
					<div class="wp-block-button ceredis-button--cyan"><a class="wp-block-button__link wp-element-button" href="/partenaires/">Voir la page Partenaires</a></div>
					<!-- /wp:button -->
					<!-- wp:button {"className":"ceredis-button--outline"} -->
					<div class="wp-block-button ceredis-button--outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $contact_mailto ); ?>">Écrire à l’équipe</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
				<!-- wp:list {"className":"ceredis-contact__details"} -->
				<ul class="wp-block-list ceredis-contact__details">
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>E-mail&nbsp;:</strong> <a href="<?php echo esc_url( $contact_mailto ); ?>">contact@ceredis.net</a></li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Téléphone&nbsp;:</strong> <a href="tel:+242064061534">(+242)&nbsp;06&nbsp;406&nbsp;1534</a></li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Brazzaville&nbsp;:</strong> 43 B Avenue du Djoué, Quartier La Milice, République du Congo</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
