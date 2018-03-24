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
	                        throw new Exception(_e('Invalid upload categories request.', ELECTION_UTILITIES_TEXTDOMAIN ) );
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
		$uploader = new File_Uploader( $data );
		return $uploader->handle_upload();
    }


}