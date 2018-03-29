(function( $ ) {
	'use strict';


    $(document).ready( function(){

        // Just to be sure that the input will be called
        $("#category_file_upload").on("click", function(){
            $('#category_file_input').click(function(event) {
                event.stopPropagation();
            });
        });

        $(document).on('change', '#election-dropdown', function( event ) {
            var catID = $('#election-dropdown').val();
            getCategoryDropdown( catID, '#office-dropdown-container' );
        });

        $(document).on( 'change' ,'#office-dropdown',function( event ) {
            var catID = $('#office-dropdown').val();
            $('#response-file-upload').show();
        });

        $('#category_file_input').on('change', prepareElectionCategoryUpload);
        $('.category-upload-button').click( doCategoryUpload );

        $('#response-file-input').on('change', prepareResponseUpload);
        $('.response-upload-button').click( doResponseUpload );


        var file;
        var parent;
        var data;

        function prepareElectionCategoryUpload(event) {

            file = event.target.files;
            parent = $("#" + event.target.id).parent();
            data = new FormData();
            $.each(file, function (key, value) {
                data.append('category_file_upload', value);
            });
            $('.category-upload-button').removeAttr('disabled');
        }

        function prepareResponseUpload(event) {

            file = event.target.files;
            parent = $("#" + event.target.id).val();
            data = new FormData();
            $.each(file, function (key, value) {
                data.append('response_file_upload', value);
            });
            $('.response-upload-button').removeAttr('disabled');
        }

        function doCategoryUpload() {

            data.append('action','election_utilities_ajax');
            data.append('fn','upload_categories');


            add_message('File upload in progress.', 'info' ,'.category_upload_message');

            $('.spinner').show();

            $.ajax({
                url: wpNg.config.ajaxUrl,
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function(data, textStatus, jqXHR) {

                    $('.spinner').hide();

                    if ( data.errorData != null && data.errorData == 'true' ) {
                        add_message('File upload failed', 'danger','.category_upload_message');
                    }
                    else {
                        add_message('Yay! The file is uploaded', 'success','.category_upload_message');
                    }

                    $('.category-upload-button').attr('disabled','disabled');
                    $('#category_file_input').val('');


                },
                error: function(errorThrown){

                    $('.spinner').hide();

                    add_message('File upload failed because of an exception', 'danger', '.category_upload_message');
                    console.log(errorThrown);
                }

            });

        }

        function doResponseUpload() {

            var parentID = $('#office-dropdown').val();

            data.append('action','election_utilities_ajax');
            data.append('fn','upload_questionnaire_responses');
            data.append( 'parentID', parentID );

            add_message('File upload in progress.', 'info', '.response-upload-message');

            $('.spinner').show();

            $.ajax({
                url: wpNg.config.ajaxUrl,
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function(data, textStatus, jqXHR) {

                    $('.spinner').hide();

                    if ( data.errorData != null && data.errorData == 'true' ) {
                        add_message('Response file upload failed', 'danger' , '.response-upload-message');
                    }
                    else {
                        add_message('Yay! The response file is uploaded', 'success' , '.response-upload-message');
                    }

                    $('.response-upload-button').attr('disabled','disabled');
                    $('#response-file-input').val('');


                },
                error: function(errorThrown){

                    $('.spinner').hide();
                    add_message('File upload failed because of an exception', 'danger', '.response-upload-message');
                    console.log(errorThrown);
                }

            });

        }



        function getCategoryDropdown( parentID, targetContainer ) {

            $('.spinner').show();
            $.ajax({
                url: wpNg.config.ajaxUrl,
                type: 'POST',
                data:{
                    'action':'election_utilities_ajax',
                    'fn':'get_child_category_dropdown',
                    'parentID' : parentID,
                },
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    $('.spinner').hide();

                    if ( data.errorData != null && data.errorData == 'true' ) {
                        add_message('Could not retreive child category dropdown', 'danger', '.category_upload_message');
                    }

                    displayDropdown( data, targetContainer);

                },
                error: function(errorThrown){
                    $('.spinner').hide();
                    add_message('File upload failed because of an exception', 'danger', '.category_upload_message');
                    console.log(errorThrown);
                }

            });
        }

        function displayDropdown ( dropdownHTML, targetContainer ) {
            $( targetContainer).html( dropdownHTML);
        }



        function add_message($msg, $type, targetContainer ){
            var html = "<div class='alert alert-"+$type+"'>" + $msg + "</div>";
            $(targetContainer).empty().append(html);
            $(targetContainer).fadeIn();
            setTimeout(function() { $(targetContainer).fadeOut("slow"); }, 2000);
        }

        function reportError ( error ) {
            console.log ( error );
        }

    });


})( jQuery );
