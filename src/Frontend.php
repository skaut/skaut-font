<?php

declare( strict_types=1 );

namespace Skautfont;

final class Frontend {

	private $fonts = [];
	private $styles = [];
	private $frontendDirUrl = '';

	public function __construct( array $fonts, array $styles ) {
		$this->fonts          = $fonts;
		$this->styles         = $styles;
		$this->frontendDirUrl = SKAUTFONT_URL . 'public/';

		$this->initHooks();
	}

	private function initHooks() {
		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueueStyles' ] );
		}
	}

	private function getBodyCss(): string {
		$styleBody = get_option( SKAUTFONT_NAME . '_style_body' );
		if ( $styleBody === 'themix' ) {
			return '
body {
    font-family: themix, sans-serif !important;
}

p {
    font-weight: 400 !important;
    font-style: normal;
}
			';
		} else if ( $styleBody === 'skautbold' ) {
			return '
body {
    font-family: themix, sans-serif !important;
}
			';
		}

		return '';
	}

	private function getTitlesCss(): string {
		$styleTitles = get_option( SKAUTFONT_NAME . '_style_titles' );
		if ( $styleTitles === 'themix' ) {
			return '
h1,
h2,
h3,
h4,
h5,
h6 {
    font-family: themix, sans-serif !important;
    font-weight: normal;
}
			';
		} else if ( $styleTitles === 'skautbold' ) {
			return '
h1,
h2,
h3,
h4,
h5,
h6 {
    font-family: skautbold, sans-serif !important;
    font-weight: normal;
}
			';
		}

		return '';
	}

	private function getSiteDescCss(): string {
		$styleSiteDesc = get_option( SKAUTFONT_NAME . '_style_site-desc' );
		if ( $styleSiteDesc === 'themix' ) {
			return '
#site-description {
    font-family: themix, sans-serif !important;
}
		';
		} else if ( $styleSiteDesc === 'skautbold' ) {
			return '
#site-description {
    font-family: skautbold, sans-serif !important;
}
		';
		}

		return '';
	}

	private function getCss() {
		$styles = '';

		$styles .= $this->getBodyCss();
		$styles .= $this->getTitlesCss();
		$styles .= $this->getSiteDescCss();

		return $styles;
	}

	public function getFonts() {
		return $this->fonts;
	}

	public function getStyles() {
		return $this->styles;
	}

	public function enqueueStyles() {
		wp_enqueue_style(
			SKAUTFONT_NAME . '_fonts',
			$this->frontendDirUrl . 'css/fonts.css',
			[],
			SKAUTFONT_VERSION,
			'all'
		);

		wp_add_inline_style( SKAUTFONT_NAME . '_fonts', $this->getCss() );
	}

}
