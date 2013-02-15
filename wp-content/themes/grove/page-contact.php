<?php
/*
 * Template name: Contact Page
 *
 * @package Grove
 * @since Grove 1.0
 */

get_header(); ?>

<?php $hide_sidebar = get_post_meta(get_the_ID(), '_ignite_hide_sidebar', true);
		$banner = get_post_meta($post->ID, '_ignite_banner_size', true);

if (has_post_thumbnail()) {
			$attr = array(
			'alt'	=> trim(strip_tags( get_the_title() )),
			'title'	=> trim(strip_tags( get_the_title() )),
		);

if ($banner!='hide') { if ($banner=='large' OR $hide_sidebar=='hide') { the_post_thumbnail('960', $attr); } else {$image = get_the_post_thumbnail($post->ID, '720', $attr);} } } ?>

		<div id="primary" class="content-area">
			<div id="content" class="site-content" role="main">

				<?php echo $image; ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php echo do_shortcode("[contact-form][contact-field label='Name' type='name' required='1'/][contact-field label='Email' type='email' required='1'/][contact-field label='Website' type='url'/][contact-field label='Comment' type='textarea' required='1'/][/contact-form]") ?>

					<?php add_action('before_sidebar', 'sidebar_map');

					 function sidebar_map() {
						if (get_option('address')) { echo '<div class="location">'.do_shortcode('[gmap height="240px"]'.get_option('address').'[/gmap]'); echo '<strong>'.get_option('address').'</strong></div>'; } else { }
					} ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content .site-content -->
		</div><!-- #primary .content-area -->

<?php if ($hide_sidebar!='hide') { get_sidebar(); }; ?>
<?php get_footer(); ?>