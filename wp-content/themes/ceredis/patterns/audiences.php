<?php
/**
 * Title: CEREDIS — Publics
 * Slug: ceredis/audiences
 * Categories: ceredis
 * Inserter: yes
 */
$rallye_illustration = 'illustration-publics-rallye.webp';
$enaa_illustration   = 'illustration-publics-enaa.webp';
?>

<!-- wp:group {"anchor":"publics","align":"full","className":"ceredis-section ceredis-section--light ceredis-audiences","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull ceredis-section ceredis-section--light ceredis-audiences" id="publics">
	<!-- wp:group {"className":"ceredis-section__inner","layout":{"type":"flex","orientation":"vertical"}} -->
	<div class="wp-block-group ceredis-section__inner">

		<!-- wp:group {"className":"ceredis-audiences__intro","layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group ceredis-audiences__intro">
			<!-- wp:paragraph {"className":"ceredis-eyebrow"} -->
			<p class="ceredis-eyebrow">VOTRE PLACE DANS CEREDIS</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2} -->
			<h2 class="wp-block-heading">Deux plateformes, une même ambition : que chaque élève progresse.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"ceredis-lead"} -->
			<p class="ceredis-lead">Que vous enseigniez en lycée ou au CP, il y a une porte d'entrée pour vous.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"anchor":"rallye","className":"ceredis-audience ceredis-audience--rallye","layout":{"type":"default"}} -->
		<div class="wp-block-group ceredis-audience ceredis-audience--rallye" id="rallye">
			<!-- wp:group {"tagName":"article","className":"ceredis-card ceredis-audience__card","layout":{"type":"flex","orientation":"vertical"}} -->
			<article class="wp-block-group ceredis-card ceredis-audience__card">
				<!-- wp:group {"className":"ceredis-audience__tags","layout":{"type":"flex","flexWrap":"wrap"}} -->
				<div class="wp-block-group ceredis-audience__tags">
					<!-- wp:paragraph {"className":"ceredis-chip--accent"} -->
					<p class="ceredis-chip--accent">Lycée · Seconde et Première</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"ceredis-chip"} -->
					<p class="ceredis-chip">Français</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"ceredis-chip"} -->
					<p class="ceredis-chip">1<sup>re</sup> édition : janvier 2027</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
				<!-- wp:heading {"level":3} -->
				<h3 class="wp-block-heading">Le Rallye scolaire : vos élèves se préparent, puis se mesurent.</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Vous enseignez le français en Seconde ou en Première ? Inscrivez votre établissement. Vos élèves s'entraînent sur la plateforme à partir de situations réelles de communication, des chansons françaises par exemple, puis s'affrontent dans une compétition en manches éliminatoires. Les finalistes repartent avec des prix.</p>
				<!-- /wp:paragraph -->
				<!-- wp:list {"className":"ceredis-audience__list"} -->
				<ul class="wp-block-list ceredis-audience__list">
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Vous, enseignant·e :</strong> des supports numériques alignés sur les programmes congolais, et un tableau de bord pour suivre la progression de chaque élève.</li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Vous, chef d'établissement :</strong> une compétition qui valorise votre lycée, sans aucun frais.</li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Vos élèves :</strong> un entraînement qui ressemble à un jeu, et une vraie compétition à gagner.</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->
				<!-- wp:group {"className":"ceredis-audience__actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
				<div class="wp-block-group ceredis-audience__actions">
					<!-- wp:buttons -->
					<div class="wp-block-buttons">
						<!-- wp:button {"className":"ceredis-button--primary"} -->
						<div class="wp-block-button ceredis-button--primary"><a class="wp-block-button__link wp-element-button" href="/#inscription">Inscrire mon lycée au Rallye</a></div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
					<!-- wp:paragraph -->
					<p><a href="https://rallye.ceredis.net" target="_blank" rel="noopener">Voir la plateforme ↗</a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</article>
			<!-- /wp:group -->

			<!-- wp:html -->
			<div class="ceredis-audience__art"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/' . $rallye_illustration ) ); ?>" alt="Illustration : quatre lycéens regroupés devant un ordinateur, concentrés sur l'écran." width="900" height="1103" loading="lazy" decoding="async"/></div>
			<!-- /wp:html -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"anchor":"enaa","className":"ceredis-audience ceredis-audience--enaa","layout":{"type":"default"}} -->
		<div class="wp-block-group ceredis-audience ceredis-audience--enaa" id="enaa">
			<!-- wp:html -->
			<div class="ceredis-audience__art"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/' . $enaa_illustration ) ); ?>" alt="Illustration : quatre écoliers en uniforme regroupés devant un ordinateur." width="900" height="1103" loading="lazy" decoding="async"/></div>
			<!-- /wp:html -->

			<!-- wp:group {"tagName":"article","className":"ceredis-card ceredis-audience__card","layout":{"type":"flex","orientation":"vertical"}} -->
			<article class="wp-block-group ceredis-card ceredis-audience__card">
				<!-- wp:group {"className":"ceredis-audience__tags","layout":{"type":"flex","flexWrap":"wrap"}} -->
				<div class="wp-block-group ceredis-audience__tags">
					<!-- wp:paragraph {"className":"ceredis-chip--accent"} -->
					<p class="ceredis-chip--accent">École primaire · CP et CE1</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"ceredis-chip"} -->
					<p class="ceredis-chip">Mathématiques</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"ceredis-chip"} -->
					<p class="ceredis-chip">Version pilote</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
				<!-- wp:heading {"level":3} -->
				<h3 class="wp-block-heading">ENAA : des mathématiques que vos élèves comprennent en jouant.</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Vous enseignez au CP ou au CE1 ? Avec ENAA, vos élèves construisent eux-mêmes les notions mathématiques à travers des jeux sérieux. Un tuteur virtuel les accompagne et ajuste le parcours à leur rythme, pendant que vous gardez la main sur votre classe.</p>
				<!-- /wp:paragraph -->
				<!-- wp:list {"className":"ceredis-audience__list"} -->
				<ul class="wp-block-list ceredis-audience__list">
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Vous, enseignant·e :</strong> un assistant pédagogique qui vous soutient et vous montre où en est chaque élève.</li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Vos élèves :</strong> un tuteur patient, des jeux, un parcours qui s'adapte à leur niveau.</li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li class="wp-block-list-item"><strong>Votre école :</strong> une innovation gratuite. Rejoignez les premières classes pilotes.</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->
				<!-- wp:group {"className":"ceredis-audience__actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
				<div class="wp-block-group ceredis-audience__actions">
					<!-- wp:buttons -->
					<div class="wp-block-buttons">
						<!-- wp:button {"className":"ceredis-button--navy"} -->
						<div class="wp-block-button ceredis-button--navy"><a class="wp-block-button__link wp-element-button" href="/#inscription">Tester ENAA avec ma classe</a></div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
					<!-- wp:paragraph -->
					<p><a href="https://enaa.ceredis.net" target="_blank" rel="noopener">Voir la plateforme ↗</a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</article>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- TODO-CONTENT: Vérifier que l'écusson représenté sur les uniformes (illustrations générées) ne ressemble pas à l'emblème d'un établissement ou d'une institution réels. -->
		<!-- wp:paragraph {"className":"ceredis-audiences__ai-note"} -->
		<p class="ceredis-audiences__ai-note">Illustrations générées par IA.</p>
		<!-- /wp:paragraph -->

	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
