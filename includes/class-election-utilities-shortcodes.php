<?php

/**
 * Public Notices shortcode class
 */
class Election_Utilities_Shortcodes
{


    /**
     * Public_Notices_Shortcodes constructor.
     */
    public function __construct()
    {
        
    }
    
    
    public function register() {
        add_shortcode('election_overview', array( $this, 'election_overview'));
    }


    public function election_overview( $atts, $content  ) {

        /** @var  $id */
        /** @var  $template */

        $atts_actual = shortcode_atts(
            array(
                'id'          => '',
                'template'    => ''
            ),
            $atts );


        extract( $atts_actual );

        // $output = file_get_contents(  plugin_dir_path( dirname( __FILE__ ) ) . 'partials/election_overview.html' );

	    $output = '<div ng-app="electionUtilitiesApp" ng-init="electionID=' . $id . '">
    					<div ng-view></div>
					</div>';
        return $output ;

    }


    public function get_shortcodes() {
        return array('election_overview');
    }



	public function has_shortcodes() {

		global $post;

		if ( $post == null ) return;

		$post_type = get_post_type();

		$has_shortcodes = false;
		$shortcodes = new Election_Utilities_Shortcodes();

		if ( $post_type == "post"|| $post_type == "page" ) {

			foreach( $shortcodes->get_shortcodes() as $shortcode ) {
				if ( has_shortcode( $post->post_content, $shortcode ) ) {
					$has_shortcodes = true;
					break;
				}
			}
		}
		return $has_shortcodes;
	}




}