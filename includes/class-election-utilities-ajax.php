<?php

/**
 *  Ajax controller for child theme.
 */
class Election_Utilities_Ajax_Controller
{


	/**
	 * Election_Utilities_Ajax_Controller constructor.
	 *
	 * @param $__fileErrors
	 */
	public function __construct() {

	}


	public function execute_request() {
        
        try {
            switch($_REQUEST['fn']){
                case 'upload_categories':
                    // If  not set, consider it an invalid request.
                    if ( !isset( $_FILES ) ) {
	                        throw new Exception(__('Invalid upload categories request.', ELECTION_UTILITIES_TEXTDOMAIN ) );
                    }

	                $posted_data =  isset( $_POST ) ? $_POST : array();
	                $file_data = isset( $_FILES ) ? $_FILES : array();

	                $raw_data = array_merge( $posted_data, $file_data );
	                $data = $this->sanitize_data( $raw_data );


                    $output = $this->upload_categories( $data );

                    if ( $output === false ) {
                        throw new Exception(__('Error uploading categories.', ELECTION_UTILITIES_TEXTDOMAIN ) );
                    }
                    break;

                case 'upload_questionnaire_responses';

	                if ( !isset( $_REQUEST['parentID'] ) || !isset( $_FILES ) ) {
		                throw new Exception(__('Invalid get category dropdown request', ELECTION_UTILITIES_TEXTDOMAIN ) );
	                }

	                $posted_data =  isset( $_POST ) ? $_POST : array();
	                $file_data = isset( $_FILES ) ? $_FILES : array();

	                $raw_data = array_merge( $posted_data, $file_data );
	                $data = $this->sanitize_data( $raw_data );


	                $output = $this->upload_questionnaire_responses( $data );

	                if ( $output === false ) {
		                throw new Exception(__('Error uploading categories.', ELECTION_UTILITIES_TEXTDOMAIN ) );
	                }

	                break;

	            case 'get_child_category_dropdown':
		            if ( !isset( $_REQUEST['parentID'] ) || !isset( $_REQUEST['dropdownElementID'] ) ) {
			            throw new Exception(__('Invalid get category dropdown request', ELECTION_UTILITIES_TEXTDOMAIN ) );
		            }

		            // TODO: security issue: validate inputs
					$output = $this->get_child_category_dropdown( $_REQUEST['parentID'], $_REQUEST['dropdownElementID']  );
                    break;

	            case 'fetch_election_overview':
		            if ( !isset( $_REQUEST['viewParameters'] )   ) {
			            throw new Exception(__('Invalid get fetch election overview request', ELECTION_UTILITIES_TEXTDOMAIN ) );
		            }
					$output = $this->fetch_election_overview();

	            	break;


	            case 'fetch_ballot_contest':
		            if ( !isset( $_REQUEST['viewParameters'] )   ) {
			            throw new Exception(__('Invalid get fetch ballot contest request', ELECTION_UTILITIES_TEXTDOMAIN ) );
		            }
		            $output = $this->fetch_ballot_contest();

		            break;

                default:
                    $output = __('Unknown ajax request sent from client.', ELECTION_UTILITIES_TEXTDOMAIN );
                    break;
            }
            
        }
        catch ( Exception $e ) {
            $errorData = array(
                'errorData' => 'true',
                'errorMessage' => $e->getMessage(),
                'errorTrace' => $e->getTraceAsString()
            );
            $output = $errorData;
        }
        
        $output=json_encode($output);
        if(is_array($output)){
            print_r($output);
        }
        else {
            echo  $output ;
        }
        die;
    }

    public function election_utilities_ajax() {
       $this->execute_request();
    }

    
    private function sanitize_data( $raw_data ) {

		// TODO: sanitize data

		return $raw_data;

		/*
        $scrubbed_data = array(

        );

        return $scrubbed_data;
		*/
    }

    private function upload_categories( $data ) {

		// TODO: seurity checks
		$uploader = new Election_Category_Uploader( $data );
		return $uploader->handle_upload();
    }

    private function get_child_category_dropdown ( $parentID, $dropdownElementID ) {

		// Sanitize
		$cat_id = intval( $parentID);

		$dropdown_generator = new Category_Dropdown_Generator();
		$dropdown = $dropdown_generator->get_child_categories( $cat_id, 1, $dropdownElementID );
		return $dropdown ;

    }

    private function upload_questionnaire_responses( $data ) {
		$uploader = new Response_Uploader( $data );
		return $uploader->handle_upload();
    }

    private function fetch_election_overview ( ) {

		// Sanitize data
	    $view_parameters = json_decode( stripslashes($_REQUEST['viewParameters']), true, 10 ) ;
	    $eo = new Election_Overview();
	    return $eo->fetch( $view_parameters );
    }

	private function fetch_ballot_contest ( ) {

		// TODO: Sanitize data
		$view_parameters = json_decode( stripslashes($_REQUEST['viewParameters']), true, 10 ) ;
		$bc = new Ballot_Contest();
		return $bc->fetch( $view_parameters );
	}



}