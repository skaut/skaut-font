<?php

declare( strict_types=1 );

namespace Skautfont;

final class Admin {

	private $frontend;

	public function __construct( Frontend $frontend ) {
		$this->frontend = $frontend;
		$this->initHooks();
	}

	private function initHooks() {
		add_filter( 'plugin_action_links_' . SKAUTFONT_PLUGIN_BASENAME, [
			$this,
			'addSettingsLinkToPluginsTable'
		] );

		add_action( 'admin_menu', [ $this, 'setupSettingPage' ], 5 );
		add_action( 'admin_init', [ $this, 'setupSettingFields' ] );
		add_action( 'activated_plugin', [ Admin::class, 'redirectToPluginSettingPageAfterActivation' ] );
	}

	public static function redirectToPluginSettingPageAfterActivation( string $pluginName ) {
		if ( $pluginName === SKAUTFONT_NAME . '/' . SKAUTFONT_NAME . '.php' ) {
			wp_safe_redirect( admin_url( 'themes.php?page=' . SKAUTFONT_NAME ), 302 );
			exit;
		}
	}

	public function addSettingsLinkToPluginsTable( array $links = [] ): array {
		$mylinks = [
			'<a href="' . admin_url( 'themes.php?page=' . SKAUTFONT_NAME ) . '">' . __( 'Settings' ) . '</a>',
		];

		return array_merge( $links, $mylinks );
	}

	public function setupSettingPage() {
		add_submenu_page(
			'themes.php',
			__( 'Scout fonts', 'skaut-font' ),
			__( 'Scout fonts', 'skaut-font' ),
			Helpers::getSkautfontManagerCapability(),
			SKAUTFONT_NAME,
			[ $this, 'printSettingPage' ]
		);
	}

	public function printSettingPage() {
		if ( ! current_user_can( Helpers::getSkautfontManagerCapability() ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		settings_errors();
		?>
		<div class="wrap">
			<h1><?php _e( 'Nastavení skautských fontů', 'skaut-font' ); ?></h1>
			<form method="POST" action="<?php echo admin_url( 'options.php' ); ?>">
				<?php settings_fields( SKAUTFONT_NAME );
				do_settings_sections( SKAUTFONT_NAME );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function setupSettingFields() {
		add_settings_section(
			SKAUTFONT_NAME,
			'',
			function () {
				echo '';
			},
			SKAUTFONT_NAME
		);

		add_settings_field(
			SKAUTFONT_NAME . '_styles_body',
			$this->frontend->getStyles()['body'],
			[ $this, 'styleBody' ],
			SKAUTFONT_NAME,
			SKAUTFONT_NAME
		);

		add_settings_field(
			SKAUTFONT_NAME . '_style_titles',
			$this->frontend->getStyles()['titles'],
			[ $this, 'styleTitles' ],
			SKAUTFONT_NAME,
			SKAUTFONT_NAME
		);

		add_settings_field(
			SKAUTFONT_NAME . '_style_site-desc',
			$this->frontend->getStyles()['site-desc'],
			[ $this, 'styleSiteDesc' ],
			SKAUTFONT_NAME,
			SKAUTFONT_NAME
		);

		register_setting( SKAUTFONT_NAME, SKAUTFONT_NAME . '_style_body', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field'
		] );

		register_setting( SKAUTFONT_NAME, SKAUTFONT_NAME . '_style_titles', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field'
		] );

		register_setting( SKAUTFONT_NAME, SKAUTFONT_NAME . '_style_site-desc', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field'
		] );
	}

	public function styleBody() {
		$bodyStyle = get_option( SKAUTFONT_NAME . '_style_body' );
		?>
		<div>
			<label>
				<input type="radio" name="<?php echo SKAUTFONT_NAME . '_style_body'; ?>"
				       value="themix"<?php checked( 'themix' === $bodyStyle ); ?> />
				<span><?php echo $this->frontend->getFonts()['themix']; ?></span>
			</label>
		</div>
		<div>
			<label>
				<input type="radio" name="<?php echo SKAUTFONT_NAME . '_style_body'; ?>"
				       value="skautbold"<?php checked( 'skautbold' === $bodyStyle ); ?> />
				<span><?php echo $this->frontend->getFonts()['skautbold']; ?></span>
			</label>
		</div>
		<div>
			<label>
				<input type="radio" name="<?php echo SKAUTFONT_NAME . '_style_body'; ?>"
				       value="default"<?php checked( 'default' === $bodyStyle ); ?> />
				<span><?php _e( 'Neměnit', 'skaut-font' ); ?></span>
			</label>
		</div>
		<?php
	}

	public function styleTitles() {
		$bodyStyle = get_option( SKAUTFONT_NAME . '_style_titles' );
		?>
		<div>
			<label>
				<input type="radio" name="<?php echo SKAUTFONT_NAME . '_style_titles'; ?>"
				       value="themix"<?php checked( 'themix' === $bodyStyle ); ?> />
				<span><?php echo $this->frontend->getFonts()['themix']; ?></span>
			</label>
		</div>
		<div>
			<label>
				<input type="radio" name="<?php echo SKAUTFONT_NAME . '_style_titles'; ?>"
				       value="skautbold"<?php checked( 'skautbold' === $bodyStyle ); ?> />
				<span><?php echo $this->frontend->getFonts()['skautbold']; ?></span>
			</label>
		</div>
		<div>
			<label>
				<input type="radio" name="<?php echo SKAUTFONT_NAME . '_style_titles'; ?>"
				       value="default"<?php checked( 'default' === $bodyStyle ); ?> />
				<span><?php _e( 'Neměnit', 'skaut-font' ); ?></span>
			</label>
		</div>
		<?php
	}

	public function styleSiteDesc() {
		$bodyStyle = get_option( SKAUTFONT_NAME . '_style_site-desc' );
		?>
		<div>
			<label>
				<input type="radio" name="<?php echo SKAUTFONT_NAME . '_style_site-desc'; ?>"
				       value="themix"<?php checked( 'themix' === $bodyStyle ); ?> />
				<span><?php echo $this->frontend->getFonts()['themix']; ?></span>
			</label>
		</div>
		<div>
			<label>
				<input type="radio" name="<?php echo SKAUTFONT_NAME . '_style_site-desc'; ?>"
				       value="skautbold"<?php checked( 'skautbold' === $bodyStyle ); ?> />
				<span><?php echo $this->frontend->getFonts()['skautbold']; ?></span>
			</label>
		</div>
		<div>
			<label>
				<input type="radio" name="<?php echo SKAUTFONT_NAME . '_style_site-desc'; ?>"
				       value="default"<?php checked( 'default' === $bodyStyle ); ?> />
				<span><?php _e( 'Neměnit', 'skaut-font' ); ?></span>
			</label>
		</div>
		<?php
	}

}
