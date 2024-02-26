<?php
/** 
 * Renders Select Artist page to create the gallery
 * 
 * 
 * 
 * 
*/
?>
<div class="wrap">
    <h1 style="width:100%; display:inline-block; margin-bottom:15px;">Select Your Artist</h1>
    <?php if(!empty($artists)){ ?>
        <form id="art-gallery-artists" method="get" action="<?=esc_url( admin_url('edit.php') )?>">
            <input type="hidden" name="post_type" value="artists" />
            <input type="hidden" name="page" value="artist-gallery" />
            <input type="hidden" name="action" value="create_gallery" /> 
            <div class="form-group" style="width:100%; display:inline-block; margin-bottom:15px;">
                <label for="_event_date" style="width:25%; display:inline-block; font-weight:bold;vertical-align: top;">Artist </label>
                <div style="width:74%; display:inline-block;">
                    <select name="artist_id" id="artist_id" class="form-control select-artist">
                        <option value="">Choose Artist</option>
                        <?php foreach($artists as $artist){ ?>
                            <option value="<?=$artist->ID?>"><?=$artist->post_title?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group" style="width:100%; display:inline-block; margin-bottom:15px;">
                <label for="_event_date" style="width:25%; display:inline-block; font-weight:bold;vertical-align: top;">Opening Date </label>
                <div style="width:74%; display:inline-block;">
                    <select name="event_id" id="event_id" class="form-control artist-events-options">
                        <option value="">Select Event Date</option>
                    </select>
                    <p class="artist-event-dates-message"></p>
                </div>
            </div>

            <div class="form-group" style="width:100%; display:inline-block; margin-bottom:15px;">
                <label for="_event_date" style="width:25%; display:inline-block; font-weight:bold;">&nbsp; </label>
                <button type="submit" class="button button-primary create-artist-gallery" disabled="disabled">Manage Gallery</button>
            </div>
        </form>
    <?php }else{ ?>
        <div style="width:100%; display:inline-block; margin-bottom:15px;"> 
            There is not any Artist exist under Artists. Please add Artists to manage their Art work.
        </div>
        <a href="<?=esc_url( admin_url( 'edit.php' ).'?post_type=artists')?>" class="button button-primary">Create Artist</a> 
    <?php } ?>
</div>