<?php
/**
 * Class: Election Utitliies Page Manager
 *
 * Creates pages required for displaying various views of the ballot contest and
 * candidate responses.
 *
 * In this class also resides for query_vars and rewrite rules which route acesss
 * to election information pages.
 */

class Election_Utilities_Page_Manager {

	/**
	 *
	 * Called from plugins_loaded hook. Registered in Hospitality class. Creates the page
	 * used to display room lists.
	 */
	public function create_contest_page() {

		if ( current_user_can( 'manage_options' ) && current_user_can( 'edit_posts' ) ) {
			// See if pages exists. If not, create it.
			if ( get_page_by_path( ELECTION_UTILITIES_CONTEST_PAGE_SLUG ) == null ) {
				global $user_ID;

				$page = array();

				$page['post_type']    = 'page';
				$page['post_content'] = '';
				$page['post_parent']  = 0;
				$page['post_author']  = $user_ID;
				$page['post_status']  = 'publish';
				$page['post_title']   = ELECTION_UTILITIES_CONTEST_PAGE_TITLE;
				// $page = apply_filters('yourplugin_add_new_page', $page, 'teams');
				$pageid = wp_insert_post( $page );
				if ( $pageid == 0 ) { /* Add Page Failed */
					error_log('Ballot Contest pageid is 0 in file ' . __FILE__ . ', line ' . __LINE__ . '.');

				}
			}
		}
	}
	/**
	 *
	 * Called from plugins_loaded hook. Registered in Hospitality class. Creates the page
	 * used to display room detail.
	 */
	public function create_compare_page() {

		if ( current_user_can( 'manage_options' ) && current_user_can( 'edit_posts' ) ) {
			// See if pages exists. If not, create it.
			if ( get_page_by_path( ELECTION_UTILITIES_COMPARE_PAGE_SLUG ) == null ) {
				global $user_ID;

				$page = array();

				$page['post_type']    = 'page';
				$page['post_content'] = '';
				$page['post_parent']  = 0;
				$page['post_author']  = $user_ID;
				$page['post_status']  = 'publish';
				$page['post_title']   = ELECTION_UTILITIES_COMPARE_PAGE_TITLE;
				$pageid = wp_insert_post( $page );
				if ( $pageid == 0 ) { /* Add Page Failed */
					error_log('Compare pageid is 0 in file ' . __FILE__ . ', line ' . __LINE__ . '.' );
				}
			}
		}
	}

	
	/**
	 *
	 * Registered as a 'the_content' filter. Returns the room listing shortcode output
	 * if the page is the rooms listing page.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function display_contest_page( $content ) {

		global $post;


		if ( $post->post_name == ELECTION_UTILITIES_CONTEST_PAGE_SLUG ) {

			$contest_post_id = $this->get_post_id_var();

			if ( $contest_post_id ) {
				return do_shortcode( '[ballot_contest id="' . $contest_post_id . '" ]' );
			}
		}

		return $content;

	}


	public function display_compare_page( $content ) {

		global $post;

		if ( $post->post_name == ELECTION_UTILITIES_COMPARE_PAGE_SLUG ) {
			$contest_post_id = $this->get_post_id_var();

			if ( $contest_post_id ) {
				return do_shortcode( '[compare_options id="' . $contest_post_id . '" ]' );
			}
		}

		return $content;

	}




	/**
	 * Registered as add_query_vars callback in the Hospitality class. It enables passing
	 * a rooms post ID as a query parameter.
	 *
	 * @param $the_vars
	 *
	 * @return array
	 */
	public function add_query_vars( $the_vars ) {
		$the_vars[] = ELECTION_UTILITIES_CONTEST_ID_VAR ;
		return $the_vars;
	}

	/**
	 * Registered as add_rewrite_rules callback in the Hospitality class. It adds
	 * a rewrite rule that maps rooms-listing/room-slug to a parameterized URL.
	 *
	 * @param $the_vars
	 *
	 * @return array
	 */

	public function add_rewrite_rules () {

		// get page ID for contest
		$contest_page_id = 0;
		$wp_post = get_page_by_path( ELECTION_UTILITIES_CONTEST_PAGE_SLUG,  OBJECT, 'page' ) ;
		if ( $wp_post != null ) {
			$contest_page_id = $wp_post->ID ;
		}
		else {
			error_log('wp_post is null, file '. __FILE__ . ', line ' .  __LINE__ );
		}

		add_rewrite_rule('^' . ELECTION_UTILITIES_CONTEST_PAGE_SLUG . '/([^/]*)/?',  'index.php?page_id=' . $contest_page_id . '&' . ELECTION_UTILITIES_CONTEST_PAGE_SLUG . '=$matches[1]','top');

	}

	/**
	 * Adds rewrite tag to support rule added in add_rewrite_rules function.
	 */
	public function add_rewrite_tags() {
		add_rewrite_tag('%' . ELECTION_UTILITIES_CONTEST_PAGE_SLUG . '%', '/([^/]*)/?');
	}

	private function get_post_id_var () {

		global $wp_query ;

		if(isset($wp_query->query_vars[ ELECTION_UTILITIES_CONTEST_ID_VAR ])) {
			$contest_post_id = urldecode( $wp_query->query_vars[ ELECTION_UTILITIES_CONTEST_ID_VAR ] );
		}
		else if(isset($wp_query->query_vars[ ELECTION_UTILITIES_CONTEST_SLUG_VAR ])) {
			$contest_post_slug = urldecode( $wp_query->query_vars[ ELECTION_UTILITIES_CONTEST_SLUG_VAR ] );
			$wp_post = get_page_by_path( $contest_post_slug,  OBJECT, 'page' ) ;

			if ( $wp_post != null ) {
				$contest_post_id = $wp_post->ID ;
			}
		}

		if ( isset( $contest_post_id )) {
			return $contest_post_id ;
		}

		return FALSE;
	}


}