<?php

if ( ! class_exists( 'Yoast_Api_Googleanalytics' ) ) {

	class Yoast_Api_Googleanalytics {

		public $options;

		/**
		 * This class will be loaded when someone calls the API library with the Google analytics module
		 */
		public function __construct() {

		}

		/**
		 * Autoload the Oauth classes
		 */
		private function load_api_oauth_files() {
			$oauth_files = array(
				'yoast_api_googleanalytics_reporting'                  => 'class-googleanalytics-reporting',
			);

			foreach ( $oauth_files as $key => $name ) {
				if ( file_exists( dirname( __FILE__ ) . '/' . $name . '.php' ) ) {
					require_once( dirname( __FILE__ ) . '/' . $name . '.php' );
				}
			}
		}

		/**
		 * Doing request to Google Analytics
		 *
		 * This method will do a request to google and get the response code and body from content
		 *
		 * @param string $target_url
		 * @param string $scope
		 * @param string $access_token
		 * @param string secret
		 *
		 * @return array|null
		 */
		public function do_request( $target_url, $scope, $access_token, $secret ) {
			$gdata     = $this->get_gdata( $scope, $access_token, $secret );
			$response  = $gdata->get( $target_url );
			$http_code = wp_remote_retrieve_response_code( $response );
			$response  = wp_remote_retrieve_body( $response );

			if ( $http_code == 200 ) {
				return array(
					'response' => array( 'code' => $http_code ),
					'body'     => $response,
				);
			}
		}

		/**
		 * Getting WP_GData object
		 *
		 * If not available include class file and create an instance of WP_GDAta
		 *
		 * @param string $scope
		 * @param null   $token
		 * @param null   $secret
		 *
		 * @return WP_GData
		 */
		protected function get_gdata( $scope, $token = null, $secret = null ) {
			$args = array(
				'scope'              => $scope,
				'xoauth_displayname' => 'Google Analytics by Yoast',
			);

			$gdata = new WP_GData( $args, $token, $secret );

			return $gdata;
		}

	}

}