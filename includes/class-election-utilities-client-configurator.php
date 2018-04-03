<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 9/15/17
 * Time: 4:13 PM
 */

class Election_Utilities_Client_Configurator {

	public function configure_js_globals( $handle = 'election-utilities-js-globals') {

		$_theme = wp_get_theme();


		$config = array(
			'config' => array(
				'baseUrl'     => trailingslashit( get_home_url() ),
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'local'       => get_locale(),
				'themeName'   => $_theme->get('Name'),
				'themeVersion'=> $_theme->get('Version'),
				'wpVersion'   => get_bloginfo('version'),
				'enableDebug' => (WP_DEBUG !== false) ? true : false,
				'env'         => defined('WP_ENV') ? WP_ENV : 'production',
				'modules'     => array(
					'electionUtilities' => array(
						'l10n' =>  array(
							'placeHolder' => __( 'Placeholder', ELECTION_UTILITIES_TEXTDOMAIN),
						),
						'restNamespace' => 'v1/election-utilities-route',   // Rest Api route
						'partialUrl' => plugin_dir_url( dirname(__FILE__ ) ) . 'partials/', //Template html url
						'resourceRoot' => plugin_dir_url( dirname(__FILE__ )  ) . '/',
						'localizations' => $this->get_localizations(),
						'electionOverviewSlug' => 'election-overview',
						'ballotContestSlug' => 'ballot-contest'
					)
				)
			)
		);


		$script = sprintf( 'window.wpNg = %s', json_encode( $config ) );
		$ret_val = wp_add_inline_script( $handle, $script, 'before');
	}




	private function get_localizations() {

		$text_domain = ELECTION_UTILITIES_TEXTDOMAIN;

		$localizations = array(
			'uploadFileButtonLabel' => __('Upload file', $text_domain),
			'uploadFileInputLabel' => __('Choose a file to uplodad', $text_domain)
		);

		return $localizations;

	}

}