(function( $ ) {
	'use strict';


    $(document).ready( function(){

        // Just to be sure that the input will be called
        $("#category_file_upload").on("click", function(){
            $('#category_file_input').click(function(event) {
                event.stopPropagation();
            });
        });

        $('#election-dropdown').click( function( event ) {
            var catID = $('#election-dropdown').val();
            getCategoryDropDown( catID, '#office-dropdown-container' );
        });

        $('#category_file_input').on('change', prepareUpload);
        $('.category-upload-button').click( doUpload );



        var file;
        var parent;
        var data;

        function prepareUpload(event) {

            file = event.target.files;
            parent = $("#" + event.target.id).parent();
            data = new FormData();
            $.each(file, function (key, value) {
                data.append('category_file_upload', value);
            });
            $('.category-upload-button').removeAttr('disabled');
        }

        function doUpload() {

            data.append('action','election_utilities_ajax');
            data.append('fn','upload_categories');

            add_message('File upload in progress.', 'info');

            $.ajax({
                url: wpNg.config.ajaxUrl,
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function(data, textStatus, jqXHR) {

                    if ( data.errorData != null && data.errorData == 'true' ) {
                        add_message('File upload failed', 'danger');
                    }
                    else {
                        add_message('Yay! The file is uploaded', 'success');
                    }

                    $('.category-upload-button').attr('disabled','disabled');
                    $('#category_file_input').val('');


                },
                error: function(errorThrown){
                    add_message('File upload failed because of an exception', 'danger');
                    console.log(errorThrown);
                }

            });

        }

        function getCategoryDropDown( parentID, targetContainer ) {
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

                    if ( data.errorData != null && data.errorData == 'true' ) {
                        add_message('Could not retreive child category dropdown', 'danger');
                    }

                    displayDropdown( data, targetContainer);

                },
                error: function(errorThrown){
                    add_message('File upload failed because of an exception', 'danger');
                    console.log(errorThrown);
                }

            });
        }

        function displayDropdown ( dropdownHTML, targetContainer ) {
            $( targetContainer).html( dropdownHTML);
        }



        function add_message($msg, $type){
            var html = "<div class='alert alert-"+$type+"'>" + $msg + "</div>";
            $(".category_upload_message").empty().append(html);
            $(".category_upload_message").fadeIn();
            setTimeout(function() { $(".category_upload_message").fadeOut("slow"); }, 2000);
        }

        function reportError ( error ) {
            console.log ( error );
        }

    });


})( jQuery );
