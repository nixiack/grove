<?php get_header(); ?>

<div id="content" role="main">

    <?php
    $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
    ?>

    <h2><?php
	
	
	$sg_member_gravatar = sg_get_avatar_url(get_avatar( $curauth->user_email, 200 ));
		
		echo '<div class="sg_subscriber" style="float:right;"><div class="sg_subscriber_gravatar" style="background:url('.$sg_member_gravatar.') top center no-repeat;"></div></div>';
	
	echo $curauth->display_name; if($curauth->biz_name != '') { echo ' of <strong>'.$curauth->biz_name.'</strong>'; } ?></h2>
    <p>Website: <a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a></p>
    <?php if($curauth->user_description != '') { ?>
		
		<p>Profile:<br/><?php echo $curauth->user_description; ?></p>
        
    <?php } ?>
    
    
    <?php if($curauth->user_occupation != '') { ?>
		
		<p>Occupation: <?php echo $curauth->user_occupation; ?></p>
        
    <?php } ?>
    
    <?php if($curauth->user_address != '') { ?>
		
		<p>Address:<br/>
		<?php echo $curauth->user_address; ?><br />
		<?php if($curauth->user_county != '') { echo $curauth->user_address2.'<br />'; } ?>
		<?php echo $curauth->user_city; ?>, <?php echo $curauth->user_state; ?> <?php echo $curauth->user_zip; ?><br />
		<?php if($curauth->user_county != '') { echo $curauth->user_county.'<br />'; } ?>
		<?php echo $curauth->user_country; ?></p>
        
    <?php } ?>
    
    <?php if($curauth->user_phone != '') { ?>
		
		<p>Phone:<br/><?php echo $curauth->user_phone; ?></p>
        
    <?php } ?>
    
    <?php if($curauth->user_tw != '') { ?>
		
		<p>Twitter: <a href="http://twitter.com/<?php echo $curauth->user_tw; ?>" target="_blank">http://twitter.com/<?php echo $curauth->user_tw; ?></a></p>
        
    <?php } ?>
    
    <?php if($curauth->user_fb != '') { ?>
		
		<p>Facebook: <a href="<?php echo $curauth->user_fb; ?>" target="_blank"><?php echo $curauth->user_fb; ?></a></p>
        
    <?php } ?>
    

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>