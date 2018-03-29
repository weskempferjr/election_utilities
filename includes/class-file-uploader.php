<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 3/22/18
 * Time: 12:43 PM
 */

abstract class File_Uploader {

	protected $__file_errors ;
	protected $__data;


	/**
	 * File_Uploader constructor.
	 */
	public function __construct( $data) {

		$this->__data = $data;

		$this->__file_errors = array(
			0 => __('There is no error, the file uploaded with success', ELECTION_UTILITIES_TEXTDOMAIN),
			1 => __('The uploaded file exceeds the upload_max_files in server settings',ELECTION_UTILITIES_TEXTDOMAIN),
			2 => __('The uploaded file exceeds the MAX_FILE_SIZE from html form',ELECTION_UTILITIES_TEXTDOMAIN),
			3 => __('The uploaded file uploaded only partially',ELECTION_UTILITIES_TEXTDOMAIN),
			4 => __('No file was uploaded',ELECTION_UTILITIES_TEXTDOMAIN),
			6 => __('Missing a temporary folder',ELECTION_UTILITIES_TEXTDOMAIN),
			7 => __('Failed to write file to disk',ELECTION_UTILITIES_TEXTDOMAIN),
			8 => __('A PHP extension stoped file to upload',ELECTION_UTILITIES_TEXTDOMAIN) );

	}

	abstract public function handle_upload() ;

}