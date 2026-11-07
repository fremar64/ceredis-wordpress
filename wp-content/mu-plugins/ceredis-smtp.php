<?php
/**
 * Plugin Name: CEREDIS SMTP
 * Description: Envoi des e-mails WordPress par SMTP authentifié. Configuration par variables d'environnement (CEREDIS_SMTP_*). Sans CEREDIS_SMTP_HOST, le comportement par défaut de WordPress est conservé.
 *
 * @package ceredis
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lit une variable d'environnement CEREDIS_SMTP_*.
 */
function ceredis_smtp_env( string $key ): string {
	$value = getenv( $key );
	return is_string( $value ) ? trim( $value ) : '';
}

/**
 * Le SMTP est-il configuré ?
 */
function ceredis_smtp_enabled(): bool {
	return '' !== ceredis_smtp_env( 'CEREDIS_SMTP_HOST' );
}

add_action(
	'phpmailer_init',
	static function ( $phpmailer ): void {
		if ( ! ceredis_smtp_enabled() ) {
			return;
		}

		$secure = strtolower( ceredis_smtp_env( 'CEREDIS_SMTP_SECURE' ) );
		$secure = in_array( $secure, array( 'ssl', 'tls', 'none' ), true ) ? $secure : 'tls';
		$port   = (int) ceredis_smtp_env( 'CEREDIS_SMTP_PORT' );
		if ( $port <= 0 ) {
			$port = ( 'ssl' === $secure ) ? 465 : 587;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host        = ceredis_smtp_env( 'CEREDIS_SMTP_HOST' );
		$phpmailer->Port        = $port;
		$phpmailer->SMTPSecure  = ( 'none' === $secure ) ? '' : $secure;
		$phpmailer->SMTPAutoTLS = ( 'none' !== $secure );
		$phpmailer->Timeout     = 15;

		$user = ceredis_smtp_env( 'CEREDIS_SMTP_USER' );
		if ( '' !== $user ) {
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = $user;
			$phpmailer->Password = ceredis_smtp_env( 'CEREDIS_SMTP_PASSWORD' );
		}

		$from = ceredis_smtp_env( 'CEREDIS_SMTP_FROM' );
		if ( '' !== $from && is_email( $from ) ) {
			$phpmailer->Sender = $from;
		}
	}
);

add_filter(
	'wp_mail_from',
	static function ( $from ) {
		$configured = ceredis_smtp_env( 'CEREDIS_SMTP_FROM' );
		return ( ceredis_smtp_enabled() && is_email( $configured ) ) ? $configured : $from;
	}
);

add_filter(
	'wp_mail_from_name',
	static function ( $name ) {
		$configured = ceredis_smtp_env( 'CEREDIS_SMTP_FROM_NAME' );
		return ( ceredis_smtp_enabled() && '' !== $configured ) ? $configured : $name;
	}
);

// Journal d'échec sans donnée personnelle (ni destinataire, ni contenu).
add_action(
	'wp_mail_failed',
	static function ( $error ): void {
		error_log( '[ceredis] wp_mail a échoué : ' . $error->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
);
