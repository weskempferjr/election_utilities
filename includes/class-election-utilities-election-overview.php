<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 3/29/18
 * Time: 9:02 PM
 */

class Election_Overview {


	/**
	 * Election_Overview constructor.
	 */
	public function __construct() {

	}

	public function fetch( $view_parameters ) {

		$election_cat_id = $view_parameters['id'] ;

		$election_overview = array();

		$election_cat = get_category( $election_cat_id);

		if ( $election_cat == null ) {
			throw new Exception(__('Could not retrieve electiion top level category', ELECTION_UTILITIES_TEXTDOMAIN));
		}

		$election_overview['title'] = $election_cat->name ;
		$election_overview['description'] = $election_cat->category_description ;

		$ballot_contests = array();

		$child_cats = get_term_children( $election_cat_id, 'category');

		$order = 1;
		foreach ( $child_cats as $child_cat ) {
			$term = get_term_by( 'id', $child_cat,'category' );

			if ( is_wp_error( $term )) {
				throw new Exception(__('Could not get child category of top level election catetory', ELECTION_UTILITIES_TEXTDOMAIN));
			}

			if ( $term->parent != $election_cat_id ) {
				continue;
			}

			$term_id = intval( $term->term_id );
			$contest_url = $this->get_contest_url( $term_id );

			$ballot_contest = array(
				'id' => intval( $term->term_id),
				'title' => $term->name,
				'description' => $term->description,
				'contestURL'=> $contest_url,
				'order' => $order++
			);

			$ballot_contests[] = $ballot_contest;
 		}

 		$election_overview['ballot_contests'] = $ballot_contests;
		return $election_overview;

	}

	private function get_contest_url ($id ) {
		$contest_url = get_site_url() . '/' . ELECTION_UTILITIES_CONTEST_PAGE_SLUG . '/?' . ELECTION_UTILITIES_CONTEST_ID_VAR . '=' . $id ;
		return $contest_url;
	}
}