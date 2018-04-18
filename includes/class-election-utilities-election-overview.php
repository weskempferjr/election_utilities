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

		$election_overview['jurisdictions'] = $this->get_type_child_categories( $election_cat_id, Election_Category_Uploader::JURISDICTION_TYPE);

		for ( $i = 0 ; $i < count( $election_overview['jurisdictions']) ; $i++  ) {
			$election_overview['jurisdictions'][$i]['jurisdiction_bodies'] = $this->get_type_child_categories( $election_overview['jurisdictions'][$i]['id'], Election_Category_Uploader::JURISDICTION_BODY_TYPE);

			for ( $j = 0 ;  $j < count($election_overview['jurisdictions'][$i]['jurisdiction_bodies']) ; $j++ ) {
				$election_overview['jurisdictions'][$i]['jurisdiction_bodies'][$j]['contests'] = $this->get_type_child_categories(  $election_overview['jurisdictions'][$i]['jurisdiction_bodies'][$j]['id'], Election_Category_Uploader::OFFICE_TYPE);
			}

		}

		return $election_overview;

	}

	private function get_contest_url ($id ) {
		$contest_url = get_site_url() . '/' . ELECTION_UTILITIES_CONTEST_PAGE_SLUG . '/?' . ELECTION_UTILITIES_CONTEST_ID_VAR . '=' . $id ;
		return $contest_url;
	}

	private function get_type_child_categories( $cat_id, $type_id ) {

		$child_cats = get_term_children( $cat_id, 'category');
		$order = 1;

		$categories_array = array();

		foreach ( $child_cats as $child_cat ) {
			$term = get_term_by( 'id', $child_cat, 'category' );

			$term_id = intval( $term->term_id );

			// Get only direct children
			if ( $term->parent != $cat_id )
				continue;

			$eu_type = get_term_meta( $term_id, Election_Category_Uploader::ELECTION_UTILITIES_TYPE_KEY, true );

			if ( $eu_type != $type_id ) {
				error_log( sprintf(__('Found category in election hierarchy with not election type meta value. ID = %d. Expected %s, got %s', ELECTION_UTILITIES_TEXTDOMAIN ), $cat_id, $type_id, $eu_type));
				continue;
			}

			switch( $type_id ) {

				case Election_Category_Uploader::JURISDICTION_TYPE:
				case Election_Category_Uploader::JURISDICTION_BODY_TYPE;

					$categories_array[] = array(
						'id' => intval( $term->term_id),
						'title' => $term->name,
						'description' => $term->description,
						'order' => $order++
					);
					break;

				case Election_Category_Uploader::OFFICE_TYPE:

					$contest_url = $this->get_contest_url( $term_id );
					$categories_array[] = array(
						'id' => intval( $term->term_id),
						'title' => $term->name,
						'description' => $term->description,
						'contestURL'=> $contest_url,
						'order' => $order++
					);
					break;

				default:
					error_log( sprintf(__('Unknown election utility type: %s', ELECTION_UTILITIES_TEXTDOMAIN ), $type_id ));
					break;
			}



		}

		return $categories_array;

	}
}