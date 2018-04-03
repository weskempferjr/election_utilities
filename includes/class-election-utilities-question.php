<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 3/29/18
 * Time: 9:02 PM
 */

class Election_Utilities_Question {



	public function __construct() {

	}

	public function fetch_by_ID ( $id ) {
		$view_parameters = array();
		$view_parameters['id'] = $id ;
		return $this->fetch( $view_parameters) ;
	}

	public function fetch( $view_parameters ) {

		$question_cat_id = $view_parameters['id'] ;

		$question = array();

		$question_cat = get_category( $question_cat_id);

		if ( $question_cat == null ) {
			throw new Exception(__('Could not retrieve survey question category', ELECTION_UTILITIES_TEXTDOMAIN));
		}

		$question['title'] = $question_cat->name ;
		$question['description'] = $question_cat->category_description ;


		// Responses

		$args = array(
			'posts_per_page'   => 20,
			'offset'           => 0,
			'category'         => $question_cat_id,
			'orderby'          => 'date',
		);
		$response_posts = get_posts( $args );
		$responses = array();

		foreach ( $response_posts as $response_post ) {

			$response = new Election_Utilities_Response();
			$response->setArray( $response_post);
			$responses[] = $response->getArray();
 		}

 		$question['responses'] = $responses;
		return $question;

	}


}