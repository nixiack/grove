<?php if (get_option( 'small_slider' )) { ?>

	<div class="small-slider-outer">
	<div class="slider half">
	<?php echo do_shortcode('[wooslider slide_page="'.get_option("slide_page").'" slider_type="slides" limit="5"]') ?>
	</div>

	
	<div class="homepage-features">
	<?php echo get_option('slide_feature_static') ?>
	</div>
	</div>

<?php } else {?>

	<div class="slider-outer">
	<div class="slider">
	<?php //echo do_shortcode('[wooslider slide_page="'.get_option("slide_page").'" slider_type="slides" limit="5"]') ?>
    
    <?php 
	
	$slide_type = get_option('slide_type');
	
	if($slide_type == 'posttype') {
		$mmm = 'wooslider category="'.get_option('slide_pcat').'" slider_type="posts" layout="text-'.get_option('slide_text_side').'" overlay="'.get_option("slide_text_overlay").'" limit="'.get_option("slide_num").'"';
																																																					 		if(get_option("slider_linking") != '') { $mmm .= ' link_title="'.get_option("slider_linking").'"'; }
	
		if(get_option("slide_custom_id") != '') { $mmm .= ' id="'.get_option("slide_custom_id").'"'; }
	
		echo do_shortcode('['.$mmm.']');
		
	
	} else if($slide_type == 'sliderstype') {
		$mmm = 'wooslider slide_page="'.get_option("slide_page").'" slider_type="slides" smoothheight="true" limit="'.get_option("slide_num").'"';
		
		if(get_option("slide_custom_id") != '') { $mmm .= ' id="'.get_option("slide_custom_id").'"'; }
		
		echo do_shortcode('['.$mmm.']');
		
	}
	
	?>
    
    
	</div>
	</div>
	
<?php } ?>