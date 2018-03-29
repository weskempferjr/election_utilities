<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 3/26/18
 * Time: 4:43 PM
 */




class Personal_Info_Manager {

	public static $info_fields = array(
		'Timestamp',
		'Name',
		'Email',
		'Address',
		'Phone number',
		'Comments'
	);

	private $__personal_info = array();

	/**
	 * Personal_Info_Manager constructor.
	 */
	public function __construct( $personal_info ) {
		$this->__personal_info = $personal_info;
	}

	public function update() {

		$user_login =  $user_email = $this->__personal_info['Email'];
		$user_id = username_exists( $user_login );

		$user_info = array(
			'user_login'  => $user_login,
			'user_email' => $user_email,
			'user_pass'   =>  $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false ),
			'display_name' => $this->__personal_info['Name'],

		);

		// If user exists, add user ID to array. Operation will be an update rather than a create.
		if ( $user_id ) {
			$user_info['ID'] = $user_id ;
		}

		$user_id = wp_insert_user( $user_info ) ;

		//On failure throw exception
		if ( is_wp_error( $user_id ) ) {
			throw new Exception(__('Error attempting to add or update personal info found in questionnair data.', ELECTION_UTILITIES_TEXTDOMAIN));
		}

		return $user_id;


	}
}