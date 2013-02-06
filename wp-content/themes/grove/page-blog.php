<?php
/*
 * Template name: Custom archive
 *
 * The template for displaying all pages.
 *
 * This template can display a list of blog posts from a certain category, tag, etc.
 *
 * @package Grove
 * @since Grove 1.0
 */

get_header(); ?>

		<div id="primary" class="content-area">
			<div id="content" class="site-content" role="main">

				<?php
				global $paged;
				 
				query_posts(array(
				'category_name' => get_post_meta($post->ID, '_ignite_category', true),
				'tag' => get_post_meta($post->ID, '_ignite_tags', true),
				'paged' => $paged // set the current page
				));
				 
				if (have_posts()):
				 
				// Loop
				?>

			<?php echo get_post_meta($post->ID, '_ignite_tags', true) ?>

			
        <?php while (have_posts()): the_post(); ?>

            <?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );
					?>

        <?php endwhile; ?>
      
        <div class="nav-previous"><?php next_posts_link('← Older posts'); ?></div>
    <div class="nav-next"><?php previous_posts_link('Newer posts →'); ?></div>
        <?php endif; ?>

			</div><!-- #content .site-content -->
		</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>