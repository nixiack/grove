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
	<?php echo do_shortcode('[wooslider slide_page="'.get_option("slide_page").'" slider_type="slides" limit="5"]') ?>
	</div>
	</div>
	
<?php } ?>