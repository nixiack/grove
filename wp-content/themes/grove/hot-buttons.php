<?php 
		$hb_show = array();
		
			if (get_theme_mod( 'hb_1_link' )!="http://" AND get_theme_mod( 'hb_1_link' )!="") { $hb_show[] = 1; }
			if (get_theme_mod( 'hb_2_link' )!="http://" AND get_theme_mod( 'hb_2_link' )!="") { $hb_show[] = 2; }
			if (get_theme_mod( 'hb_3_link' )!="http://" AND get_theme_mod( 'hb_3_link' )!="") { $hb_show[] = 3; }
			if (get_theme_mod( 'hb_4_link' )!="http://" AND get_theme_mod( 'hb_4_link' )!="") { $hb_show[] = 4; }
			if (get_theme_mod( 'hb_5_link' )!="http://" AND get_theme_mod( 'hb_5_link' )!="") { $hb_show[] = 5; }

		$count=count($hb_show);

		if ($count > 0) {
			echo '<div class="hotbuttons-outer"><div class="hotbuttons hotbuttons-'.$count.'">';
		}

		foreach ($hb_show as $number) {
			
			$title=get_theme_mod( 'hb_'.$number.'_title' );
			$link=get_theme_mod( 'hb_'.$number.'_link' );
			$image=get_theme_mod( 'hb_'.$number.'_image' );
			$excerpt=get_theme_mod( 'hb_'.$number.'_excerpt' );
			
			if ($title AND $image AND $excerpt) {

				echo '<a href="'.$link.'"><img src="'.$image.'"><strong>'.$title.'</strong><span>'.$excerpt.'</span></a>';

			} elseif ($title AND $image AND !$excerpt) {

				echo '<a href="'.$link.'"><img src="'.$image.'"><strong>'.$title.'</strong></a>';
			
			} elseif ($title AND !$image AND $excerpt) {

				echo '<a href="'.$link.'"><strong>'.$title.'</strong><span>'.$excerpt.'</span></a>';

			} elseif ($title AND !$image AND !$excerpt) {

				echo '<a href="'.$link.'"><strong>'.$title.'</strong></a>';

			}

		}

		if ($count > 0) {
			echo '</div></div>';
		}

 ?>