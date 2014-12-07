<?php

if ( ! class_exists( 'Yoast_Api_Oauth' ) ) {

	class Yoast_Api_Oauth {

		/**
		 * This class will be loaded when someone calls the API library with the Oauth module
		 */
		public function __construct() {
			$this->load_api_oauth_files();
		}

		/**
		 * Autoload the Oauth classes
		 */
		private function load_api_oauth_files() {
			$oauth_files = array(
				'yoast_oauthconsumer'                  => 'class-oauth-consumer',
				'yoast_oauthdatastore'                 => 'class-oauth-datestore',
				'yoast_oauthexception'                 => 'class-oauth-exception',
				'yoast_oauthrequest'                   => 'class-oauth-request',
				'yoast_oauthserver'                    => 'class-oauth-server',
				'yoast_oauthsignaturemethod'           => 'class-oauth-signature-method',
				'yoast_oathsignaturemethod_hmac_sha1'  => 'class-oauth-signature-method-hmac-sha1',
				'yoast_oauthsignaturemethod_plaintext' => 'class-oauth-signature-method-plaintext',
				'yoast_oauthsignaturemethod_rsa_sha1'  => 'class-oauth-signature-method-rsa-sha1',
				'yoast_oauthtoken'                     => 'class-oauth-token',
				'yoast_oauthutil'                      => 'class-oauth-util',
			);

			foreach ( $oauth_files as $key => $name ) {
				if ( file_exists( dirname( __FILE__ ) . '/' . $name . '.php' ) ) {
					require_once( dirname( __FILE__ ) . '/' . $name . '.php' );
				}
			}
		}

	}

}