<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 3/29/18
 * Time: 9:02 PM
 */

class Ballot_Contest {


	/**
	 * Election_Overview constructor.
	 */
	public function __construct() {

	}

	public function fetch( $view_parameters ) {

		$contest_cat_id = $view_parameters['id'] ;

		$ballot_contest = array();

		$contests_cat = get_category( $contest_cat_id);

		if ( $contests_cat == null ) {
			throw new Exception(__('Could not retrieve ballot contest category', ELECTION_UTILITIES_TEXTDOMAIN));
		}

		$ballot_contest['title'] = $contests_cat->name ;
		$ballot_contest['description'] = $contests_cat->category_description ;

		$questions = array();

		// Question categories
		$child_cats = get_term_children( $contest_cat_id, 'category');

		$order = 1;
		foreach ( $child_cats as $child_cat ) {
			$term = get_term_by( 'id', $child_cat,'category' );

			if ( is_wp_error( $term )) {
				throw new Exception(__('Could not get child category (question catetory) of ballot contest catetory', ELECTION_UTILITIES_TEXTDOMAIN));
			}

			// Ignore indirect descendents
			if ( $term->parent != $contest_cat_id ) {
				continue;
			}



			$question_obj = new Election_Utilities_Question();
			$question = $question_obj->fetch_by_ID( $term->term_id );

			$questions[] = $question;
 		}

 		$ballot_contest['questions'] = $questions;

		$pi_manager = new Personal_Info_Manager();
		$ballot_contest['contestants'] = $pi_manager->get_ballot_contestants( $contest_cat_id );

		return $ballot_contest;

	}

	private function get_contest_url ($id ) {
		$contest_url = get_site_url() . '/' . ELECTION_UTILITIES_CONTEST_PAGE_SLUG . '/?' . ELECTION_UTILITIES_CONTEST_ID_VAR . '=' . $id ;
		return $contest_url;
	}
}