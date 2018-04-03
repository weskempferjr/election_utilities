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
	public function __construct() {
	}

	public function set_personal_info( $personal_info ) {
		$this->__personal_info = $personal_info;
	}

	public function update() {

		if ( $this->__personal_info == null ) {
			throw new Exception(__('Cannot update personal info before running set_personal_info', ELECTION_UTILITIES_TEXTDOMAIN));
		}

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

		if ( ! update_user_meta( $user_id, 'ballot_contest_ID', $this->__personal_info['ballot_contest_ID']) ) {
			throw new Exception(__('Could not update user meta: ballot_contest_ID', ELECTION_UTILITIES_TEXTDOMAIN));
		}

		return $user_id;


	}

	function get_info( $user_id ) {

		$wp_user = get_userdata( $user_id );

		if ( ! $wp_user ) {
			throw new Exception(__('Could not get user data.', ELECTION_UTILITIES_TEXTDOMAIN ));
		}

		// get meta fields of interest
		return $this->user_as_array( $wp_user ) ;

	}

	public function get_ballot_contestants( $ballot_contest_id ) {

		$args = array(
			'meta_key'     => 'ballot_contest_ID',
			'meta_value' => $ballot_contest_id,
			'meta_compare' => '=',
			'fields'       => 'all_with_meta'
		);

		$wp_users = get_users( $args );

		$contestants = array();

		foreach ( $wp_users as $wp_user ) {
			$contestants[] = $this->user_as_array( $wp_user );
		}

		return $contestants;

	}

	private function user_as_array ( $wp_user ) {

		$user_info = array(
			'ID' => $wp_user->ID,
			'firstName' => $wp_user->first_name,
			'lastName' => $wp_user->last_name,
			'displayName' => $wp_user->display_name,
			'avatar' => get_avatar( $wp_user->ID, 150 )
		);

		return $user_info ;

	}

}