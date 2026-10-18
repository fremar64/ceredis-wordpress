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
				<p class="ceredis-lead">Gratuit. Remplissez ce formulaire : nous revenons vers vous avec la suite.</p>
				<!-- /wp:paragraph -->

				<!-- wp:html -->
				<!-- TODO-CONTENT: BLOQUANT POUR LA MISE EN PRODUCTION : remplacer ce repli mailto par un traitement réel (plugin de formulaire ou autre), avec message de confirmation, protection anti-spam et mention d'information sur l'usage des données (page de confidentialité à créer). -->
				<form class="ceredis-signup-contact__form" action="mailto:contact@ceredis.net" method="post" enctype="text/plain">
					<div class="ceredis-form__field">
						<label for="ceredis-signup-organization">Nom de l'établissement</label>
						<input id="ceredis-signup-organization" name="etablissement" type="text" autocomplete="organization" required />
					</div>
					<div class="ceredis-form__field">
						<label for="ceredis-signup-city">Ville</label>
						<input id="ceredis-signup-city" name="ville" type="text" autocomplete="address-level2" required />
					</div>
					<div class="ceredis-form__field">
						<label for="ceredis-signup-role">Votre fonction</label>
						<select id="ceredis-signup-role" name="fonction" required>
							<option value="" selected disabled>Choisissez une option</option>
							<option value="Chef d'établissement">Chef d'établissement</option>
							<option value="Enseignant·e">Enseignant·e</option>
							<option value="Autre">Autre</option>
						</select>
					</div>
					<div class="ceredis-form__field">
						<label for="ceredis-signup-email">E-mail</label>
						<input id="ceredis-signup-email" name="email" type="email" autocomplete="email" required />
					</div>
					<div class="ceredis-form__field">
						<label for="ceredis-signup-interest">Je m'intéresse à</label>
						<select id="ceredis-signup-interest" name="programme">
							<option value="" selected>Choisissez une option</option>
							<option value="Rallye scolaire (lycée)">Rallye scolaire (lycée)</option>
							<option value="ENAA (CP et CE1)">ENAA (CP et CE1)</option>
							<option value="Les deux">Les deux</option>
						</select>
					</div>
					<button class="ceredis-form__submit" type="submit">Envoyer ma demande</button>
				</form>
				<!-- /wp:html -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"anchor":"contact","className":"ceredis-signup-contact__contact","layout":{"type":"constrained"}} -->
			<div class="wp-block-group ceredis-signup-contact__contact" id="contact">
				<!-- wp:paragraph {"className":"ceredis-eyebrow"} -->
				<p class="ceredis-eyebrow">ÉCHANGER AVEC CEREDIS</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2} -->
				<h2 class="wp-block-heading">Vous représentez une entreprise ou une institution ?</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Parlons du Rallye, de la façon dont votre marque peut s'y associer et de l'impact que vous souhaitez mesurer.</p>
				<!-- /wp:paragraph -->
				<!-- wp:buttons {"className":"ceredis-signup-contact__actions"} -->
				<div class="wp-block-buttons ceredis-signup-contact__actions">
					<!-- wp:button {"className":"ceredis-button--cyan"} -->
					<div class="wp-block-button ceredis-button--cyan"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $contact_mailto ); ?>">Écrire à l'équipe</a></div>
					<!-- /wp:button -->
					<!-- wp:button {"className":"ceredis-button--outline"} -->
					<div class="wp-block-button ceredis-button--outline"><a class="wp-block-button__link wp-element-button" href="tel:+242064061534">Appeler l'équipe</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
				<!-- wp:list {"className":"ceredis-contact__details"} -->
				<ul class="wp-block-list ceredis-contact__details">
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>E-mail :</strong> <a href="<?php echo esc_url( $contact_mailto ); ?>">contact@ceredis.net</a></li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Téléphone :</strong> <a href="tel:+242064061534">(+242) 06 406 1534</a></li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Brazzaville :</strong> 43 B Avenue du Djoué, Quartier La Milice, République du Congo</li>
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
