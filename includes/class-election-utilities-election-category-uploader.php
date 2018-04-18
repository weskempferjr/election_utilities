<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 3/24/18
 * Time: 12:17 PM
 */

class Election_Category_Uploader extends File_Uploader  {

	const ELECTION_TYPE = 'election';
	const JURISDICTION_TYPE = 'jurisdiction';
	const JURISDICTION_BODY_TYPE ='jurisdiction_body';
	const OFFICE_TYPE = 'office';
	const BALLOT_ISSUE_TYPE = 'ballot_issue';

	private static $election_type_id = self::ELECTION_TYPE;
	private static $jurisdiction_type_id = self::JURISDICTION_TYPE;
	private static $jurisdiction_body_type_id = self::JURISDICTION_BODY_TYPE;
	private static $office_type_id = self::OFFICE_TYPE;
	private static $ballot_issue_type_id = self::BALLOT_ISSUE_TYPE;

	const INITIAL_STATE = 'initial';
	const JURISDICTION_STATE = 'jurisdiction';
	const JURISDICTION_BODY_STATE ='jurisdiction_body';
	const CONTEST_STATE = 'contest';

	const ELECTION_UTILITIES_TYPE_KEY = 'election_utilities_type';

	private $_load_state = self::INITIAL_STATE;



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
			throw new Exception(__('Could not load or root category', ELECTION_UTILITIES_TEXTDOMAIN));
		}



		/*
		 * Assumptions:
		 *  - The first record is a header.
		 *  - The second record is the election category.
		 *  - All other records represent child categories (offices) of the election category.
		 *  - The first column is the category name
		 * -  The second column is the category description
		 * -  The third colunmn is the category type (election, jurisdiction, jurisdiction_body, or office)
		 * -  The category type determines level in the category hiearchy.
		 *     election is a child of voter guide root
		 *     jurisdiction is a child of election
		 *     jurisdiction body is a child of jurisdiction
		 *     office is a child of jurisdiction_body
		 */



		/*
		 * This is the root of the election category. If it is not of type election, bail.
		 */

		if ( $category_data[1][2] == self::$election_type_id ) {
			$cat_array = array(
				'cat_name' => $category_data[1][0],
				'cat_description' => $category_data[1][1],
				'category_parent' => $root_cat,
				'category_type' => self::$election_type_id
			);
		}
		else {
			throw new Exception(__('Election category not found in file where it was expected', ELECTION_UTILITIES_TEXTDOMAIN));
		}



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

			$this->set_type_id( $parent_cat, self::$election_type_id );

			$this->_load_state = self::JURISDICTION_STATE ;

			$current_parent_cat = $parent_cat;

			for ( $i = 2; $i < count( $category_data ) ; $i++ ) {

				switch ( $this->_load_state) {

					case self::JURISDICTION_STATE:

						if ( $category_data[$i][2] != self::$jurisdiction_type_id ) {
							throw new Exception(__('Expected jurisdiction record.', ELECTION_UTILITIES_TEXTDOMAIN));
						}

						$cat_array = array(
							'cat_name' => $category_data[$i][0],
							'category_description' => $category_data[$i][1],
							'category_parent' => $current_parent_cat
						);

						$child_term = term_exists( $cat_array['cat_name'], 'category', $current_parent_cat );
						if ( ! $child_term ) {
							$child_cat = wp_insert_category( $cat_array );
						}
						else {
							$child_cat = $child_term['term_id'];
						}

						if ( ! $child_cat ) {
							throw new Exception( sprintf(__('Could not create or find category for type in state = %s', ELECTION_UTILITIES_TEXTDOMAIN)), $this->_load_state );
						}

						$this->set_type_id( $child_cat, self::$jurisdiction_type_id );
						$this->_load_state = self::JURISDICTION_BODY_STATE;
						$current_parent_cat = $child_cat;

						break;


					case self::JURISDICTION_BODY_STATE:

						if ( $category_data[$i][2] != self::$jurisdiction_body_type_id ) {
							$this->_load_state = self::JURISDICTION_STATE;
							$i--;
							$current_parent_cat = $this->get_parent_category_id( $current_parent_cat);
							continue;
						}

						$cat_array = array(
							'cat_name' => $category_data[$i][0],
							'category_description' => $category_data[$i][1],
							'category_parent' => $current_parent_cat
						);

						$child_term = term_exists( $cat_array['cat_name'], 'category', $current_parent_cat );
						if ( ! $child_term ) {
							$child_cat = wp_insert_category( $cat_array );
						}
						else {
							$child_cat = $child_term['term_id'];
						}

						if ( ! $child_cat ) {
							throw new Exception( sprintf(__('Could not create or find category for type = %s', ELECTION_UTILITIES_TEXTDOMAIN)), $this->_load_state );
						}

						$this->set_type_id( $child_cat, self::$jurisdiction_body_type_id );
						$this->_load_state = self::CONTEST_STATE;
						$current_parent_cat = $child_cat;

						break;

					case self::CONTEST_STATE:

						if ( $category_data[$i][2] != self::$office_type_id ) {
							$this->_load_state = self::JURISDICTION_BODY_STATE;
							$i--;
							$current_parent_cat = $this->get_parent_category_id( $current_parent_cat);
							continue;
						}

						$cat_array = array(
							'cat_name' => $category_data[$i][0],
							'category_description' => $category_data[$i][1],
							'category_parent' => $current_parent_cat
						);

						$child_term = term_exists( $cat_array['cat_name'], 'category', $current_parent_cat );
						if ( ! $child_term ) {
							$child_cat = wp_insert_category( $cat_array );
						}
						else {
							$child_cat = $child_term['term_id'];
						}

						if ( ! $child_cat ) {
							throw new Exception( sprintf(__('Could not create or find category for type = %s', ELECTION_UTILITIES_TEXTDOMAIN)), $this->_load_state );
						}
						$this->set_type_id( $child_cat, self::$office_type_id );

						break;


					default:
						throw new Exception(__('Category loader in unknown state.', ELECTION_UTILITIES_TEXTDOMAIN));
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

	private function get_parent_category_id( $cat_id ) {
		$cat = get_category(( $cat_id ));
		return $cat->parent ;
	}

	private function set_type_id ( $cat, $type_id ) {
		$meta_id = update_term_meta( $cat, self::ELECTION_UTILITIES_TYPE_KEY , $type_id );
		return $meta_id;
	}

}