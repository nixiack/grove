<?php if (get_option('show_tweet_ticker') AND get_option('tweet_ticker_user')) {

$transName = 'list_tweets';

if(false === ($twitterData = get_transient($transName) ) ){
     // require the twitter auth class
     require_once 'twitteroauth/twitteroauth.php';

      $twitterConnection = new TwitterOAuth(
	'Pmxb6TgS8g7e97Ht080eQ',	// Consumer Key
	'PzvJJumF5y3KOMjbsTiDyicEBI8i65MxbUwX5Wgea0',   	// Consumer secret
	'20116892-cLYQ0j6hlboeu5PZoZRSxHyeoWuqPXdmJeHHpy6Wc',       // Access token
	'MoJXm52SDjQolyRkPwa9G8gFek4c0nyFItUXJ1s0q8'    	// Access token secret
	);

     $twitterData = $twitterConnection->get(
	'statuses/user_timeline',
	  array(
	    'screen_name'     => get_option('tweet_ticker_user'),
	    'count'           => 5,
	    'exclude_replies' => false
	  )
	);

     if($twitterConnection->http_code != 200)
     {
          $twitterData = get_transient($transName);
     }

     set_transient($transName, $twitterData, 60 * 60);
    
};

$twitterData = get_transient( 'list_tweets' ); 

if ($twitterData){  ?>

<div class="tweets-container-outer">				 
<div class="tweets-container">
						<ul class="tweetscroll">
						<?php 



foreach($twitterData as $item){ ?>
<li class="tweet">
							<span class="tweet-body">
								<?php echo make_clickable_tweet($item->text); ?>
							</span>
							<span class="tweet-stamp">
								<?php echo human_time_diff( strtotime($item->created_at), current_time('timestamp') ) . ' ago';  ?>
							</span>
						</li>
		          
					<?php }	echo '</ul>'; } ?>

					<script>
	    				jQuery(document).ready(function(){
						jQuery("ul.tweetscroll").show().liScroll();
						});
						</script>

						<a class="follow-button" href="http://twitter.com/<?php echo get_option('tweet_ticker_user'); ?>"><i class="ss-icon ss-social">Twitter</i>Follow @<?php echo get_option('tweet_ticker_user'); ?></a>

						</div>
				</div>

					 <?php } 