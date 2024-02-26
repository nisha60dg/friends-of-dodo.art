jQuery(function($){
    
    if($('.select2').length){
        $('.select2').select2();
    }

    $(document).on("change",".select-artist", function(){
        var artist_id = $(this).val();
        $(".artist-event-dates-message").html('');
        $(".artist-events-options").html('<option value=""> Select Opening</option>'); 
        $(".create-artist-gallery").prop("disabled",true);
        if(artist_id !== "" || typeof artist_id != "undefined" ){
            jQuery.ajax({
                url:artgallery.ajaxurl,
                data:{
                    action:'artgallery_get_artists_events',
                    artist_id:artist_id,
                },
                type:'POST',
                dataType:'JSON',
                success:function(response){
                    // console.log(response);
                    if(response.status === true){
                        $(".artist-events-options").html(response.content); 
                        $(".create-artist-gallery").prop("disabled",false);
                    }else{
                        $(".artist-event-dates-message").html(response.message);
                    }
                }
            });
        }
    });
	
	$(document).on("change","._artwork_artist", function(){
        var artist_id = $(this).val();
        $("._artwork_opening").html('<option value=""> Select Opening</option>'); 
        if(artist_id !== "" || typeof artist_id != "undefined" ){
            jQuery.ajax({
                url:artgallery.ajaxurl,
                data:{
                    action:'artgallery_get_artists_events',
                    artist_id:artist_id,
                },
                type:'POST',
                dataType:'JSON',
                success:function(response){
                    // console.log(response);
                    if(response.status === true){
                        $("._artwork_opening").html(response.content); 
                    }
                }
            });
        }
    });

    $('body').on( 'click', '.add_gallery_image', function(e){
        e.preventDefault();

        var button = $(this),
        custom_uploader = wp.media({
            title: 'Insert image',
            library : {
                // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                type : 'image'
            },
            button: {
                text: 'Use this image' // button label text
            },
            multiple: true 
        }).on('select', function() { // it also has "open" and "close" events
            var attachments = custom_uploader.state().get('selection').toJSON();
            var gallery_attachments = '';
            
            var artist_gallery_images = $("#gallery_attachments").val();
            artist_gallery_images = artist_gallery_images.split(",");
            
            $.each(attachments, function(index,attachment){
                var exists = false;
                $.each(artist_gallery_images, function(index, data) {
                    if (data == attachment.id) {
                        exists = true;
                    }
                });

                if(exists == false){
                    // $(".artist-gallery-images").append('<img src="' + attachment.url + '">').next().show().next().val(attachment.id);
                    var image_html = '<li tabindex="'+( parseInt(artist_gallery_images.length) )+'" role="checkbox" aria-label="twelve logo white 1-1" aria-checked="false" data-id="' + attachment.id + '" class="attachment selected"><div class="attachment-preview js--select-attachment type-image subtype-webp portrait"><div class="thumbnail"><div class="centered"><img src="' + attachment.url + '" draggable="false" alt=""></div></div></div><button type="button" class="remove-image check" data-id="' + attachment.id + '" tabindex="-1"><span class="media-modal-icon"> </span></button></li>';
                    $(".artist-gallery-images").append(image_html);
                    
                    artist_gallery_images.push(attachment.id);
                    gallery_attachments = artist_gallery_images.join(",");
                    $("#gallery_attachments").val(gallery_attachments);
                }
            });
        }).open();

    });

    // on remove button click
    $(document).on('click', '.remove-image', function(e){
        
        e.preventDefault();

        var button = $(this);
        // button.next().val(''); // emptying the hidden field
        // button.hide().prev().html('Upload image');
        var image_id = button.data('id');
        var gallery_attachments = $("#gallery_attachments").val();
        gallery_attachments = gallery_attachments.split(",");
        var image_id = button.data('id');
        gallery_attachments = jQuery.grep(gallery_attachments, function(value) {
            return value != image_id;
        });

        // console.log("gallery_attachments ", gallery_attachments);
        gallery_attachments = gallery_attachments.join(",");
        // console.log("gallery_attachments ", gallery_attachments);
        $("#gallery_attachments").val(gallery_attachments);
        button.closest("li").remove();

    });

    $(document).on("click", ".save_gallery_image", function(){
        var gallery_attachments = $("#gallery_attachments").val();
        if(gallery_attachments !== "" || typeof gallery_attachments != "undefined" ){
            jQuery.ajax({
                url:artgallery.ajaxurl,
                data:{
                    action:'artgallery_save_artist_gallery_attachments',
                    artist_id:$("#artist_id").val(),
                    event_id:$("#event_id").val(),
                    event_date:$("#event_date").val(),
                    gallery_attachments: gallery_attachments,
                },
                type:'POST',
                dataType:'JSON',
                success:function(response){
                    // console.log(response);
                    if(response.status === true){
                        alert("Congrats! Your gallery has been updated successfully.");
                        // setInterval(function () {
                            window.location = window.location.href;
                        // }, 2000);
                        
                    }else{
                        $(".gallery-upload-message").html(response.message);
                    }
                }
            });
        }
    });

    $('body').on( 'click', '.upload_artist_image_button', function(e){
        e.preventDefault();

        var button = $(this),
        custom_uploader = wp.media({
            title: 'Insert image',
            library : {
                // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                type : 'image'
            },
            button: {
                text: 'Use this image' // button label text
            },
            multiple: false 
        }).on('select', function() { // it also has "open" and "close" events
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            
            button.parent('div').find("._window_image").val(attachment.id);
            button.parent('div').find("._window_image_preview").attr("src",attachment.url).removeClass('d-none');
        }).open();

    });
})