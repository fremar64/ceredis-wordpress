<?php
/**
 * Formulaire « Demande de partenariat » (page /partenaires/).
 *
 * - Shortcode : [ceredis_partner_form]
 * - Traitement : admin-post.php (action ceredis_partner_form)
 * - Protections : nonce, champ piège, délai minimal, liste blanche, limites de longueur
 * - Aucune donnée stockée en base. Envoi par wp_mail() à contact@ceredis.net
 *
 * @package ceredis
 */

defined( 'ABSPATH' ) || exit;

const CEREDIS_PARTNER_FORM_TO       = 'contact@ceredis.net';
const CEREDIS_PARTNER_FORM_MIN_SECS = 3;      // en dessous : probablement un robot.
const CEREDIS_PARTNER_FORM_MAX_SECS = 86400;  // au-delà : formulaire périmé.

/**
 * Redirige vers la page Partenaires avec un statut, puis termine la requête.
 */
function ceredis_partner_form_redirect( string $status ): void {
	$url = add_query_arg( 'demande', $status, home_url( '/partenaires/' ) ) . '#demande';
	wp_safe_redirect( $url );
	exit;
}

/**
 * Lit un champ texte POST, le nettoie et le tronque.
 */
function ceredis_partner_form_text( string $key, int $max = 200 ): string {
	if ( ! isset( $_POST[ $key ] ) || ! is_string( $_POST[ $key ] ) ) {
		return '';
	}
	$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
	return mb_substr( $value, 0, $max );
}

/**
 * Incrémente le compteur d'une fenêtre horaire sans renouveler son expiration.
 */
function ceredis_partner_form_increment_hourly_count( string $key ): bool {
	if ( wp_using_ext_object_cache() ) {
		return false !== wp_cache_incr( $key, 1, 'transient' );
	}

	$count = get_transient( $key );
	if ( false === $count ) {
		return set_transient( $key, 1, 2 * HOUR_IN_SECONDS );
	}

	return update_option( '_transient_' . $key, (int) $count + 1, false );
}

/**
 * Traitement du formulaire.
 */
function ceredis_partner_form_handle(): void {
	// 1. Nonce.
	$nonce = isset( $_POST['ceredis_partner_nonce'] ) && is_string( $_POST['ceredis_partner_nonce'] )
		? sanitize_text_field( wp_unslash( $_POST['ceredis_partner_nonce'] ) )
		: '';
	if ( ! wp_verify_nonce( $nonce, 'ceredis_partner_form' ) ) {
		ceredis_partner_form_redirect( 'erreur' );
	}

	// 2. Champ piège : on répond « ok » pour ne rien apprendre aux robots.
	if ( ! empty( $_POST['ceredis_partner_hp'] ) ) {
		ceredis_partner_form_redirect( 'ok' );
	}

	// 3. Délai : le champ est OBLIGATOIRE (son absence est suspecte).
	$started = isset( $_POST['ceredis_partner_started'] ) ? (int) $_POST['ceredis_partner_started'] : 0;
	$elapsed = time() - $started;
	if ( $started <= 0 || $elapsed < CEREDIS_PARTNER_FORM_MIN_SECS || $elapsed > CEREDIS_PARTNER_FORM_MAX_SECS ) {
		ceredis_partner_form_redirect( 'erreur' );
	}

	// 4. Lecture et nettoyage.
	$profil       = ceredis_partner_form_text( 'profil', 60 );
	$nom          = ceredis_partner_form_text( 'nom', 120 );
	$organisation = ceredis_partner_form_text( 'organisation', 160 );
	$fonction     = ceredis_partner_form_text( 'fonction', 120 );
	$telephone    = ceredis_partner_form_text( 'telephone', 40 );
	$programme    = ceredis_partner_form_text( 'programme', 40 );
	$soutien      = ceredis_partner_form_text( 'soutien', 40 );
	$email        = isset( $_POST['email'] ) && is_string( $_POST['email'] )
		? sanitize_email( wp_unslash( $_POST['email'] ) )
		: '';
	$message      = isset( $_POST['message'] ) && is_string( $_POST['message'] )
		? mb_substr( sanitize_textarea_field( wp_unslash( $_POST['message'] ) ), 0, 5000 )
		: '';
	$consent      = ! empty( $_POST['consent'] );

	// 5. Listes blanches.
	$profils    = array( 'Une entreprise', 'Une institution ou une fondation' );
	$programmes = array( 'Le Rallye scolaire', 'ENAA', 'Les deux' );
	$soutiens   = array( 'Financier', 'En nature', 'Technique', 'Je ne sais pas encore' );

	// 6. Champs obligatoires.
	if (
		! in_array( $profil, $profils, true )
		|| ( '' !== $programme && ! in_array( $programme, $programmes, true ) )
		|| ( '' !== $soutien && ! in_array( $soutien, $soutiens, true ) )
		|| '' === $nom
		|| '' === $organisation
		|| ! is_email( $email )
		|| ! $consent
	) {
		ceredis_partner_form_redirect( 'erreur' );
	}

	$email_key = 'ceredis_partner_form_email_' . wp_hash( strtolower( $email ), 'auth' );
	$count_key = 'ceredis_partner_hourly_count_' . gmdate( 'YmdH' );
	$count     = get_transient( $count_key );
	$count     = false === $count ? 0 : (int) $count;
	if ( false !== get_transient( $email_key ) || $count >= 20 ) {
		ceredis_partner_form_redirect( 'limite' );
	}

	// 7. Message (texte brut, UTF-8).
	$subject = (string) preg_replace( '/[\x00-\x1F]/', '', '[Partenariat] ' . $organisation );
	$body    = "Profil : {$profil}\n"
		. "Nom et prénom : {$nom}\n"
		. "Organisation : {$organisation}\n"
		. "Fonction : {$fonction}\n"
		. "E-mail : {$email}\n"
		. "Téléphone : {$telephone}\n"
		. "Programme : {$programme}\n"
		. "Soutien : {$soutien}\n\n"
		. "Message :\n{$message}\n";

	// Le nom est inséré dans un en-tête : on retire les caractères sensibles.
	$safe_name = trim( str_replace( array( '<', '>', '"', ',', ';', "\r", "\n" ), '', $nom ) );
	$headers   = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $safe_name . ' <' . $email . '>',
	);

	$sent = wp_mail( CEREDIS_PARTNER_FORM_TO, $subject, $body, $headers );

	if ( ! $sent ) {
		// Journal sans donnée personnelle, pour détecter un SMTP mal configuré.
		error_log( '[ceredis] Formulaire partenaire : wp_mail() a échoué.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		ceredis_partner_form_redirect( 'erreur' );
	}

	if ( ! set_transient( $email_key, 1, HOUR_IN_SECONDS ) ) {
		error_log( '[ceredis] Formulaire partenaire : impossible de définir la limite par adresse.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
	if ( ! ceredis_partner_form_increment_hourly_count( $count_key ) ) {
		error_log( '[ceredis] Formulaire partenaire : impossible de mettre à jour le compteur horaire.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	ceredis_partner_form_redirect( 'ok' );
}
add_action( 'admin_post_ceredis_partner_form', 'ceredis_partner_form_handle' );
add_action( 'admin_post_nopriv_ceredis_partner_form', 'ceredis_partner_form_handle' );

/**
 * Shortcode : affiche le message de statut puis le formulaire.
 */
function ceredis_partner_form_shortcode(): string {
	$status = isset( $_GET['demande'] ) && is_string( $_GET['demande'] )
		? sanitize_key( wp_unslash( $_GET['demande'] ) )
		: '';

	ob_start();
	?>
	<div class="ceredis-partner-form">
		<h3 class="ceredis-partner-form__title">Demande de partenariat</h3>

		<?php if ( 'ok' === $status ) : ?>
			<div class="ceredis-partner-form__status ceredis-partner-form__status--ok" role="status">
				Merci, votre demande a bien été envoyée. Nous vous répondons personnellement.
			</div>
		<?php elseif ( 'erreur' === $status ) : ?>
			<div class="ceredis-partner-form__status ceredis-partner-form__status--error" role="alert">
				Votre demande n'a pas pu être envoyée. Vérifiez les champs obligatoires ou écrivez-nous à
				<a href="mailto:contact@ceredis.net">contact@ceredis.net</a>.
			</div>
		<?php elseif ( 'limite' === $status ) : ?>
			<div class="ceredis-partner-form__status ceredis-partner-form__status--error" role="alert">
				Le formulaire ne peut pas accepter cette demande pour le moment. Réessayez plus tard ou écrivez-nous à
				<a href="mailto:contact@ceredis.net">contact@ceredis.net</a>.
			</div>
		<?php endif; ?>

		<form class="ceredis-partner-form__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ceredis_partner_form">
			<?php wp_nonce_field( 'ceredis_partner_form', 'ceredis_partner_nonce' ); ?>
			<input type="hidden" name="ceredis_partner_started" value="<?php echo esc_attr( (string) time() ); ?>">

			<div class="ceredis-partner-form__trap" aria-hidden="true">
				<label>Ne pas remplir
					<input type="text" name="ceredis_partner_hp" tabindex="-1" autocomplete="off">
				</label>
			</div>

			<fieldset class="ceredis-partner-form__fieldset">
				<legend>Vous représentez</legend>
				<div class="ceredis-partner-form__choices">
					<label class="ceredis-partner-form__choice">
						<input type="radio" name="profil" value="Une entreprise" required>
						<span>Une entreprise</span>
					</label>
					<label class="ceredis-partner-form__choice">
						<input type="radio" name="profil" value="Une institution ou une fondation" required>
						<span>Une institution ou une fondation</span>
					</label>
				</div>
			</fieldset>

			<div class="ceredis-partner-form__field ceredis-partner-form__field--full">
				<label for="cpf-nom">Nom et prénom</label>
				<input id="cpf-nom" type="text" name="nom" required maxlength="120" autocomplete="name">
			</div>

			<div class="ceredis-partner-form__field">
				<label for="cpf-organisation">Organisation</label>
				<input id="cpf-organisation" type="text" name="organisation" required maxlength="160" autocomplete="organization">
			</div>
			<div class="ceredis-partner-form__field">
				<label for="cpf-fonction">Fonction</label>
				<input id="cpf-fonction" type="text" name="fonction" maxlength="120" autocomplete="organization-title">
			</div>

			<div class="ceredis-partner-form__field">
				<label for="cpf-email">E-mail professionnel</label>
				<input id="cpf-email" type="email" name="email" required maxlength="200" autocomplete="email">
			</div>
			<div class="ceredis-partner-form__field">
				<label for="cpf-telephone">Téléphone (facultatif)</label>
				<input id="cpf-telephone" type="tel" name="telephone" maxlength="40" autocomplete="tel">
			</div>

			<div class="ceredis-partner-form__field">
				<label for="cpf-programme">Programme concerné</label>
				<select id="cpf-programme" name="programme">
					<option value="">Choisissez une option</option>
					<option value="Le Rallye scolaire">Le Rallye scolaire</option>
					<option value="ENAA">ENAA</option>
					<option value="Les deux">Les deux</option>
				</select>
			</div>
			<div class="ceredis-partner-form__field">
				<label for="cpf-soutien">Type de soutien envisagé</label>
				<select id="cpf-soutien" name="soutien">
					<option value="">Choisissez une option</option>
					<option value="Financier">Financier</option>
					<option value="En nature">En nature</option>
					<option value="Technique">Technique</option>
					<option value="Je ne sais pas encore">Je ne sais pas encore</option>
				</select>
			</div>

			<div class="ceredis-partner-form__field ceredis-partner-form__field--full">
				<label for="cpf-message">Votre message</label>
				<textarea id="cpf-message" name="message" rows="4" maxlength="5000"></textarea>
			</div>

			<div class="ceredis-partner-form__field ceredis-partner-form__field--full ceredis-partner-form__consent">
				<label>
					<input type="checkbox" name="consent" value="1" required>
					<span>J'accepte que CEREDIS utilise ces informations pour répondre à ma demande.</span>
				</label>
			</div>

			<div class="ceredis-partner-form__field ceredis-partner-form__field--full">
				<button type="submit" class="ceredis-partner-form__submit">Envoyer ma demande</button>
			</div>
		</form>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'ceredis_partner_form', 'ceredis_partner_form_shortcode' );

/**
 * Bloc dynamique ceredis/partner-form.
 *
 * Les shortcodes ne sont PAS exécutés dans les templates et patterns d'un thème
 * à blocs : on enregistre donc un vrai bloc rendu côté serveur, utilisable dans
 * un pattern avec <!-- wp:ceredis/partner-form /-->.
 * Le nonce, l'horodatage et le message de statut sont ainsi produits à chaque requête.
 */
function ceredis_partner_form_register_block(): void {
	register_block_type(
		'ceredis/partner-form',
		array(
			'api_version'     => 3,
			'title'           => 'Formulaire de demande de partenariat',
			'category'        => 'widgets',
			'supports'        => array( 'inserter' => false ),
			'render_callback' => 'ceredis_partner_form_shortcode',
		)
	);
}
add_action( 'init', 'ceredis_partner_form_register_block' );
