<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 3/24/18
 * Time: 12:41 PM
 */

class Response_Uploader extends  File_Uploader {

	public function handle_upload() {
		// TODO: Implement handle_upload() method.

		$fp = fopen( $this->__data['response_file_upload']['tmp_name'], 'r');

		$response_data = array();

		if ( $fp !== FALSE ) {
			while(! feof($fp )){
				$csv_row = fgetcsv($fp);
				array_push( $response_data, $csv_row );

			}
		}

		/*
		 * Assumptions:
		 *   Keys (names) for data are in row 0
		 *   Values for data are in row 1
		 *   All other rows are ignored.
		 */

		$responses = array();

		$keys = $response_data[0];
		$values = $response_data[1];

		for ( $i = 0 ; $i < count($keys) ; $i++  ) {
			$responses[ $keys[ $i ] ] = $values[ $i ];
		}

		$info_columns = Personal_Info_Manager::$info_fields;


		/*
		 * Load or update personal info
		 */

		$personal_info = array();
		foreach ( array_keys( $responses  ) as $info_field ) {
			// Ignore info columns
			if ( in_array( $info_field, $info_columns)) {
				$personal_info[ $info_field ] = $responses [ $info_field] ;
			}
			else {
				continue;
			}
		}

		$pi_manager = new Personal_Info_Manager( $personal_info );
		$user_id = $pi_manager->update();


		// Get questionnaire responses
		// For each question (key), create a category if it does not exists.
		// For each question response (value), create a new post and assign it to the question category

		$parent_cat = $this->__data['parentID'];


		/*
		 * Load question categories and responses
		 */
		foreach ( array_keys( $responses  ) as $question ) {

			// Ignore info columns
			if ( in_array( $question, $info_columns)) {
				continue;
			}

			$cat_array = array(
				'cat_name' => $question,
				'cat_description' => $question,
				'category_parent' => $parent_cat
			);

			$child_cat = FALSE;
			$child_term = term_exists( $cat_array['cat_name'], 'category', $parent_cat );
			if ( ! $child_term ) {
				$child_cat = wp_insert_category( $cat_array );
			}
			else {
				$child_cat = $child_term['term_id'];
			}

			if ( ! $child_cat ) {

				// TODO: throw an exception
				error_log(__FILE__ . ':' . __LINE__ . ', Create or find of child category failed' );
			}
			/*
			 * Load question responses
	        */

			$post_arr = array(
				'post_author' => $user_id,
				'post_content' => $responses[ $question ],
				'post_title' => $question ,
				'post_status' => 'publish',
				'post_type' => 'post',

			);

			// If the page doesn't already exist, then create it
			if( ! $this->response_post_exists( $question, $user_id, $child_cat) ) {
				$post_id = wp_insert_post( $post_arr );
				if ( is_wp_error( $post_id ) ) {
					throw new Exception(__('Could not insert new post for questionnaire response'), ELECTION_UTILITIES_TEXTDOMAIN);
				}
				wp_set_post_categories( $post_id, $child_cat ) ;
			}

		}

		return '';

	}

	private function response_post_exists( $title, $author_id, $category_id ) {

		$args = array(
			'author' => $author_id,
			'cat' => $category_id,
			'title' => $title
		);

		$retval = FALSE;

		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			$retval = TRUE;
		}

		wp_reset_postdata();

		return $retval;

	}


}