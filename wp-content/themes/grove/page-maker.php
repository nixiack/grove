<?php 

add_action('admin_menu', 'register_make_content');

function register_make_content() {
	add_submenu_page( 'Grove', 'Make Content', 'Make Content', 'manage_options', 'make-content', 'make_content_callback' ); 
}

function make_content_callback() {
	
	if ($_GET['type']!='event' AND $_GET['sidebar']) {

		if ($_GET['type'] == 'page') {
			$title = 'New page';
		} elseif ($_GET['type'] == 'post') {
			$title = 'New post';
		}
			
			$post = array(
				'post_title'	=> wp_strip_all_tags($title),
				'post_content'	=> '',
				'post_status'	=> 'draft',
				'post_type' 	=> $_GET['type']
			);

			if ($_GET['template']=='contact') { $post['post_content'] = "[contact-form][contact-field label='Name' type='name' required='1'/][contact-field label='Email' type='email' required='1'/][contact-field label='Website' type='url'/][contact-field label='Comment' type='textarea' required='1'/][/contact-form]"; };

			$post_id = wp_insert_post($post); 

			update_post_meta($post_id, '_ignite_hide_sidebar', $_GET['sidebar']);

			if ($_GET['template']=="contact") { update_post_meta( $post_id, '_wp_page_template', 'page-contact.php' ); };
			if ($_GET['template']=="blog") { update_post_meta( $post_id, '_wp_page_template', 'page-blog.php' ); };

			wp_redirect('/wp-admin/post.php?post='.$post_id.'&action=edit');

	} elseif ($_GET['type']=='event') {

		$post = array(
		   'post_title' => 'New event',
		   'post_status' => 'draft',
		);

		$post_id = tribe_create_event( $post );

		wp_redirect('/wp-admin/post.php?post='.$post_id.'&action=edit');

	}

	?>

	<style type="text/css">
	#wpbody-content{}
	.picker{width:48%; float: left;}
	.types{position: relative;  float: left; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; padding: 30px 20px 40px 0; }
	.types li, .types span{display: inline; float:left;}
	.types span{font-size: 24px; font-weight: bold;}
	.types li a{display: none; position: relative; margin: 0; font-size: 16px !important; font-weight: bold;}
	.types ul li{float:none; }
	.types ul{top:10px; right:-160px; position: absolute; width:160px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;}
	.types ul.open{border: 1px solid #dedede; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; background: #fff; padding: 10px 15px; -webkit-box-shadow: inset 0 1px 0 #fff, inset 0 0 20px rgba(0,0,0,0.05), 0 1px 2px rgba( 0,0,0,0.1 ); -moz-box-shadow: inset 0 1px 0 #fff, inset 0 0 20px rgba(0,0,0,0.05), 0 1px 2px rgba( 0,0,0,0.1 );	box-shadow: inset 0 1px 0 #fff, inset 0 0 20px rgba(0,0,0,0.05), 0 1px 2px rgba( 0,0,0,0.1 );}
	.types ul li a.button-secondary{display: block; height: auto; padding:4px 30px 4px 10px; }
	.types ul li a.button-secondary:after{content:"."; text-indent: -9999px; width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid #00a99d; position: absolute; top:10px; right:10px;}
	.types .open{display: block; text-decoration: none; line-height: 20px;}
	.preview{width:48%; background: #eee; border-radius: 4px; position: absolute; top:20px; bottom:30px; right:20px; }
	.preview-pane{text-align: center; height: 100%; background-repeat: no-repeat; background-size: 100% auto;  border-radius: 3px; border:1px solid #ccc;}
	.preview-pane span{position: absolute; top:50%; padding: 0 20%; text-align: center; font-size: 18px; color: #999; display: block; line-height: 25px; margin-top:-25px; width:100%; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;}
	.layouts{overflow: hidden; display: none; clear:both;}
	.layouts.selected{display: block;}
	.layouts li{margin:0;}
		.layouts li a{width:180px; height: 125px; text-align: center; border:1px solid #eee; display: block; float: left; margin: 0 20px 25px 0; padding: 125px 0 0 0; font-weight: bold; border-radius:3px; text-decoration: none; background-size: 100% auto; line-height: 40px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;}
		.layouts li a.selected{border-color: #00a99d; border-width:4px;}
		.make-content-submit{text-align:center; display:block; position:absolute; top:-60px; width:100%; font-size:18px !important; font-weight:bold; text-decoration:none; display: none; height: 40px !important; line-height: 40px !important;}
		.make-content-submit:hover{color:#fff;}



	</style>

	<script type="text/javascript">
		jQuery(document).ready(function() {

			jQuery('.types a.button-secondary').live("click", function() {
			jQuery('.types ul').addClass("open");
		  	jQuery('.types a').removeClass("button-secondary").addClass("open");
		  	return false;
		  })

			jQuery('.types a.open').live("click", function() {
		  	var _href = jQuery(this).attr("href").replace('#', '');
		  	jQuery('.types ul').removeClass("open");
		  	jQuery('.types a').removeClass("open");
		  	jQuery(this).addClass("button-secondary");
		  	jQuery(".layouts").hide();
		  	jQuery('.preview-pane span').text('Now pick a template from the grid.');
		  	jQuery(".layouts." + _href).fadeIn('fast');
		  	return false;
		  })

		  jQuery('.layouts a').each(function(index) {
		  	var _thumb = jQuery(this).attr("rel");
    		jQuery(this).css("background-image", 'url(/wp-content/mu-plugins/inc/images/' + _thumb + ')');
			});

		  jQuery('.layouts a').click(function() {
		  	var _href = jQuery(this).attr("href");
		  	var _preview = jQuery(this).attr("rel");
		  	jQuery('.layouts a').removeClass("selected");
		  	jQuery(this).addClass("selected");
		  	jQuery('.preview-pane span').hide();
		  	jQuery('.preview').stop().css("background-color", "#fff").css("top", "85px").animate({ backgroundColor: "#eee"}, 150);
		  	jQuery('.preview-pane').css("background-image", 'url(/wp-content/mu-plugins/inc/images/' + _preview + ')');
		  	jQuery(".make-content-submit").attr("href", _href).fadeIn('fast');
		  	return false;
		  })
		});
	</script>

<div class="picker">

<div class="types">
	<span>I want to make a new</span>
	<ul>
		<li><a href="#page" class="button-secondary">page</a></li>
		<li><a href="#post">blog post</a></li>
		<li><a href="#event">event</a></li>
		<li><a href="#gallery">photo gallery</a></li>
		<li><a href="#archive">blog listing</a></li>
		<li><a href="#contact">contact page</a></li>
	</ul>
</div>

<ul class="layouts page selected">
	<li><a href="admin.php?page=make-content&type=page&sidebar=right&noheader=true" rel="page-sidebar-right.png">Sidebar on Right</a></li>
	<li><a href="admin.php?page=make-content&type=page&sidebar=left&noheader=true" rel="page-sidebar-left.png">Sidebar on Left</a></li>
	<li><a href="admin.php?page=make-content&type=page&sidebar=hide&noheader=true" rel="page-sidebar-none.png">No sidebar</a></li>
</ul>

<ul class="layouts post">
	<li><a href="admin.php?page=make-content&type=post&sidebar=right&noheader=true" rel="post-sidebar-right.png">Sidebar on Right</a></li>
	<li><a href="admin.php?page=make-content&type=post&sidebar=left&noheader=true" rel="post-sidebar-left.png">Sidebar on Left</a></li>
	<li><a href="admin.php?page=make-content&type=post&sidebar=hide&noheader=true" rel="post-sidebar-none.png">No sidebar</a></li>
</ul>

<ul class="layouts gallery">
	<li><a href="admin.php?page=make-content&type=post&sidebar=right&noheader=true" rel="gallery-sidebar-right.png">Sidebar on Right</a></li>
	<li><a href="admin.php?page=make-content&type=post&sidebar=left&noheader=true" rel="gallery-sidebar-left.png">Sidebar on Left</a></li>
	<li><a href="admin.php?page=make-content&type=post&sidebar=hide&noheader=true" rel="gallery-sidebar-none.png">No sidebar</a></li>
</ul>

<ul class="layouts event">
	<li><a href="admin.php?page=make-content&type=event&sidebar=right&noheader=true" rel="event-sidebar-right.png">Sidebar on Right</a></li>
	<li><a href="admin.php?page=make-content&type=event&sidebar=left&noheader=true" rel="event-sidebar-left.png">Sidebar on Left</a></li>
	<li><a href="admin.php?page=make-content&type=event&sidebar=hide&noheader=true" rel="event-sidebar-none.png">No sidebar</a></li>
</ul>

<ul class="layouts archive">
	<li><a href="admin.php?page=make-content&type=page&sidebar=right&template=blog&noheader=true" rel="archive-sidebar-right.png">Sidebar on Right</a></li>
	<li><a href="admin.php?page=make-content&type=page&sidebar=left&template=blog&noheader=true"  rel="archive-sidebar-left.png">Sidebar on Left</a></li>
</ul>

<ul class="layouts contact">
	<li><a href="admin.php?page=make-content&type=page&sidebar=right&template=contact&noheader=true" rel="contact-sidebar-right.png">Map/Sidebar on Right</a></li>
	<li><a href="admin.php?page=make-content&type=page&sidebar=left&template=contact&noheader=true" rel="contact-sidebar-left.png">Map/Sidebar on Left</a></li>
</ul>

</div>

<div class="preview">
	<div class="preview-pane"><span>Use the dropdown to select which type of content you want to make.</span></div>
	<a href="#" class="make-content-submit button-primary">Create it!</a>
</div>

	<?php } 
	
	