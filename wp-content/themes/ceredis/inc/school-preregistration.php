<?php
/**
 * Pré-inscription des établissements au Rallye scolaire.
 *
 * @package ceredis
 */

defined( 'ABSPATH' ) || exit;

const CEREDIS_PREREG_TO       = 'contact@ceredis.net';
const CEREDIS_PREREG_MIN_SECS = 3;
const CEREDIS_PREREG_MAX_SECS = 86400;

function ceredis_school_prereg_redirect( string $status, string $field_error = '' ): void {
	$args = array( 'preinscription' => $status );
	if ( '' !== $field_error ) {
		$args['preinscription_champs'] = $field_error;
	}
	$url = add_query_arg( $args, home_url( '/' ) ) . '#inscription';
	wp_safe_redirect( $url );
	exit;
}

function ceredis_school_prereg_text( string $key, int $max, bool $textarea = false ): ?string {
	if ( ! isset( $_POST[ $key ] ) ) {
		return '';
	}

	if ( ! is_string( $_POST[ $key ] ) ) {
		return null;
	}

	$value = wp_unslash( $_POST[ $key ] );
	$value = $textarea ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	$value = trim( $value );

	return mb_strlen( $value ) <= $max ? $value : null;
}

function ceredis_school_prereg_choice( string $key, array $allowed, bool $required = false ): ?string {
	$value = ceredis_school_prereg_text( $key, 80 );
	if ( null === $value || ( '' !== $value && ! in_array( $value, $allowed, true ) ) ) {
		return null;
	}

	return $required && '' === $value ? null : $value;
}

/**
 * Incrémente le compteur d'une fenêtre horaire sans renouveler son expiration.
 */
function ceredis_school_prereg_increment_hourly_count( string $key ): bool {
	if ( wp_using_ext_object_cache() ) {
		return false !== wp_cache_incr( $key, 1, 'transient' );
	}

	$count = get_transient( $key );
	if ( false === $count ) {
		return set_transient( $key, 1, 2 * HOUR_IN_SECONDS );
	}

	return update_option( '_transient_' . $key, (int) $count + 1, false );
}

function ceredis_school_prereg_handle(): void {
	$nonce = isset( $_POST['ceredis_school_prereg_nonce'] ) && is_string( $_POST['ceredis_school_prereg_nonce'] )
		? sanitize_text_field( wp_unslash( $_POST['ceredis_school_prereg_nonce'] ) )
		: '';
	if ( ! wp_verify_nonce( $nonce, 'ceredis_school_prereg' ) ) {
		ceredis_school_prereg_redirect( 'erreur' );
	}

	if ( ! empty( $_POST['ceredis_school_prereg_hp'] ) ) {
		ceredis_school_prereg_redirect( 'ok' );
	}

	$started = isset( $_POST['ceredis_school_prereg_started'] ) && is_string( $_POST['ceredis_school_prereg_started'] )
		? filter_var( wp_unslash( $_POST['ceredis_school_prereg_started'] ), FILTER_VALIDATE_INT )
		: false;
	$elapsed = false !== $started ? time() - $started : 0;
	if ( false === $started || $elapsed < CEREDIS_PREREG_MIN_SECS || $elapsed > CEREDIS_PREREG_MAX_SECS ) {
		ceredis_school_prereg_redirect( 'erreur' );
	}

	$programme = ceredis_school_prereg_choice( 'programme', array( 'rallye', 'enaa', 'les_deux' ), true );
	if ( null === $programme ) {
		ceredis_school_prereg_redirect( 'erreur', 'programme-niveaux' );
	}

	$programmes = array(
		'rallye'   => array(
			'label'  => 'Rallye scolaire (lycée)',
			'niveaux' => array( 'Seconde', 'Première' ),
			'sujet'  => '[Pré-inscription Rallye]',
		),
		'enaa'     => array(
			'label'  => 'ENAA (CP et CE1)',
			'niveaux' => array( 'CP', 'CE1' ),
			'sujet'  => '[Pré-inscription ENAA]',
		),
		'les_deux' => array(
			'label'  => 'Les deux',
			'niveaux' => array( 'Seconde', 'Première', 'CP', 'CE1' ),
			'sujet'  => '[Pré-inscription Rallye + ENAA]',
		),
	);

	$niveaux = array();
	if ( isset( $_POST['niveaux'] ) ) {
		if ( ! is_array( $_POST['niveaux'] ) ) {
			ceredis_school_prereg_redirect( 'erreur', 'programme-niveaux' );
		}
		foreach ( wp_unslash( $_POST['niveaux'] ) as $niveau ) {
			if ( ! is_string( $niveau ) ) {
				ceredis_school_prereg_redirect( 'erreur', 'programme-niveaux' );
			}
			$niveau = sanitize_text_field( $niveau );
			if ( ! in_array( $niveau, array( 'Seconde', 'Première', 'CP', 'CE1' ), true ) ) {
				ceredis_school_prereg_redirect( 'erreur', 'programme-niveaux' );
			}
			if ( in_array( $niveau, $programmes[ $programme ]['niveaux'], true ) ) {
				$niveaux[] = $niveau;
			}
		}
	}
	$niveaux = array_values( array_unique( $niveaux ) );
	if ( empty( $niveaux ) ) {
		ceredis_school_prereg_redirect( 'erreur', 'programme-niveaux' );
	}

	$etablissement = ceredis_school_prereg_text( 'etablissement', 160 );
	$statut        = ceredis_school_prereg_choice( 'statut', array( 'Public', 'Privé' ), true );
	$ville         = ceredis_school_prereg_text( 'ville', 80 );
	$quartier      = ceredis_school_prereg_text( 'quartier', 80 );
	$nom           = ceredis_school_prereg_text( 'nom', 120 );
	$fonction      = ceredis_school_prereg_choice(
		'fonction',
		array( "Chef d'établissement", 'Directeur des études', 'Enseignant référent', 'Autre' )
	);
	$telephone     = ceredis_school_prereg_text( 'telephone', 40 );
	$message       = ceredis_school_prereg_text( 'message', 3000, true );
	$consent       = isset( $_POST['consent'] ) && '1' === $_POST['consent'];

	$email_raw = isset( $_POST['email'] ) && is_string( $_POST['email'] )
		? trim( wp_unslash( $_POST['email'] ) )
		: '';
	$email     = sanitize_email( $email_raw );

	$effectif = '';
	if ( isset( $_POST['effectif'] ) && is_string( $_POST['effectif'] ) ) {
		$effectif_raw = trim( wp_unslash( $_POST['effectif'] ) );
		if ( ctype_digit( $effectif_raw ) ) {
			$effectif_value = ltrim( $effectif_raw, '0' );
			$effectif_value = '' === $effectif_value ? '0' : $effectif_value;
			if ( strlen( $effectif_value ) <= 4 && (int) $effectif_value <= 2000 ) {
				$effectif = $effectif_value;
			}
		}
	}

	$equipement = ceredis_school_prereg_choice( 'equipement', array( 'Oui', 'En partie', 'Non' ) );

	if (
		null === $etablissement
		|| '' === $etablissement
		|| null === $statut
		|| null === $ville
		|| '' === $ville
		|| null === $quartier
		|| null === $nom
		|| '' === $nom
		|| null === $fonction
		|| null === $telephone
		|| '' === $telephone
		|| null === $message
		|| null === $equipement
		|| '' === $email_raw
		|| strlen( $email_raw ) > 254
		|| ! is_email( $email )
		|| ! $consent
	) {
		ceredis_school_prereg_redirect( 'erreur' );
	}

	$email_key = 'ceredis_school_prereg_email_' . wp_hash( strtolower( $email ), 'auth' );
	if ( false !== get_transient( $email_key ) ) {
		ceredis_school_prereg_redirect( 'limite' );
	}

	$count_key = 'ceredis_school_prereg_hourly_count_' . gmdate( 'YmdH' );
	$count     = get_transient( $count_key );
	$count     = false === $count ? 0 : (int) $count;
	if ( $count >= 30 ) {
		ceredis_school_prereg_redirect( 'limite' );
	}

	$programme_label = $programmes[ $programme ]['label'];
	$subject         = (string) preg_replace( '/[\x00-\x1F]/', '', $programmes[ $programme ]['sujet'] . ' ' . $etablissement );
	$body    = "Nom de l'établissement : {$etablissement}\n"
		. "Programme : {$programme_label}\n"
		. "Statut : {$statut}\n"
		. "Ville : {$ville}\n"
		. "Quartier ou arrondissement : {$quartier}\n"
		. "Niveaux concernés : " . implode( ', ', $niveaux ) . "\n"
		. "Nombre approximatif d'élèves intéressés : {$effectif}\n"
		. "Salle informatique ou ordinateurs disponibles : {$equipement}\n"
		. "Nom et prénom du contact : {$nom}\n"
		. "Fonction : {$fonction}\n"
		. "Adresse e-mail : {$email}\n"
		. "Téléphone : {$telephone}\n"
		. "Consentement : Oui\n\n"
		. "Message :\n{$message}\n";

	$safe_name = trim( str_replace( array( '<', '>', '"', ',', ';', "\r", "\n" ), '', $nom ) );
	$headers   = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $safe_name . ' <' . $email . '>',
	);

	if ( ! wp_mail( CEREDIS_PREREG_TO, $subject, $body, $headers ) ) {
		error_log( '[ceredis] Pré-inscription Rallye : envoi de la demande échoué.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		ceredis_school_prereg_redirect( 'erreur' );
	}

	if ( ! set_transient( $email_key, 1, HOUR_IN_SECONDS ) ) {
		error_log( '[ceredis] Pré-inscription Rallye : impossible de définir la limite par adresse.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
	if ( ! ceredis_school_prereg_increment_hourly_count( $count_key ) ) {
		error_log( '[ceredis] Pré-inscription Rallye : impossible de mettre à jour le compteur horaire.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	$receipt_subject = 'CEREDIS — Nous avons bien reçu votre pré-inscription';
	$receipt_intro   = array(
		'rallye'   => "Nous avons bien reçu la pré-inscription de {$etablissement} au programme Rallye scolaire (lycée).",
		'enaa'     => "Nous avons bien reçu la pré-inscription de {$etablissement} au programme ENAA (CP et CE1).",
		'les_deux' => "Nous avons bien reçu la pré-inscription de {$etablissement} aux programmes Rallye scolaire (lycée) et ENAA (CP et CE1).",
	)[ $programme ];
	$receipt_body    = "Bonjour {$nom},\n\n"
		. "{$receipt_intro} Merci de l'intérêt que vous portez à cette initiative.\n\n"
		. ( 'enaa' === $programme
			? "Il s'agit d'une manifestation d'intérêt, sans engagement de part ni d'autre. Nous reviendrons vers vous pour étudier avec vous la participation de votre classe aux premières classes pilotes d'ENAA.\n\n"
			: "Il s'agit d'une manifestation d'intérêt, sans engagement de part ni d'autre. Le calendrier ci-dessous est prévisionnel et reste soumis à la confirmation du financement de la première édition :\n"
				. "- inscriptions des établissements : du 4 janvier au 27 février 2027 ;\n"
				. "- préparation des élèves : du 1er mars au 15 mai 2027 ;\n"
				. "- période du Rallye : du 17 mai au 30 juin 2027, avec les finales le 19 juin.\n\n"
				. ( 'les_deux' === $programme
					? "Concernant ENAA, nous reviendrons vers vous pour étudier avec vous la participation de votre classe aux premières classes pilotes.\n\n"
					: '' )
				. "À l'ouverture des inscriptions, nous reviendrons vers vous pour préciser les pièces nécessaires et les prochaines étapes.\n\n" )
		. "Pour toute question, répondez simplement à ce message.\n\n"
		. "L'équipe CEREDIS\n"
		. "https://ceredis.net";

	if ( ! wp_mail( $email, $receipt_subject, $receipt_body, array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . CEREDIS_PREREG_TO ) ) ) {
		error_log( '[ceredis] Pré-inscription Rallye : envoi de l’accusé de réception échoué.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	ceredis_school_prereg_redirect( 'ok' );
}
add_action( 'admin_post_ceredis_school_prereg', 'ceredis_school_prereg_handle' );
add_action( 'admin_post_nopriv_ceredis_school_prereg', 'ceredis_school_prereg_handle' );

function ceredis_school_prereg_render(): string {
	$status = isset( $_GET['preinscription'] ) && is_string( $_GET['preinscription'] )
		? sanitize_key( wp_unslash( $_GET['preinscription'] ) )
		: '';

	ob_start();
	?>
	<div class="ceredis-preregistration">
		<?php if ( 'ok' === $status ) : ?>
			<div class="ceredis-preregistration__status ceredis-preregistration__status--ok" role="status">
				Merci, votre pré-inscription a bien été envoyée. Un accusé de réception vous est adressé par e-mail.
			</div>
		<?php elseif ( 'erreur' === $status ) : ?>
			<div class="ceredis-preregistration__status ceredis-preregistration__status--error" role="alert">
				Votre demande n'a pas pu être envoyée. Vérifiez les champs obligatoires ou écrivez-nous à
				<a href="mailto:contact@ceredis.net">contact@ceredis.net</a>.
				<?php if ( isset( $_GET['preinscription_champs'] ) && is_string( $_GET['preinscription_champs'] ) && 'programme-niveaux' === sanitize_key( wp_unslash( $_GET['preinscription_champs'] ) ) ) : ?>
					<p>Choisissez un programme et au moins un niveau concerné.</p>
				<?php endif; ?>
			</div>
		<?php elseif ( 'limite' === $status ) : ?>
			<div class="ceredis-preregistration__status ceredis-preregistration__status--error" role="alert">
				<p>Une demande vient d'être enregistrée avec cette adresse, ou le formulaire reçoit beaucoup de demandes. Réessayez plus tard ou écrivez-nous à <a href="mailto:contact@ceredis.net">contact@ceredis.net</a>.</p>
			</div>
		<?php endif; ?>

		<form class="ceredis-preregistration__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ceredis_school_prereg">
			<?php wp_nonce_field( 'ceredis_school_prereg', 'ceredis_school_prereg_nonce' ); ?>
			<input type="hidden" name="ceredis_school_prereg_started" value="<?php echo esc_attr( (string) time() ); ?>">

			<p class="ceredis-preregistration__required-note">Les champs marqués d'un * sont obligatoires.</p>

			<div class="ceredis-preregistration__trap" aria-hidden="true">
				<label for="csp-hp">Ne pas remplir
					<input id="csp-hp" type="text" name="ceredis_school_prereg_hp" tabindex="-1" autocomplete="off">
				</label>
			</div>

			<div class="ceredis-preregistration__field ceredis-preregistration__field--full">
				<label for="csp-programme">Je m'intéresse à <span class="ceredis-preregistration__req" aria-hidden="true">*</span></label>
				<select id="csp-programme" name="programme" required>
					<option value="" selected disabled>Choisissez une option</option>
					<option value="rallye">Rallye scolaire (lycée)</option>
					<option value="enaa">ENAA (CP et CE1)</option>
					<option value="les_deux">Les deux</option>
				</select>
			</div>

			<div class="ceredis-preregistration__field">
				<label for="csp-etablissement">Nom de l'établissement <span class="ceredis-preregistration__req" aria-hidden="true">*</span></label>
				<input id="csp-etablissement" type="text" name="etablissement" maxlength="160" autocomplete="organization" required>
			</div>
			<div class="ceredis-preregistration__field">
				<label for="csp-statut">Statut <span class="ceredis-preregistration__req" aria-hidden="true">*</span></label>
				<select id="csp-statut" name="statut" required>
					<option value="" selected disabled>Choisissez une option</option>
					<option value="Public">Public</option>
					<option value="Privé">Privé</option>
				</select>
			</div>
			<div class="ceredis-preregistration__field">
				<label for="csp-ville">Ville <span class="ceredis-preregistration__req" aria-hidden="true">*</span></label>
				<input id="csp-ville" type="text" name="ville" maxlength="80" autocomplete="address-level2" required>
			</div>
			<div class="ceredis-preregistration__field">
				<label for="csp-quartier">Quartier ou arrondissement</label>
				<input id="csp-quartier" type="text" name="quartier" maxlength="80">
			</div>

			<fieldset class="ceredis-preregistration__fieldset">
				<legend>Niveaux concernés <span class="ceredis-preregistration__req" aria-hidden="true">*</span> — Rallye : Seconde, Première · ENAA : CP, CE1</legend>
				<div class="ceredis-preregistration__choices" aria-required="true">
					<label class="ceredis-preregistration__choice" for="csp-niveau-seconde">
						<input id="csp-niveau-seconde" type="checkbox" name="niveaux[]" value="Seconde">
						<span>Seconde</span>
					</label>
					<label class="ceredis-preregistration__choice" for="csp-niveau-premiere">
						<input id="csp-niveau-premiere" type="checkbox" name="niveaux[]" value="Première">
						<span>Première</span>
					</label>
					<label class="ceredis-preregistration__choice" for="csp-niveau-cp">
						<input id="csp-niveau-cp" type="checkbox" name="niveaux[]" value="CP">
						<span>CP</span>
					</label>
					<label class="ceredis-preregistration__choice" for="csp-niveau-ce1">
						<input id="csp-niveau-ce1" type="checkbox" name="niveaux[]" value="CE1">
						<span>CE1</span>
					</label>
				</div>
			</fieldset>

			<div class="ceredis-preregistration__field">
				<label for="csp-effectif">Nombre approximatif d'élèves intéressés</label>
				<input id="csp-effectif" type="number" name="effectif" min="0" max="2000" step="1">
			</div>
			<div class="ceredis-preregistration__field">
				<label for="csp-equipement">Salle informatique ou ordinateurs disponibles</label>
				<select id="csp-equipement" name="equipement">
					<option value="" selected>Choisissez une option</option>
					<option value="Oui">Oui</option>
					<option value="En partie">En partie</option>
					<option value="Non">Non</option>
				</select>
			</div>

			<div class="ceredis-preregistration__field">
				<label for="csp-nom">Nom et prénom du contact <span class="ceredis-preregistration__req" aria-hidden="true">*</span></label>
				<input id="csp-nom" type="text" name="nom" maxlength="120" autocomplete="name" required>
			</div>
			<div class="ceredis-preregistration__field">
				<label for="csp-fonction">Fonction</label>
				<select id="csp-fonction" name="fonction">
					<option value="" selected>Choisissez une option</option>
					<option value="Chef d'établissement">Chef d'établissement</option>
					<option value="Directeur des études">Directeur des études</option>
					<option value="Enseignant référent">Enseignant référent</option>
					<option value="Autre">Autre</option>
				</select>
			</div>
			<div class="ceredis-preregistration__field">
				<label for="csp-email">Adresse e-mail <span class="ceredis-preregistration__req" aria-hidden="true">*</span></label>
				<input id="csp-email" type="email" name="email" maxlength="254" autocomplete="email" required>
			</div>
			<div class="ceredis-preregistration__field">
				<label for="csp-telephone">Téléphone <span class="ceredis-preregistration__req" aria-hidden="true">*</span></label>
				<input id="csp-telephone" type="tel" name="telephone" maxlength="40" autocomplete="tel" required>
			</div>

			<div class="ceredis-preregistration__field ceredis-preregistration__field--full">
				<label for="csp-message">Message</label>
				<textarea id="csp-message" name="message" rows="4" maxlength="3000"></textarea>
			</div>

			<fieldset class="ceredis-preregistration__fieldset ceredis-preregistration__fieldset--full">
				<legend>Consentement <span class="ceredis-preregistration__req" aria-hidden="true">*</span></legend>
				<label class="ceredis-preregistration__consent" for="csp-consent">
					<input id="csp-consent" type="checkbox" name="consent" value="1" required>
					<span>J&#039;accepte que CEREDIS utilise ces informations pour me recontacter au sujet de cette pré-inscription.</span>
				</label>
				<?php $privacy_policy_url = get_privacy_policy_url(); ?>
				<?php if ( '' !== $privacy_policy_url ) : ?>
					<a class="ceredis-preregistration__privacy" href="<?php echo esc_url( $privacy_policy_url ); ?>">Politique de confidentialité</a>
				<?php endif; ?>
			</fieldset>

			<p class="ceredis-preregistration__notice ceredis-preregistration__field--full">
				Pré-inscription sans engagement. Le calendrier de la première édition du Rallye est prévisionnel et reste soumis à la confirmation du financement.
			</p>
			<div class="ceredis-preregistration__field ceredis-preregistration__field--full">
				<button class="ceredis-preregistration__submit" type="submit">Envoyer ma pré-inscription</button>
			</div>
		</form>
	</div>
	<?php
	return (string) ob_get_clean();
}

function ceredis_school_prereg_register_block(): void {
	register_block_type(
		'ceredis/school-preregistration',
		array(
			'render_callback' => 'ceredis_school_prereg_render',
		)
	);
}
add_action( 'init', 'ceredis_school_prereg_register_block' );
