<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 3/29/18
 * Time: 9:02 PM
 */

class Election_Utilities_Response {


	private $__response = array();

	public function __construct() {

	}

	public function setArray( $response_post ) {
		$this->__response = $this->post_to_array( $response_post );
	}

	public function getArray() {
		return $this->__response ;
	}

	public function fetch_by_ID ( $id ) {
		$view_parameters = array();
		$view_parameters['id'] = $id ;
		$this->fetch( $view_parameters) ;
	}

	public function fetch( $view_parameters ) {

		$response_post_id = $view_parameters['id'] ;

		$response_post = get_post( $response_post_id );

		if ( $response_post == null ) {
			throw new Exception(__('Could not retrieve survey response category', ELECTION_UTILITIES_TEXTDOMAIN));
		}

		$this->__response  =  $this->post_to_array( $response_post) ;
		return $this->__response ;

	}

	private function post_to_array ( $response_post ) {

		$pim = new Personal_Info_Manager();
		$author_info = $pim->get_info( $response_post->post_author);

		$response = array(
			'id' => $response_post->ID,
			'title' => $response_post->post_title,
			'name' => $response_post->post_name,
			'author' => $response_post->post_author,
			'content' => $response_post->post_content,
			'authorInfo' => $author_info
		);


		return $response;
	}

}