<?php
/**
 * 
 * 
 * 
 */
?>
<option value="">Select Opening</option>
<?php foreach($openings as $opening){ 
    $event_date = get_post_meta( $opening->ID, '_event_date', true );
    ?>
    <option value="<?=$opening->ID?>"><?=$opening->post_title." (".date("d M, Y", strtotime($event_date)).")"?></option>
<?php } ?>
