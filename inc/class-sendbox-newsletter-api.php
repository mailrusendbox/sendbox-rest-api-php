<?php

/**
 * Extend library class.
 *
 * Class Sendbox_Newsletter_API
 */
class Sendbox_Newsletter_API extends SendboxApi {

	/**
	 * @var string|int Id default address book for subscribe.
	 */
	public $default_book;


	/**
	 * Get client option. Api ready for using.
	 *
	 * Sendbox_Newsletter_API constructor.
	 */
	public function __construct() {

		$user_id = $this->get_option( 'client_id' );
		$secret  = $this->get_option( 'client_secret' );

		parent::__construct( $user_id, $secret, new FileStorage() );

		$this->default_book = $this->get_option( 'default_book' );

	}

	/**
	 * Get plugin API Settings option.
	 *
	 * @param $name string Option name.
	 *
	 * @return string Option value
	 */
	public function get_option( $name ) {
		return Sendbox_Newsletter_Settings::get_option( $name, 'sp_api_setting' );
	}

}