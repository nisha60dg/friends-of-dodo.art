<?php
/**
 * 
 * 
 * 
 */

$_event_date = get_post_meta( $event->ID, '_event_date', true );

$artist_gallery = artgallery_get_artist_gallery($artist->ID, $event->ID);
?>
<div class="wrap">
    <h1 style="width:100%; display:inline-block; margin-bottom:15px;">
        <?=$artist->post_title?> : <?=$event->post_title?> (<small>Event Date: <?=artgallery_show_date($_event_date)?></small>) Gallery
    </h1> 

    <br />
    <a href="javascript:;" class="form-control add_gallery_image button button-info"> Add Images</a>
    <a href="javascript:;" class="form-control save_gallery_image button button-primary "> Update Gallery </a>
    <p style="width:100%; display:inline-block;">Please click Update Gallery after any change you make to save the changes.</p>
    <p class="gallery-upload-message"></p>

    <div class="">
        
    </div>

    <div class="media-frame wp-core-ui mode-grid mode-edit hide-menu">
        <div class="media-frame-tab-panel" style="background:#fff; min-height:460px;">
            <div class="media-frame-content" data-columns="8">
                <div class="media-frame-tab-panel">
                    <div class="attachments-browser has-load-more hide-sidebar sidebar-for-errors">
                        <div class="attachments-wrapper">
                            <ul tabindex="-1" class="attachments ui-sortable ui-sortable-disabled artist-gallery-images" id="__attachments-view">
                                <?php if(!empty($artist_gallery)){ $gallery_images = explode(",", $artist_gallery->gallery_images)?>
                                    <?php foreach($gallery_images as $index=>$attachment){
                                        $img_src = wp_get_attachment_url($attachment);
                                        ?>
                                        <li tabindex="<?=$index?>" role="checkbox" aria-label="twelve logo white 1-1" aria-checked="false" data-id="<?=$attachment?>" class="attachment selected">
                                            <div class="attachment-preview js--select-attachment type-image subtype-webp portrait">
                                                <div class="thumbnail">
                                                <div class="centered">
                                                    <img src="<?=$img_src?>" draggable="false" alt="">
                                                </div>
                                                </div>
                                            </div>
                                            <button type="button" class="remove-image check" data-id="<?=$attachment?>" tabindex="-1"><span class="media-modal-icon"> </span></button> 
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form name="artist-gallery-form">
        <input type="hidden" name="gallery_attachments" id="gallery_attachments" value="<?=(!empty($artist_gallery)) ? $artist_gallery->gallery_images : ''?>" />
        <input type="hidden" id="event_id" name="event_id" value="<?=$event->ID?>" />
        <input type="hidden" id="event_date" name="event_date" value="<?=$_event_date?>" />
        <input type="hidden" id="artist_id" name="artist_id" value="<?=$artist->ID?>" />
    </form>
</div>