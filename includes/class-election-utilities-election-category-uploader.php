<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 3/24/18
 * Time: 12:17 PM
 */

class Election_Category_Uploader extends File_Uploader  {


	public function handle_upload() {
		$response = array();

		$fp = fopen( $this->__data['category_file_upload']['tmp_name'], 'r');

		$category_data = array();

		if ( $fp !== FALSE ) {
			while(! feof($fp )){
				$csv_row = fgetcsv($fp);
				array_push( $category_data, $csv_row );

			}
		}


		/*
		 * Create voter guide root category
		 */

		$cat_array = array(
			'cat_name' => 'Voter Guide Root',
			'cat_description' => 'This is the root category for voter guide categories'
		);

		$root_cat = FALSE;
		$root_term = term_exists( $cat_array['cat_name'], 'category', 0);
		if ( ! $root_term ) {
			$root_cat = wp_insert_category( $cat_array );
		}
		else {
			$root_cat = $root_term['term_id'];
		}

		if ( !$root_cat ) {
			$response['response'] = "FAILURE";
			return $response;
		}



		/*
		 * Assumptions:
		 *  - The first record is a header.
		 *  - The second record is the election category.
		 *  - All other records represent child categories (offices) of the election category.
		 *  - The first column is the category name
		 * -  The second column is the category description
		 * -  The third colunmn is the category type (election, or office)
		 */



		$cat_array = array(
			'cat_name' => $category_data[1][0],
			'cat_description' => $category_data[1][1],
			'category_parent' => $root_cat
		);

		/*
		 * See if category exists. If not, create it.
		 */
		$parent_cat = FALSE;
		$parent_term = term_exists( $cat_array['cat_name'], 'category', $root_cat);
		if ( ! $parent_term ) {
			$parent_cat = wp_insert_category( $cat_array );
		}
		else {
			$parent_cat = $parent_term['term_id'];
		}

		if ( $parent_cat ) {

			for ( $i = 2; $i < count( $category_data) - 2 ; $i++ ) {
				$cat_array = array(
					'cat_name' => $category_data[$i][0],
					'category_description' => $category_data[$i][1],
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
					error_log(__FILE__ . ':' . __LINE__ . ', Create or find of child category failed' );
				}

			}

			$response['response'] = "SUCCESS";
		}
		else {
			error_log(__FILE__ . ':' . __LINE__ . ', Top level category could not be found or created.' );
			throw new Exception(__('Could not create or update election categories', ELECTION_UTILITIES_TEXTDOMAIN));
		}


		return $response;
	}

}