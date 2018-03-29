<?php
/**
 * Created by PhpStorm.
 * User: weskempferjr
 * Date: 7/24/17
 * Time: 3:48 PM
 */

class Election_Utilities_Settings
{

    private $options_name = ELECTION_UTILITIES_OPTIONS_NAME;
    

    /**
     * Constructor
     *
     * @since 0.0.1
     */
    public function __construct() {

    }

    /*
	 * Get the theme options name.
	 *
	 * @return string theme options name.
	 */
    public function get_options_name() {
        return $this->options_name;
    }

    /*
	 * This function was intended to be called to delete the
	 * options from the database.
	 *
	 * @since 0.0.1
	 */

    public function delete_options() {
        if ( current_user_can('delete_plugins') ) {
            delete_option($this->options_name );
        }
    }

    /*
	 * This method defines the theme settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return void
	 */
    public function settings_init(  ) {

        register_setting( 'election_utilities-settings-group', $this->options_name, array( $this, 'sanitize') );

        add_settings_section(
            'election_utilities-settings-general-section',
            __( 'Election Utilities Settings', ELECTION_UTILITIES_TEXTDOMAIN ),
            array($this, 'section_general_render'),
            'election_utilities-settings-page'
        );

        add_settings_field(
            'publication_name',
            __( 'Name of publication', ELECTION_UTILITIES_TEXTDOMAIN ),
            array($this, 'publication_name_render'),
            'election_utilities-settings-page',
            'election_utilities-settings-general-section'
        );


    }


    /*
     * Render general settings section info.
     * @since 0.0.1
     */
    public function section_general_render () {
        echo '<p>' . __("Settings for Election Utilities", ELECTION_UTILITIES_TEXTDOMAIN) . '</p>';
    }

    public function section_publish_render () {
        echo '<p>' . __("Post Election Utilities", ELECTION_UTILITIES_TEXTDOMAIN) . '</p>';
        echo '<a class="election_utilities-post-btn button-secondary" title="' . __('Click this to generate a post listing all current notices.', ELECTION_UTILITIES_TEXTDOMAIN) . '">' . __('Generate Post for Current Election Utilities') . '</a><div class="spinner"></div>';
    }



    public function publication_name_render(  ) {

        $options = get_option( $this->options_name );
        if ( isset($options['publication_name'] )) {
            $publication_name = $options['publication_name'] ;
        }
        else {
            $publication_name = '';
        }
        ?>
        <input id="election_utilities-publication-name"  type="text" name="public_notice_options[publication_name]" value=" <?php echo $publication_name ; ?>">
        <?php

    }



    /*
	 * Calls add_options_page to register the page and menu item.
	 * 
	 * @since 0.0.1
	 * 
	 * @param none
	 */
    public function add_options_page( ) {

        // Add the top-level admin menu
        $page_title = 'Election Utilities Settings';
        $menu_title = 'Election Utilities';
        $capability = 'manage_options';
        $menu_slug = 'election_utilities-settings';
        $function = 'settings_page';
        add_options_page($page_title, $menu_title, $capability, $menu_slug, array($this, $function)) ;


    }

    /*
	 * Defines and displays the plugin settings page.
	 * @since 0.0.1
	 * 
	 * @param none
	 * @return none
	 */
    public function settings_page(  ) {

        $this->add_option_defaults();

        ?>
        <div class="wrap">
            <form action='options.php' method='post'>

                <div id="election_utilities-settings-container">
                    <?php

                    settings_fields( 'election_utilities-settings-group' );
                    do_settings_sections( 'election_utilities-settings-page' );

                    ?>

                    <?php
                    submit_button();
                    ?>
                </div>
                <div id="election_utilities-settings-info-container">

                </div>


            </form>
        </div>
        <div class="category_upload_message"></div>
        <div id="category_file_upload" class="file-upload">
            <h3><?php _e('Upload Election Categories', ELECTION_UTILITIES_TEXTDOMAIN) ; ?></h3>
            <p><?php  _e('Choose a CSV file containing election categories and then click the Upload button.', ELECTION_UTILITIES_TEXTDOMAIN) ;?></p>
            <input type="file" id="category_file_input" />
            <a class="button button-secondary category-upload-button" disabled>Upload</a>
        </div>


        <div class="questionnaire-upload-container">
            <h3><?php _e('Upload Candidate Questionnaire Responses', ELECTION_UTILITIES_TEXTDOMAIN) ; ?></h3>
            <div id="election-dropdown-container" class="eu-dropdown-container">
	            <?php
	            $root_term = term_exists( 'Voter Guide Root', 'category', 0);
	            if ( $root_term ) {
		            $root_cat = $root_term['term_id'];

		            $dropdown_generator = new Category_Dropdown_Generator();
		            echo $dropdown_generator->get_child_categories( $root_cat, 1, 'election-dropdown' );
	            }
	            ?>
            </div>
            <div id="office-dropdown-container" class="eu-dropdown-container">
            </div>

            <div class="response-upload-message"></div>
            <div id="response-file-upload" class="file-upload">
                <p><?php  _e('Choose a CSV file containing questionnaire responses and then click the Upload button.', ELECTION_UTILITIES_TEXTDOMAIN) ;?></p>
                <input type="file" id="response-file-input" />
                <a class="button button-secondary response-upload-button" disabled>Upload</a>
            </div>


        </div>
        <div class="spinner"></div>
        <?php

    }

    /*
         * Sanitize user input before passing values on to update options.
         * @since 1.0.0
         */
    public function sanitize( $input ) {

        $new_input = array();


        if( isset( $input['publication_name'] ) ) {
            $new_input['publication_name'] = sanitize_text_field( $input['publication_name'] );
        }
        else {
            // set to default
            $new_input['publication_name'] = '' ;
        }


        return $new_input ;
    }

    public function add_option_defaults() {

        if ( current_user_can('install_themes') ) {

            if ( get_option( ELECTION_UTILITIES_OPTIONS_NAME ) === false ) {
                $options = array();

                $options['publication_name'] = '';

                add_option( $this->options_name, $options );
            }

        }

    }

}