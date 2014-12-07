<?php

if ( ! class_exists( 'Yoast_OAuthConsumer' ) ) {

	class Yoast_OAuthConsumer {

		public $key;
		public $secret;

		function __construct( $key, $secret, $callback_url = null ) {
			$this->key          = $key;
			$this->secret       = $secret;
			$this->callback_url = $callback_url;
		}

		function __toString() {
			return "Yoast_OAuthConsumer[key=$this->key,secret=$this->secret]";
		}

	}

}