<?php if (get_option('show_tweet_ticker') AND get_option('tweet_ticker_user')) {


// TWITTER API 1.1
$transName = 'list_tweetst';

if(false === ($twitterData = get_transient($transName) ) ){

     require_once 'twitteroauth/twitteroauth.php';

function getConnectionWithAccessToken($oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth('Pmxb6TgS8g7e97Ht080eQ', 'PzvJJumF5y3KOMjbsTiDyicEBI8i65MxbUwX5Wgea0', $oauth_token, $oauth_token_secret);
  return $connection;
}
 
$connection = getConnectionWithAccessToken("20116892-cLYQ0j6hlboeu5PZoZRSxHyeoWuqPXdmJeHHpy6Wc", "MoJXm52SDjQolyRkPwa9G8gFek4c0nyFItUXJ1s0q8");

 $twitterData = $connection->get(
	'statuses/user_timeline',
	  array(
	    'screen_name'     => get_option('tweet_ticker_user'),
	    'count'           => 5,
	    'exclude_replies' => false
	  )
	);

set_transient($transName, $twitterData, 60 * 60);

}

$twitterData = get_transient($transName);  ?>

<div class="tweets-container-outer">				 
	<div class="tweets-container"><?php 
	
		if ($twitterData){  ?>

			<ul class="tweetscroll"><?php 

				foreach($twitterData as $item){ ?>
					<li class="tweet">
						<span class="tweet-body">
							<?php echo make_clickable_tweet($item->text); ?>
						</span>
						<span class="tweet-stamp">
							<?php echo human_time_diff( strtotime($item->created_at), current_time('timestamp') ) . ' ago';  ?>
						</span>
					</li><?php 
				} ?>
                
        	</ul><?php 
			
		} ?>

		<script>
	    	jQuery(document).ready(function(){
				jQuery("ul.tweetscroll").show().liScroll();
			});
		</script>

		<a class="follow-button" href="http://twitter.com/<?php echo get_option('tweet_ticker_user'); ?>"><i class="ss-icon ss-social">Twitter</i>Follow @<?php echo get_option('tweet_ticker_user'); ?></a>

	</div>
</div>

<?php }  ?>