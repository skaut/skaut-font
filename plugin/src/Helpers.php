<?php

declare( strict_types=1 );

namespace Skautfont;

class Helpers {

	public static function showAdminNotice( string $message, string $type = 'warning', string $hideNoticeOnPage = '' ) {
		add_action( 'admin_notices', function () use ( $message, $type, $hideNoticeOnPage ) {
			if ( ! $hideNoticeOnPage || $hideNoticeOnPage != get_current_screen()->id ) {
				$class = 'notice notice-' . $type . ' is-dismissible';
				printf( '<div class="%1$s"><p>%2$s</p><button type="button" class="notice-dismiss">
		<span class="screen-reader-text">' . __( 'Close' ) . '</span>
	</button></div>', esc_attr( $class ), $message );
			}
		} );
	}

	public static function getSkautfontManagerCapability(): string {
		static $capability = '';

		if ( $capability === '' ) {
			$capability = apply_filters( SKAUTFONT_NAME . '_manager_capability', 'edit_theme_options' );
		}

		return $capability;
	}

	public static function userIsSkautfontManager(): bool {
		return current_user_can( self::getSkautfontManagerCapability() );
	}

	public static function getCurrentUrl(): string {
		return ( isset( $_SERVER['HTTPS'] ) ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	public static function validateNonceFromUrl( string $url, string $nonceName ) {
		if ( ! wp_verify_nonce( self::getNonceFromUrl( urldecode( $url ), $nonceName ), $nonceName ) ) {
			wp_nonce_ays( $nonceName );
		}
	}

	public static function getNonceFromUrl( string $url, string $nonceName ): string {
		$result = [];
		if ( preg_match( "~" . $nonceName . "=([^\&,\s,\/,\#,\%,\?]*)~", $url, $result ) ) {
			if ( is_array( $result ) && isset( $result[1] ) && $result[1] ) {
				return $result[1];
			}
		}

		return '';
	}

}
