<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Grove
 * @since Grove 1.0
 */

get_header(); ?>

		

			<?php if (get_theme_mod( 'small_slider' )) { ?>

			<div class="slider half">
			<?php echo do_shortcode('[wooslider slide_page="'.get_theme_mod("slide_page").'" slider_type="slides" limit="5"]') ?>
			</div>

			
			<div class="homepage-features">
			<?php echo get_theme_mod('slide_feature_static') ?>
			</div>

			<?php } else {?>
			<div class="slider-outer">
			<div class="slider">
			<?php echo do_shortcode('[wooslider slide_page="'.get_theme_mod("slide_page").'" slider_type="slides" limit="5"]') ?>
			</div>
			</div>
			<?php } ?>

			<?php  ?>

			<?php get_template_part('hot', 'buttons'); ?>

			<?php if (get_theme_mod('show_tweet_ticker') AND get_theme_mod('tweet_ticker_user')) {
				

					$recent_tweets = get_transient('recent_tweets_'.get_theme_mod('tweet_ticker_user'));
					if (!$recent_tweets){
					$tweets = wp_remote_retrieve_body( wp_remote_get('http://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name='.get_theme_mod('tweet_ticker_user').'&count=10') );
					$recent_tweets = json_decode($tweets, true);
					set_transient('recent_tweets_'.get_theme_mod('tweet_ticker_user'), $recent_tweets, 60 * 10);
					}

					if ($recent_tweets){ 

					 ?>
<div class="tweets-container-outer">				 
<div class="tweets-container">
						<ul class="tweetscroll">
						<?php
					foreach ($recent_tweets as $tweet) {?>

						<li class="tweet">
							<span class="tweet-body">
								<?php echo make_clickable_tweet($tweet['text']); ?>
							</span>
							<span class="tweet-stamp">
								<?php echo human_time_diff( strtotime($tweet['created_at']), current_time('timestamp') ) . ' ago';  ?>
							</span>
						</li>
					<?php }	echo '</ul>'; } ?>

					<script>
	    				jQuery(document).ready(function(){
						jQuery("ul.tweetscroll").show().liScroll();
						});
						</script>

						<a class="follow-button" href="http://twitter.com/<?php echo get_theme_mod('tweet_ticker_user'); ?>"><i class="ss-icon ss-social">Twitter</i>Follow @<?php echo get_theme_mod('tweet_ticker_user'); ?></a>

					 <?php } ?>

					
					</div>
				</div>

					<?php if (count_sidebar_widgets('homepage-featured-content', false) > 0) { ?>
			<div class="homepage-featured-content-outer">
			<div class="homepage-featured-content widgets-<?php count_sidebar_widgets('homepage-featured-content') ?>">
			<?php if ( ! dynamic_sidebar( 'homepage-featured-content' ) ) : endif; ?>
			</div>
			</div>
			<?php } ?>

		

<?php get_footer(); ?>