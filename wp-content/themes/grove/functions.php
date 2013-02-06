<?php
/**
 * Grove functions and definitions
 *
 * @package Grove
 * @since Grove 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Grove 1.0
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

if ( ! function_exists( 'grove_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since Grove 1.0
 */
function grove_setup() {

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );

	require( get_template_directory() . '/inc/metabox/functions.php' );

	require_once ( get_template_directory() . '/page-maker.php' );

	/**
	 * Custom functions that act independently of the theme templates
	 */
	//require( get_template_directory() . '/inc/tweaks.php' );

	/**
	 * Custom Theme Options
	 */
	//require( get_template_directory() . '/inc/theme-options/theme-options.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on Grove, use a find and replace
	 * to change 'grove' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'grove', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'grove' ),
	) );

	/**
	 * Add support for the Aside Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'quote' ) );
}
endif; // grove_setup
add_action( 'after_setup_theme', 'grove_setup' );

/**
 * Register widgetized areas
 *
 * @since Grove 1.0
 */
function grove_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar', 'grove' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Homepage Featured Content', 'grove' ),
		'id' => 'homepage-featured-content',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer', 'grove' ),
		'id' => 'footer-widgets',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}
add_action( 'widgets_init', 'grove_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function grove_scripts() {

	if (get_theme_mod('body-font') == "Open Sans") { wp_enqueue_style( 'Open Sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,700'); }
	wp_enqueue_style( 'style', get_stylesheet_uri() );
	wp_enqueue_style( 'layout', get_template_directory_uri() . '/layouts/content-sidebar.css');

	// wp_enqueue_script( 'small-menu', get_template_directory_uri() . '/js/small-menu.js', array( 'jquery' ), '20120206', true );
	wp_enqueue_script( 'ticker', get_template_directory_uri() . '/js/jquery.li-scroller.1.0.js', array( 'jquery' ), '1.1', false);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'grove_scripts' );

//add some custom image sizes
add_image_size( '960', 960, 9999 );
add_image_size( '720', 720, 9999 );


$args = array(
	'flex-width'    => false,
	'width'         => 960,
	'flex-height'    => true,
	'height'        => 200,
	'default-image' => '',
	'uploads' => true
);
add_theme_support( 'custom-header', $args );

$defaults = array(
	'default-color'          => '',
	'default-image'          => '',
	'wp-head-callback'       => '',
	'admin-head-callback'    => '',
	'admin-preview-callback' => ''
);
add_theme_support( 'custom-background', $defaults );

add_action ('admin_menu', 'theme_customize');
function theme_customize() {
	add_menu_page( 'Grove', 'Grove', 'edit_theme_options', 'Grove', 'ignite_parent', site_url().'/wp-content/mu-plugins/inc/icon-ignite.png', 3 );
	add_submenu_page('Grove', 'Customize', 'Customize', 'edit_theme_options', 'customize.php?', '');
}

function ignite_parent() {
	echo '<div class="wrap"><h3>Enjoy your stay in the Grove.</h3></div>';
	
}

add_action('customize_register', 'ignite_hotbuttons');
function ignite_hotbuttons($wp_customize) {

	class WP_Customize_Social_Config extends WP_Customize_Control {
	public $type = 'social_setup';

	public function render_content() { ?>
	<script>	
	function popupwindow(url, title, w, h) {
  	var left = (screen.width/2)-(w/2);
  	var top = (screen.height/2)-(h/2);
  	return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
	} 
	</script>
	<style type="text/css">select{margin-bottom:20px;}</style>
		<a target="_blank" onclick="popupwindow('/wp-admin/options-general.php?page=sh_sb_settings_page&chromeless=true', 'Social Settings', 980, 640); return false;" href="/wp-admin/options-general.php?page=sh_sb_settings_page" style="padding:10px 0; text-align:center; background:#00a99d; color:#fff; border-radius:4px; display:block; margin:10px 0;">Configure Social Links</a>
	<?php }
	}

	class WP_Customize_Widget_Config extends WP_Customize_Control {
	public $type = 'widget_setup';

	public function render_content() { ?>
	<style type="text/css">select{margin-bottom:20px;}#customize-section-ignite_footer ul{padding-top:70px; position: relative;}</style>
		<a target="_blank" onclick="popupwindow('/wp-admin/widgets.php?chromeless=true', 'Widgets', 980, 800); return false;" href="/wp-admin/widgets.php" style="padding:10px 0; text-align:center; background:#00a99d; color:#fff; border-radius:4px; display:block; margin:10px 0; position:absolute; top:5px;left:20px; width:258px;">Configure Widgets</a>
	<?php }
	}

	class WP_Customize_Menu_Config extends WP_Customize_Control {
	public $type = 'menu_setup';

	public function render_content() { ?>
	<style type="text/css">select{margin-bottom:20px;}#customize-section-nav ul{padding-top:70px; position: relative;}</style>
		<a target="_blank" onclick="popupwindow('/wp-admin/nav-menus.php?chromeless=true', 'Menus', 980, 800); return false;" href="/wp-admin/widgets.php" style="padding:10px 0; text-align:center; background:#00a99d; color:#fff; border-radius:4px; display:block; margin:10px 0; position:absolute; top:5px;left:20px; width:258px;">Configure Navigation Links</a>
	<?php }
	}


	class WP_Customize_Textarea_Control extends WP_Customize_Control {
	public $type = 'textarea';

	public function render_content() {
		?>
		<label>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
		</label>

		<style type="text/css">
		#customize-section-static_front_page{display:none;}
		#customize-control-blogdescription{display: none;}
		#customize-section-ignite_hotbutton_1_settings .customize-section-content {padding-top:60px !important; position: relative;}
		#customize-section-ignite_hotbutton_2_settings .customize-section-content {padding-top:60px !important; position: relative;}
		#customize-section-ignite_hotbutton_3_settings .customize-section-content {padding-top:60px !important; position: relative;}
		#customize-section-ignite_hotbutton_4_settings .customize-section-content {padding-top:60px !important; position: relative;}
		#customize-section-ignite_hotbutton_5_settings .customize-section-content {padding-top:60px !important; position: relative;}
		#customize-section-ignite_hotbutton_2_settings{display: none;}
		#customize-section-ignite_hotbutton_3_settings{display: none;}
		#customize-section-ignite_hotbutton_4_settings{display: none;}
		#customize-section-ignite_hotbutton_5_settings{display: none;}
		#hb{position: absolute; top:20px; left: 20px;}
		#hb a{padding: 3px 5px; display: inline-block; background:#eee; border-radius: 2px; color: #777;}
		#customize-section-ignite_slider_settings #hb{display: none}</style>
		<script type="text/javascript">
		jQuery(document).ready(function() {

		jQuery("#clear").live("click", function(){
		  	jQuery(this).parents(".customize-section-content").find('input:eq(1), textarea').val('');
		  	jQuery(this).parents(".customize-section-content").find('input:eq(2)').val('http://').trigger('change');
		  	jQuery(this).parents(".customize-section-content").find(".remove").click();
		  	return false;
		  });

		  jQuery("#hb a").click(function() {
		  	var hbcount = jQuery(this).text();
		  	jQuery(".customize-section.open").removeClass("open").hide();
		  	jQuery("#customize-section-ignite_hotbutton_" + hbcount + "_settings").show();
		  	jQuery("#customize-section-ignite_hotbutton_" + hbcount + "_settings").addClass("open");
		  	return false;
		  });


		});
		</script>
		<nav id="hb">
			<a href="#">1</a>
			<a href="#">2</a>
			<a href="#">3</a>
			<a href="#">4</a>
			<a href="#">5</a>
		</nav>

		<a href="#clear" id="clear">Clear</a>

		<?php
	}

	
}

	$wp_customize->add_section( 'ignite_hotbutton_1_settings', array(
		'title'          => 'Hot Buttons',
		'description'	 => 'Manage the buttons/links in the middle of the homepage.',
		'priority'       => 44,
	) );

	$wp_customize->add_setting( 'hb_1_image', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hb_1_image', array(
		'label'   => 'Image Setting',
		'section' => 'ignite_hotbutton_1_settings',
		'settings'   => 'hb_1_image',
	) ) );

	$wp_customize->add_setting( 'hb_1_title', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'hb_1_title', array(
		'label'   => 'Title',
		'section' => 'ignite_hotbutton_1_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_1_link', array(
		'default'        => 'http://',
	) );

	$wp_customize->add_control( 'hb_1_link', array(
		'label'   => 'Link',
		'section' => 'ignite_hotbutton_1_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_1_excerpt', array(
	'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Textarea_Control( $wp_customize, 'hb_1_excerpt', array(
	'label'   => 'Excerpt (optional)',
	'section' => 'ignite_hotbutton_1_settings',
	'settings'   => 'hb_1_excerpt',
	) ) );


	$wp_customize->add_section( 'ignite_hotbutton_2_settings', array(
		'title'          => 'Hot Button 2',
		'priority'       => 44,
	) );

	$wp_customize->add_setting( 'hb_2_image', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hb_2_image', array(
		'label'   => 'Image Setting',
		'section' => 'ignite_hotbutton_2_settings',
		'settings'   => 'hb_2_image',
	) ) );

	$wp_customize->add_setting( 'hb_2_title', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'hb_2_title', array(
		'label'   => 'Title',
		'section' => 'ignite_hotbutton_2_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_2_link', array(
		'default'        => 'http://',
	) );

	$wp_customize->add_control( 'hb_2_link', array(
		'label'   => 'Link',
		'section' => 'ignite_hotbutton_2_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_2_excerpt', array(
	'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Textarea_Control( $wp_customize, 'hb_2_excerpt', array(
	'label'   => 'Excerpt (optional)',
	'section' => 'ignite_hotbutton_2_settings',
	'settings'   => 'hb_2_excerpt',
	) ) );

	$wp_customize->add_section( 'ignite_hotbutton_3_settings', array(
		'title'          => 'Hot Button 3',
		'priority'       => 44,
	) );

	$wp_customize->add_setting( 'hb_3_image', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hb_3_image', array(
		'label'   => 'Image Setting',
		'section' => 'ignite_hotbutton_3_settings',
		'settings'   => 'hb_3_image',
	) ) );

	$wp_customize->add_setting( 'hb_3_title', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'hb_3_title', array(
		'label'   => 'Title',
		'section' => 'ignite_hotbutton_3_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_3_link', array(
		'default'        => 'http://',
	) );

	$wp_customize->add_control( 'hb_3_link', array(
		'label'   => 'Link',
		'section' => 'ignite_hotbutton_3_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_3_excerpt', array(
	'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Textarea_Control( $wp_customize, 'hb_3_excerpt', array(
	'label'   => 'Excerpt (optional)',
	'section' => 'ignite_hotbutton_3_settings',
	'settings'   => 'hb_3_excerpt',
	) ) );

	$wp_customize->add_section( 'ignite_hotbutton_4_settings', array(
		'title'          => 'Hot Button 4',
		'priority'       => 44,
	) );

	$wp_customize->add_setting( 'hb_4_image', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hb_4_image', array(
		'label'   => 'Image Setting',
		'section' => 'ignite_hotbutton_4_settings',
		'settings'   => 'hb_4_image',
	) ) );

	$wp_customize->add_setting( 'hb_4_title', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'hb_4_title', array(
		'label'   => 'Title',
		'section' => 'ignite_hotbutton_4_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_4_link', array(
		'default'        => 'http://',
	) );

	$wp_customize->add_control( 'hb_4_link', array(
		'label'   => 'Link',
		'section' => 'ignite_hotbutton_4_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_4_excerpt', array(
	'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Textarea_Control( $wp_customize, 'hb_4_excerpt', array(
	'label'   => 'Excerpt (optional)',
	'section' => 'ignite_hotbutton_4_settings',
	'settings'   => 'hb_4_excerpt',
	) ) );

	$wp_customize->add_section( 'ignite_hotbutton_5_settings', array(
		'title'          => 'Hot Button 5',
		'priority'       => 44,
	) );

	$wp_customize->add_setting( 'hb_5_image', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hb_5_image', array(
		'label'   => 'Image Setting',
		'section' => 'ignite_hotbutton_5_settings',
		'settings'   => 'hb_5_image',
	) ) );

	$wp_customize->add_setting( 'hb_5_title', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'hb_5_title', array(
		'label'   => 'Title',
		'section' => 'ignite_hotbutton_5_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_5_link', array(
		'default'        => 'http://',
	) );

	$wp_customize->add_control( 'hb_5_link', array(
		'label'   => 'Link',
		'section' => 'ignite_hotbutton_5_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hb_5_excerpt', array(
	'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Textarea_Control( $wp_customize, 'hb_5_excerpt', array(
	'label'   => 'Excerpt (optional)',
	'section' => 'ignite_hotbutton_5_settings',
	'settings'   => 'hb_5_excerpt',
	) ) );

	$wp_customize->add_section( 'ignite_slider_settings', array(
		'title'          => 'Slider (Homepage)',
		'description'	 => 'Manage the main slider on the homepage.',
		'priority'       => 43,
	) );

	$wp_customize->add_setting( 'small_slider', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'small_slider', array(
		'label'   => 'Small slider?',
		'section' => 'ignite_slider_settings',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'slide_page', array(
		'default'        => '',
	) );

	$slide_categories = get_terms("slide-page"); 
			foreach ( $slide_categories as $cat ) {
			$slides[$cat->slug] = $cat->name;
			 };

	$wp_customize->add_control( 'slide_page', array(
	'label'   => 'Slide category',
	'section' => 'ignite_slider_settings',
	'type'    => 'select',
	'choices'    => $slides,
	) );

	$wp_customize->add_setting( 'slide_feature_static', array(
	'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Textarea_Control( $wp_customize, 'slide_feature_static', array(
	'label'   => 'Feature content (for use with small slider)',
	'section' => 'ignite_slider_settings',
	'settings'   => 'slide_feature_static',
	) ) );

	$wp_customize->add_section( 'ignite_social_settings', array(
		'title'          => 'Social Settings',
		'description'	 => 'Where should the social links be shown?',
		'priority'       => 42,
	) );

	$wp_customize->add_setting( 'social_config', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Social_Config( $wp_customize, 'social_config', array(
	'section' => 'ignite_social_settings',
	'settings'   => 'social_config',
	) ) );

	$wp_customize->add_setting( 'show_social_header', array(
		'default'        => 1,
	) );

	$wp_customize->add_control( 'show_social_header', array(
		'label'   => 'Show social links in header?',
		'section' => 'ignite_social_settings',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'header_social_position', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'header_social_position', array(
	'label'   => 'Social Position (Header)',
	'section' => 'ignite_social_settings',
	'type'    => 'select',
	'choices'    => array(
		'top' => 'top',
		'middle' => 'middle',
		'bottom' => 'bottom',
		),
	) );

	$wp_customize->add_setting( 'show_social_footer', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'show_social_footer', array(
		'label'   => 'Show social links in footer?',
		'section' => 'ignite_social_settings',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'footer_social_position', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'footer_social_position', array(
	'label'   => 'Social Position (Footer)',
	'section' => 'ignite_social_settings',
	'type'    => 'select',
	'choices'    => array(
		'top' => 'top',
		'bottom' => 'bottom',
		),
	) );

	$wp_customize->add_section( 'ignite_tweet_ticker', array(
		'title'          => 'Tweet Ticker',
		'description'	 => 'Show scrolling status updates on the homepage',
		'priority'       => 45,
	) );

	$wp_customize->add_setting( 'show_tweet_ticker', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'show_tweet_ticker', array(
		'label'   => 'Show tweet ticker on homepage?',
		'section' => 'ignite_tweet_ticker',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'tweet_ticker_user', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'tweet_ticker_user', array(
		'label'   => 'Twitter user (without @)',
		'section' => 'ignite_tweet_ticker',
		'type'    => 'text',
	) );

	$wp_customize->add_section( 'ignite_custom_logo', array(
		'title'          => 'Logo',
		'description'	 => 'Display a custom logo?',
		'priority'       => 25,
	) );

	$wp_customize->add_setting( 'custom_logo', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'custom_logo', array(
		'label'   => 'Custom logo',
		'section' => 'ignite_custom_logo',
		'settings'   => 'custom_logo',
	) ) );

	$wp_customize->add_setting( 'link_color', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
	'label'   => 'Link Color',
	'section' => 'colors',
	'settings'   => 'link_color',
) ) );

	$wp_customize->add_setting( 'header_nav_x', array(
		'default'        => 'left',
	) );

	$wp_customize->add_control( 'header_nav_x', array(
	'label'   => 'Navigation position (X-axis)',
	'section' => 'nav',
	'type'    => 'select',
	'choices'    => array(
		'left' => 'left',
		'center' => 'center',
		'right' => 'right',
		),
	) );

	$wp_customize->add_setting( 'header_nav_y', array(
		'default'        => 'bottom',
	) );

	$wp_customize->add_control( 'header_nav_y', array(
	'label'   => 'Navigation position (Y-axis)',
	'section' => 'nav',
	'type'    => 'select',
	'choices'    => array(
		'top' => 'top',
		'middle' => 'middle',
		'bottom' => 'bottom',
		),
	) );

	$wp_customize->add_setting( 'menu_config', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Menu_Config( $wp_customize, 'menu_config', array(
	'section' => 'nav',
	'settings'   => 'menu_config',
	) ) );

	$wp_customize->add_section( 'ignite_footer', array(
		'title'          => 'Footer',
		'priority'       => 105,
	) );

	$wp_customize->add_setting( 'footer_text', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'footer_text', array(
		'label'   => 'Footer Tagline',
		'section' => 'ignite_footer',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'widget_config_footer', array(
		'default'        => '',
	) );

	$wp_customize->add_control( new WP_Customize_Widget_Config( $wp_customize, 'widget_config_footer', array(
	'section' => 'ignite_footer',
	'settings'   => 'widget_config_footer',
	) ) );

	$wp_customize->add_section( 'ignite_font_settings', array(
		'title'          => 'Font Settings',
		'description'	 => 'Which font style would you like to use?',
		'priority'       => 48,
	) );

	$wp_customize->add_setting( 'body-font', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'body-font', array(
	'label'   => 'Body font',
	'section' => 'ignite_font_settings',
	'type'    => 'select',
	'choices'    => array(
		'Arial' => 'Arial',
		'Open Sans' => 'Open Sans',
		'Georgia' => 'Georgia',
		),
	) );

	$wp_customize->add_section( 'ignite_address', array(
		'title'          => 'Address',
		'priority'       => 105,
	) );

	$wp_customize->add_setting( 'address', array(
		'default'        => '',
	) );

	$wp_customize->add_control( 'address', array(
		'label'   => 'Address',
		'section' => 'ignite_address',
		'type'    => 'text',
	) );

}

function grove_insert_css() {

	echo '<style type="text/css">';
	echo 'body{background-image:url('.get_theme_mod( 'background_image' ).'); background-position:top '.get_theme_mod( 'background_position_x' ).'; background-repeat:'.get_theme_mod( 'background_repeat' ).'; background-color:#'.get_theme_mod('background_color').'; font-family:'.get_theme_mod( 'body-font' ).' !important}';
	echo 'a, .follow-button{color:'.get_theme_mod('link_color').'}';
	echo 'a.button{background:'.get_theme_mod('link_color').';}';
	echo '.wooslider-control-paging li a.wooslider-active{background:'.get_theme_mod('link_color').';}';
	echo '.wooslider-control-paging li a:hover{background:'.get_theme_mod('link_color').'; opacity:0.5}';
	echo '#masthead{color:#'.get_header_textcolor().'}';
	if (get_theme_mod('header_nav_y') == 'top') echo '#masthead .site-navigation{top:0;}#masthead{padding-top:60px;}';
	if (get_theme_mod('header_nav_y') == 'middle') echo '#masthead .site-navigation{top:50%; margin-top:-20px;}';
	if (get_theme_mod('header_nav_y') == 'bottom') echo '#masthead .site-navigation{bottom:0;}';
	if (get_theme_mod('header_nav_x') == 'left') echo '#masthead .site-navigation{left:0;}';
	if (get_theme_mod('header_nav_x') == 'center') echo '#masthead .site-navigation{left:50%;}';
	if (get_theme_mod('header_nav_x') == 'right') echo '#masthead .site-navigation{right:0;}';
	echo '</style>';
};

add_action('wp_head', 'grove_insert_css');

function wp_make_content() {
    global $wp_admin_bar, $wpdb;
    if ( !is_super_admin() || !is_admin_bar_showing() )
        return;
   
    $wp_admin_bar->add_menu( array( 'id' => 'make_content', 'title' => __( 'Make Content', 'textdomain' ), 'href' => '/wp-admin/admin.php?page=make-content' ) );

}
add_action( 'admin_bar_menu', 'wp_make_content', 1000 );
