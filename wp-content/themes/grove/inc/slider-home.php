<?php if (get_option( 'small_slider' )) { ?>

	<div class="small-slider-outer">
	<div class="slider half">
	<?php echo do_shortcode('[wooslider slide_page="'.get_option("slide_page").'" slider_type="slides" thumbnails="thumbnails" order="DESC" order_by="menu_order" limit="5"]') ?>
	</div>
	
	<div class="homepage-features">
	<?php echo get_option('slide_feature_static') ?>
	</div>
	</div>

<?php } else {?>

	<div class="slider-outer">
	<div class="slider">
	<?php  ?>
    
    <?php 
	
	$slide_type = get_option('slide_type');
	
	if($slide_type == 'posttype') {
		$mmm = 'wooslider category="'.get_option('slide_pcat').'" slider_type="posts" layout="text-'.get_option('slide_text_side').'" order="'.get_option("slide_order_by").'" limit="'.get_option("slide_num").'"';
		
		if(get_option("slider_linking") != '') { $mmm .= ' link_title="'.get_option("slider_linking").'"'; }
	
		if(get_option("slide_custom_id") != '') { $mmm .= ' id="'.get_option("slide_custom_id").'"'; }
	
		echo do_shortcode('['.$mmm.']');
		
	
	} else if($slide_type == 'sliderstype') {
		$mmm = 'wooslider slide_page="'.get_option("slide_page").'" slider_type="slides" smoothheight="true" thumbnails="'.get_option("slide_pagination").'" order="'.get_option("slide_order_by").'" order_by="'.get_option("slide_sort_by").'" limit="'.get_option("slide_num").'"';
		
		if(get_option("slide_custom_id") != '') { $mmm .= ' id="'.get_option("slide_custom_id").'"'; }
		
		echo do_shortcode('['.$mmm.']');
		
	}
	
	?>
    
    
	</div>
	</div>
	
<?php } ?>