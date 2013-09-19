<?php
/*
Plugin Name: Subscriber Gallery 2.0 Full Profile
Plugin URI: http://Ignite360.com
Description: Custom registration form and Subscriber Gallery.
Version: 2.0
Author: valiik (Valik Rudd)
Author URI: http://flynewmedia.com/
Donate link: http://bit.ly/A3SfBN
*/

define('SG_VERSION', '1.0');
define('SG_DIR', dirname(__FILE__));

register_activation_hook(__FILE__,'sg_install'); 

//redirect on plugin activation
add_action('admin_init', 'sg_redirect');
function sg_redirect() {
    if (get_option('sg_do_activation_redirect') == true) {
        delete_option('sg_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect("admin.php?page=subscriber-gallery/subscriber-gallery.php");
        }
    }
}

register_deactivation_hook( __FILE__, 'sg_remove' );




function cg_curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}


function sg_show_authors_without_posts($sg_template) {
		global $wp_query;
		if( !is_author() && get_query_var('author') && (0 == $wp_query->posts->post) ) {
			// debug
			// echo 'Overwrite default 404 template...';
			return get_author_template();
		}
		return $sg_template;
	}
	
add_filter('404_template', 'sg_show_authors_without_posts');
	
	
// add jquery & custom scripts
function sg_adds_to_the_head() { 
    wp_enqueue_script("jquery");
	
	wp_register_script( 'add-cg-custom-js', plugins_url( 'cg.js' , __FILE__ ), '', null,''  ); // Register our second custom script for CG
	wp_register_style( 'add-cg-css', plugins_url( 'cg.css' , __FILE__ ),'','', 'screen' ); // Register the CG Stylsheet
	
    wp_enqueue_script( 'add-cg-custom-js' );
    wp_enqueue_style( 'add-cg-css' );
	
}
add_action( 'wp_enqueue_scripts', 'sg_adds_to_the_head' );


// redirects back to custom registration page after user registered
function sg_possibly_redirect(){
  global $pagenow;
  if( 'wp-login.php' == $pagenow ) {
	if(isset($_GET['checkemail']) && $_GET['checkemail']=='registered') {
		
		if(get_option('cg_tyurl') != '') {
			$cg_ty_url = get_option('cg_tyurl');
		} else {
			$cg_ty_url = home_url().'/register/?cg-reg=1';
		}
		
		wp_redirect( $cg_ty_url );
    	exit();
	} else {
		return;	
	}
  }
}
add_action('init','sg_possibly_redirect');


// innitiate onload javascript function
function sg_init_function() {
    echo '<script>sg_initing();</script>';
}
add_action('wp_footer', 'sg_init_function');


// gravatar URL from gravatar
function sg_get_avatar_url($sg_get_avatar){
    preg_match("/src='(.*?)'/i", $sg_get_avatar, $sg_matches);
    return $sg_matches[1];
}

// [subscriber_gallery sortby="registered"] (Sort by 'ID', 'login', 'nicename', 'email', 'url', 'registered', 'display_name', or 'post_count'.)
function sg_subscriber_gallery_func( $atts ) {
	
	extract( shortcode_atts( array(
		'sortby' => 'registered'
	), $atts ) );
	
    $blogusers = get_users('orderby={$sortby}&role=subscriber');

    foreach ($blogusers as $user) {
		
		$sg_member_gravatar = sg_get_avatar_url(get_avatar( $user->user_email, 200 ));
		
		if (  get_user_meta( $user->ID, 'biz_name', true ) ) {
			$sg_title = get_user_meta( $user->ID, 'biz_name', true );
		} else {
			$sg_title = $user->display_name;
		}
     	
		$sg_members .= '<div class="sg_subscriber"><a href="'.get_author_posts_url( $user->ID ).'"><div class="sg_subscriber_gravatar" style="background:url('.$sg_member_gravatar.') top center no-repeat;"></div><div class="sg_subscriber_title">' . $sg_title  . '</div></a></div>';
    }
	
	$sg_members .= '<div style="clear:both;"></div>';

	return $sg_members;
}
add_shortcode( 'subscriber_gallery', 'sg_subscriber_gallery_func' );


///////////////////////////
// BUSINESS NAME FIELD

add_action( 'show_user_profile', 'sg_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'sg_show_extra_profile_fields' );

function sg_show_extra_profile_fields( $user ) { ?>

	<h3>Business Information</h3>

	<table class="form-table">

		<tr>
			<th><label for="biz_name">Business Name</label></th>

			<td>
				<input type="text" name="biz_name" id="biz_name" value="<?php echo esc_attr( get_the_author_meta( 'biz_name', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please add your Business name.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_occupation">Occupation</label></th>

			<td>
				<input type="text" name="user_occupation" id="user_occupation" value="<?php echo esc_attr( get_the_author_meta( 'user_occupation', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please add your occupation.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_address">Address</label></th>

			<td>
				<input type="text" name="user_address" id="user_address" value="<?php echo esc_attr( get_the_author_meta( 'user_address', $user->ID ) ); ?>" class="regular-text" /><br />
				<input type="text" name="user_address2" id="user_address2" value="<?php echo esc_attr( get_the_author_meta( 'user_address2', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please add your address.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_city">City</label></th>

			<td>
				<input type="text" name="user_city" id="user_city" value="<?php echo esc_attr( get_the_author_meta( 'user_city', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please add your city.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_state">State</label></th>

			<td>
				<select name="user_state" id="user_state" class="regular-text">
                <?php if(esc_attr( get_the_author_meta( 'user_state', $user->ID ) ) != '') {
					echo '<option value="'.esc_attr( get_the_author_meta( 'user_state', $user->ID ) ).'" selected>'.esc_attr( get_the_author_meta( 'user_state', $user->ID ) ).'</option>';
				} ?>
				<option value="AL">Alabama</option>
	            <option value="AK">Alaska</option>
	            <option value="AZ">Arizona</option>
	            <option value="AR">Arkansas</option>
	            <option value="CA">California</option>
	            <option value="CO">Colorado</option>
	            <option value="CT">Connecticut</option>
	            <option value="DE">Delaware</option>
	            <option value="DC">District of Columbia</option>
	            <option value="FL">Florida</option>
	            <option value="GA">Georgia</option>
	            <option value="HI">Hawaii</option>
	            <option value="ID">Idaho</option>
	            <option value="IL">Illinois</option>
	            <option value="IN">Indiana</option>
	            <option value="IA">Iowa</option>
	            <option value="KS">Kansas</option>
	            <option value="KY">Kentucky</option>
	            <option value="LA">Louisiana</option>
	            <option value="ME">Maine</option>
	            <option value="MD">Maryland</option>
	            <option value="MA">Massachusetts</option>
	            <option value="MI">Michigan</option>
	            <option value="MN">Minnesota</option>
	            <option value="MS">Mississippi</option>
	            <option value="MO">Missouri</option>
	            <option value="MT">Montana</option>
	            <option value="NE">Nebraska</option>
	            <option value="NV">Nevada</option>
	            <option value="NH">New Hampshire</option>
	            <option value="NJ">New Jersey</option>
	            <option value="NM">New Mexico</option>
	            <option value="NY">New York</option>
	            <option value="NC">North Carolina</option>
	            <option value="ND">North Dakota</option>
	            <option value="OH">Ohio</option>
	            <option value="OK">Oklahoma</option>
	            <option value="OR">Oregon</option>
	            <option value="PA">Pennsylvania</option>
	            <option value="RI">Rhode Island</option>
	            <option value="SC">South Carolina</option>
	            <option value="SD">South Dakota</option>
	            <option value="TN">Tennessee</option>
	            <option value="TX">Texas</option>
	            <option value="UT">Utah</option>
	            <option value="VT">Vermont</option>
	            <option value="VA">Virginia</option>
	            <option value="WA">Washington</option>
	            <option value="WV">West Virginia</option>
	            <option value="WI">Wisconsin</option>
	            <option value="WY">Wyoming</option>
			</select><br />
				<span class="description">Please select your state.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_zip">Zip</label></th>

			<td>
				<input type="text" name="user_zip" id="user_zip" value="<?php echo esc_attr( get_the_author_meta( 'user_zip', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please add your zip.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_county">County</label></th>

			<td>
				<input type="text" name="user_county" id="user_county" value="<?php echo esc_attr( get_the_author_meta( 'user_county', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please add your county.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_country">Country</label></th>

			<td>
			<select name="user_country" id="user_country" class="regular-text">
                <?php if(esc_attr( get_the_author_meta( 'user_country', $user->ID ) ) != '') {
					echo '<option value="'.esc_attr( get_the_author_meta( 'user_country', $user->ID ) ).'" title="'.esc_attr( get_the_author_meta( 'user_country', $user->ID ) ).'" selected>'.esc_attr( get_the_author_meta( 'user_country', $user->ID ) ).'</option>';
				} else {
					echo '<option value="United States" title="United States" selected="selected">United States</option>';
				}?>
	            <option value="Afghanistan" title="Afghanistan">Afghanistan</option>
	            <option value="Aland Islands" title="Aland Islands">Aland Islands</option>
	            <option value="Albania" title="Albania">Albania</option>
	            <option value="Algeria" title="Algeria">Algeria</option>
	            <option value="American Samoa" title="American Samoa">American Samoa</option>
	            <option value="Andorra" title="Andorra">Andorra</option>
	            <option value="Angola" title="Angola">Angola</option>
	            <option value="Anguilla" title="Anguilla">Anguilla</option>
	            <option value="Antarctica" title="Antarctica">Antarctica</option>
	            <option value="Antigua and Barbuda" title="Antigua and Barbuda">Antigua and Barbuda</option>
	            <option value="Argentina" title="Argentina">Argentina</option>
	            <option value="Armenia" title="Armenia">Armenia</option>
	            <option value="Aruba" title="Aruba">Aruba</option>
	            <option value="Australia" title="Australia">Australia</option>
	            <option value="Austria" title="Austria">Austria</option>
	            <option value="Azerbaijan" title="Azerbaijan">Azerbaijan</option>
	            <option value="Bahamas" title="Bahamas">Bahamas</option>
	            <option value="Bahrain" title="Bahrain">Bahrain</option>
	            <option value="Bangladesh" title="Bangladesh">Bangladesh</option>
	            <option value="Barbados" title="Barbados">Barbados</option>
	            <option value="Belarus" title="Belarus">Belarus</option>
	            <option value="Belgium" title="Belgium">Belgium</option>
	            <option value="Belize" title="Belize">Belize</option>
	            <option value="Benin" title="Benin">Benin</option>
	            <option value="Bermuda" title="Bermuda">Bermuda</option>
	            <option value="Bhutan" title="Bhutan">Bhutan</option>
	            <option value="Bolivia, Plurinational State of" title="Bolivia, Plurinational State of">Bolivia, Plurinational State of</option>
	            <option value="Bonaire, Sint Eustatius and Saba" title="Bonaire, Sint Eustatius and Saba">Bonaire, Sint Eustatius and Saba</option>
	            <option value="Bosnia and Herzegovina" title="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
	            <option value="Botswana" title="Botswana">Botswana</option>
	            <option value="Bouvet Island" title="Bouvet Island">Bouvet Island</option>
	            <option value="Brazil" title="Brazil">Brazil</option>
	            <option value="British Indian Ocean Territory" title="British Indian Ocean Territory">British Indian Ocean Territory</option>
	            <option value="Brunei Darussalam" title="Brunei Darussalam">Brunei Darussalam</option>
	            <option value="Bulgaria" title="Bulgaria">Bulgaria</option>
	            <option value="Burkina Faso" title="Burkina Faso">Burkina Faso</option>
	            <option value="Burundi" title="Burundi">Burundi</option>
	            <option value="Cambodia" title="Cambodia">Cambodia</option>
	            <option value="Cameroon" title="Cameroon">Cameroon</option>
	            <option value="Canada" title="Canada">Canada</option>
	            <option value="Cape Verde" title="Cape Verde">Cape Verde</option>
	            <option value="Cayman Islands" title="Cayman Islands">Cayman Islands</option>
	            <option value="Central African Republic" title="Central African Republic">Central African Republic</option>
	            <option value="Chad" title="Chad">Chad</option>
	            <option value="Chile" title="Chile">Chile</option>
	            <option value="China" title="China">China</option>
	            <option value="Christmas Island" title="Christmas Island">Christmas Island</option>
	            <option value="Cocos (Keeling) Islands" title="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
	            <option value="Colombia" title="Colombia">Colombia</option>
	            <option value="Comoros" title="Comoros">Comoros</option>
	            <option value="Congo" title="Congo">Congo</option>
	            <option value="Congo, the Democratic Republic of the" title="Congo, the Democratic Republic of the">Congo, the Democratic Republic of the</option>
	            <option value="Cook Islands" title="Cook Islands">Cook Islands</option>
	            <option value="Costa Rica" title="Costa Rica">Costa Rica</option>
	            <option value="Cote d'Ivoire" title="Cote d'Ivoire">Cote d'Ivoire</option>
	            <option value="Croatia" title="Croatia">Croatia</option>
	            <option value="Cuba" title="Cuba">Cuba</option>
	            <option value="Curacao" title="Curacao">Curacao</option>
	            <option value="Cyprus" title="Cyprus">Cyprus</option>
	            <option value="Czech Republic" title="Czech Republic">Czech Republic</option>
	            <option value="Denmark" title="Denmark">Denmark</option>
	            <option value="Djibouti" title="Djibouti">Djibouti</option>
	            <option value="Dominica" title="Dominica">Dominica</option>
	            <option value="Dominican Republic" title="Dominican Republic">Dominican Republic</option>
	            <option value="Ecuador" title="Ecuador">Ecuador</option>
	            <option value="Egypt" title="Egypt">Egypt</option>
	            <option value="El Salvador" title="El Salvador">El Salvador</option>
	            <option value="Equatorial Guinea" title="Equatorial Guinea">Equatorial Guinea</option>
	            <option value="Eritrea" title="Eritrea">Eritrea</option>
	            <option value="Estonia" title="Estonia">Estonia</option>
	            <option value="Ethiopia" title="Ethiopia">Ethiopia</option>
	            <option value="Falkland Islands (Malvinas)" title="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
	            <option value="Faroe Islands" title="Faroe Islands">Faroe Islands</option>
	            <option value="Fiji" title="Fiji">Fiji</option>
	            <option value="Finland" title="Finland">Finland</option>
	            <option value="France" title="France">France</option>
	            <option value="French Guiana" title="French Guiana">French Guiana</option>
	            <option value="French Polynesia" title="French Polynesia">French Polynesia</option>
	            <option value="French Southern Territories" title="French Southern Territories">French Southern Territories</option>
	            <option value="Gabon" title="Gabon">Gabon</option>
	            <option value="Gambia" title="Gambia">Gambia</option>
	            <option value="Georgia" title="Georgia">Georgia</option>
	            <option value="Germany" title="Germany">Germany</option>
	            <option value="Ghana" title="Ghana">Ghana</option>
	            <option value="Gibraltar" title="Gibraltar">Gibraltar</option>
	            <option value="Greece" title="Greece">Greece</option>
	            <option value="Greenland" title="Greenland">Greenland</option>
	            <option value="Grenada" title="Grenada">Grenada</option>
	            <option value="Guadeloupe" title="Guadeloupe">Guadeloupe</option>
	            <option value="Guam" title="Guam">Guam</option>
	            <option value="Guatemala" title="Guatemala">Guatemala</option>
	            <option value="Guernsey" title="Guernsey">Guernsey</option>
	            <option value="Guinea" title="Guinea">Guinea</option>
	            <option value="Guinea-Bissau" title="Guinea-Bissau">Guinea-Bissau</option>
	            <option value="Guyana" title="Guyana">Guyana</option>
	            <option value="Haiti" title="Haiti">Haiti</option>
	            <option value="Heard Island and McDonald Islands" title="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>
	            <option value="Holy See (Vatican City State)" title="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
	            <option value="Honduras" title="Honduras">Honduras</option>
	            <option value="Hong Kong" title="Hong Kong">Hong Kong</option>
	            <option value="Hungary" title="Hungary">Hungary</option>
	            <option value="Iceland" title="Iceland">Iceland</option>
	            <option value="India" title="India">India</option>
	            <option value="Indonesia" title="Indonesia">Indonesia</option>
	            <option value="Iran, Islamic Republic of" title="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
	            <option value="Iraq" title="Iraq">Iraq</option>
	            <option value="Ireland" title="Ireland">Ireland</option>
	            <option value="Isle of Man" title="Isle of Man">Isle of Man</option>
	            <option value="Israel" title="Israel">Israel</option>
	            <option value="Italy" title="Italy">Italy</option>
	            <option value="Jamaica" title="Jamaica">Jamaica</option>
	            <option value="Japan" title="Japan">Japan</option>
	            <option value="Jersey" title="Jersey">Jersey</option>
	            <option value="Jordan" title="Jordan">Jordan</option>
	            <option value="Kazakhstan" title="Kazakhstan">Kazakhstan</option>
	            <option value="Kenya" title="Kenya">Kenya</option>
	            <option value="Kiribati" title="Kiribati">Kiribati</option>
	            <option value="Korea, Democratic People's Republic of" title="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
	            <option value="Korea, Republic of" title="Korea, Republic of">Korea, Republic of</option>
	            <option value="Kuwait" title="Kuwait">Kuwait</option>
	            <option value="Kyrgyzstan" title="Kyrgyzstan">Kyrgyzstan</option>
	            <option value="Lao People's Democratic Republic" title="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
	            <option value="Latvia" title="Latvia">Latvia</option>
	            <option value="Lebanon" title="Lebanon">Lebanon</option>
	            <option value="Lesotho" title="Lesotho">Lesotho</option>
	            <option value="Liberia" title="Liberia">Liberia</option>
	            <option value="Libya" title="Libya">Libya</option>
	            <option value="Liechtenstein" title="Liechtenstein">Liechtenstein</option>
	            <option value="Lithuania" title="Lithuania">Lithuania</option>
	            <option value="Luxembourg" title="Luxembourg">Luxembourg</option>
	            <option value="Macao" title="Macao">Macao</option>
	            <option value="Macedonia, the former Yugoslav Republic of" title="Macedonia, the former Yugoslav Republic of">Macedonia, the former Yugoslav Republic of</option>
	            <option value="Madagascar" title="Madagascar">Madagascar</option>
	            <option value="Malawi" title="Malawi">Malawi</option>
	            <option value="Malaysia" title="Malaysia">Malaysia</option>
	            <option value="Maldives" title="Maldives">Maldives</option>
	            <option value="Mali" title="Mali">Mali</option>
	            <option value="Malta" title="Malta">Malta</option>
	            <option value="Marshall Islands" title="Marshall Islands">Marshall Islands</option>
	            <option value="Martinique" title="Martinique">Martinique</option>
	            <option value="Mauritania" title="Mauritania">Mauritania</option>
	            <option value="Mauritius" title="Mauritius">Mauritius</option>
	            <option value="Mayotte" title="Mayotte">Mayotte</option>
	            <option value="Mexico" title="Mexico">Mexico</option>
	            <option value="Micronesia, Federated States of" title="Micronesia, Federated States of">Micronesia, Federated States of</option>
	            <option value="Moldova, Republic of" title="Moldova, Republic of">Moldova, Republic of</option>
	            <option value="Monaco" title="Monaco">Monaco</option>
	            <option value="Mongolia" title="Mongolia">Mongolia</option>
	            <option value="Montenegro" title="Montenegro">Montenegro</option>
	            <option value="Montserrat" title="Montserrat">Montserrat</option>
	            <option value="Morocco" title="Morocco">Morocco</option>
	            <option value="Mozambique" title="Mozambique">Mozambique</option>
	            <option value="Myanmar" title="Myanmar">Myanmar</option>
	            <option value="Namibia" title="Namibia">Namibia</option>
	            <option value="Nauru" title="Nauru">Nauru</option>
	            <option value="Nepal" title="Nepal">Nepal</option>
	            <option value="Netherlands" title="Netherlands">Netherlands</option>
	            <option value="New Caledonia" title="New Caledonia">New Caledonia</option>
	            <option value="New Zealand" title="New Zealand">New Zealand</option>
	            <option value="Nicaragua" title="Nicaragua">Nicaragua</option>
	            <option value="Niger" title="Niger">Niger</option>
	            <option value="Nigeria" title="Nigeria">Nigeria</option>
	            <option value="Niue" title="Niue">Niue</option>
	            <option value="Norfolk Island" title="Norfolk Island">Norfolk Island</option>
	            <option value="Northern Mariana Islands" title="Northern Mariana Islands">Northern Mariana Islands</option>
	            <option value="Norway" title="Norway">Norway</option>
	            <option value="Oman" title="Oman">Oman</option>
	            <option value="Pakistan" title="Pakistan">Pakistan</option>
	            <option value="Palau" title="Palau">Palau</option>
	            <option value="Palestinian Territory, Occupied" title="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
	            <option value="Panama" title="Panama">Panama</option>
	            <option value="Papua New Guinea" title="Papua New Guinea">Papua New Guinea</option>
	            <option value="Paraguay" title="Paraguay">Paraguay</option>
	            <option value="Peru" title="Peru">Peru</option>
	            <option value="Philippines" title="Philippines">Philippines</option>
	            <option value="Pitcairn" title="Pitcairn">Pitcairn</option>
	            <option value="Poland" title="Poland">Poland</option>
	            <option value="Portugal" title="Portugal">Portugal</option>
	            <option value="Puerto Rico" title="Puerto Rico">Puerto Rico</option>
	            <option value="Qatar" title="Qatar">Qatar</option>
	            <option value="Reunion" title="Reunion">Reunion</option>
	            <option value="Romania" title="Romania">Romania</option>
	            <option value="Russian Federation" title="Russian Federation">Russian Federation</option>
	            <option value="Rwanda" title="Rwanda">Rwanda</option>
	            <option value="Saint Barthelemy" title="Saint Barthelemy">Saint Barthelemy</option>
	            <option value="Saint Helena, Ascension and Tristan da Cunha" title="Saint Helena, Ascension and Tristan da Cunha">Saint Helena, Ascension and Tristan da Cunha</option>
	            <option value="Saint Kitts and Nevis" title="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
	            <option value="Saint Lucia" title="Saint Lucia">Saint Lucia</option>
	            <option value="Saint Martin (French part)" title="Saint Martin (French part)">Saint Martin (French part)</option>
	            <option value="Saint Pierre and Miquelon" title="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
	            <option value="Saint Vincent and the Grenadines" title="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
	            <option value="Samoa" title="Samoa">Samoa</option>
	            <option value="San Marino" title="San Marino">San Marino</option>
	            <option value="Sao Tome and Principe" title="Sao Tome and Principe">Sao Tome and Principe</option>
	            <option value="Saudi Arabia" title="Saudi Arabia">Saudi Arabia</option>
	            <option value="Senegal" title="Senegal">Senegal</option>
	            <option value="Serbia" title="Serbia">Serbia</option>
	            <option value="Seychelles" title="Seychelles">Seychelles</option>
	            <option value="Sierra Leone" title="Sierra Leone">Sierra Leone</option>
	            <option value="Singapore" title="Singapore">Singapore</option>
	            <option value="Sint Maarten (Dutch part)" title="Sint Maarten (Dutch part)">Sint Maarten (Dutch part)</option>
	            <option value="Slovakia" title="Slovakia">Slovakia</option>
	            <option value="Slovenia" title="Slovenia">Slovenia</option>
	            <option value="Solomon Islands" title="Solomon Islands">Solomon Islands</option>
	            <option value="Somalia" title="Somalia">Somalia</option>
	            <option value="South Africa" title="South Africa">South Africa</option>
	            <option value="South Georgia and the South Sandwich Islands" title="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
	            <option value="South Sudan" title="South Sudan">South Sudan</option>
	            <option value="Spain" title="Spain">Spain</option>
	            <option value="Sri Lanka" title="Sri Lanka">Sri Lanka</option>
	            <option value="Sudan" title="Sudan">Sudan</option>
	            <option value="Suriname" title="Suriname">Suriname</option>
	            <option value="Svalbard and Jan Mayen" title="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
	            <option value="Swaziland" title="Swaziland">Swaziland</option>
	            <option value="Sweden" title="Sweden">Sweden</option>
	            <option value="Switzerland" title="Switzerland">Switzerland</option>
	            <option value="Syrian Arab Republic" title="Syrian Arab Republic">Syrian Arab Republic</option>
	            <option value="Taiwan, Province of China" title="Taiwan, Province of China">Taiwan, Province of China</option>
	            <option value="Tajikistan" title="Tajikistan">Tajikistan</option>
	            <option value="Tanzania, United Republic of" title="Tanzania, United Republic of">Tanzania, United Republic of</option>
	            <option value="Thailand" title="Thailand">Thailand</option>
	            <option value="Timor-Leste" title="Timor-Leste">Timor-Leste</option>
	            <option value="Togo" title="Togo">Togo</option>
	            <option value="Tokelau" title="Tokelau">Tokelau</option>
	            <option value="Tonga" title="Tonga">Tonga</option>
	            <option value="Trinidad and Tobago" title="Trinidad and Tobago">Trinidad and Tobago</option>
	            <option value="Tunisia" title="Tunisia">Tunisia</option>
	            <option value="Turkey" title="Turkey">Turkey</option>
	            <option value="Turkmenistan" title="Turkmenistan">Turkmenistan</option>
	            <option value="Turks and Caicos Islands" title="Turks and Caicos Islands">Turks and Caicos Islands</option>
	            <option value="Tuvalu" title="Tuvalu">Tuvalu</option>
	            <option value="Uganda" title="Uganda">Uganda</option>
	            <option value="Ukraine" title="Ukraine">Ukraine</option>
	            <option value="United Arab Emirates" title="United Arab Emirates">United Arab Emirates</option>
	            <option value="United Kingdom" title="United Kingdom">United Kingdom</option>
	            <option value="United States" title="United States">United States</option>
	            <option value="United States Minor Outlying Islands" title="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
	            <option value="Uruguay" title="Uruguay">Uruguay</option>
	            <option value="Uzbekistan" title="Uzbekistan">Uzbekistan</option>
	            <option value="Vanuatu" title="Vanuatu">Vanuatu</option>
	            <option value="Venezuela, Bolivarian Republic of" title="Venezuela, Bolivarian Republic of">Venezuela, Bolivarian Republic of</option>
	            <option value="Viet Nam" title="Viet Nam">Viet Nam</option>
	            <option value="Virgin Islands, British" title="Virgin Islands, British">Virgin Islands, British</option>
	            <option value="Virgin Islands, U.S." title="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
	            <option value="Wallis and Futuna" title="Wallis and Futuna">Wallis and Futuna</option>
	            <option value="Western Sahara" title="Western Sahara">Western Sahara</option>
	            <option value="Yemen" title="Yemen">Yemen</option>
	            <option value="Zambia" title="Zambia">Zambia</option>
	            <option value="Zimbabwe" title="Zimbabwe">Zimbabwe</option>
			</select><br />
				<span class="description">Please select your country.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_phone">Phone</label></th>

			<td>
				<input type="text" name="user_phone" id="user_phone" value="<?php echo esc_attr( get_the_author_meta( 'user_phone', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please add your phone number.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_tw">Twitter</label></th>

			<td>
				<input type="text" name="user_tw" id="user_tw" value="<?php echo esc_attr( get_the_author_meta( 'user_tw', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please add your Twitter handle.</span>
			</td>
		</tr>
		<tr>
			<th><label for="user_fb">Facebook</label></th>

			<td>
				<input type="text" name="user_fb" id="user_fb" value="<?php echo esc_attr( get_the_author_meta( 'user_fb', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please add your full Facebook URL.</span>
			</td>
		</tr>

	</table>

<?php }


add_action( 'personal_options_update', 'sg_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'sg_save_extra_profile_fields' );

function sg_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'biz_name', $_POST['biz_name'] );
	if($_POST['user_name'] != 'Your Name') {
		if($_POST['user_name'] != '') {
			$sg_u_names = explode(' ',$_POST['user_name']);
          	wp_update_user( array ( 'ID' => $user_id, 'first_name' => $sg_u_names[0] ) ) ;
          	wp_update_user( array ( 'ID' => $user_id, 'last_name' => $sg_u_names[1] ) ) ;
		}
	}
	
	
	if($_POST['user_website'] != '') {
		wp_update_user( array ( 'ID' => $user_id, 'user_url' => $_POST['user_website'] ) ) ;
	}
	if($_POST['user_occupation'] != 'Occupation') {
		update_usermeta( $user_id, 'user_occupation', $_POST['user_occupation'] );
	}
	if($_POST['user_address'] != 'Address') {
		update_usermeta( $user_id, 'user_address', $_POST['user_address'] );
	}
	if($_POST['user_address2'] != 'Address 2') {
		update_usermeta( $user_id, 'user_address2', $_POST['user_address2'] );
	}
	if($_POST['user_city'] != 'City') {
		update_usermeta( $user_id, 'user_city', $_POST['user_city'] );
	}
	if($_POST['user_state'] != 'State') {
		update_usermeta( $user_id, 'user_state', $_POST['user_state'] );
	}
	if($_POST['user_zip'] != 'Zip') {
		update_usermeta( $user_id, 'user_zip', $_POST['user_zip'] );
	}
	if($_POST['user_county'] != 'County') {
		update_usermeta( $user_id, 'user_county', $_POST['user_county'] );
	}
	if($_POST['user_country'] != 'Country') {
		update_usermeta( $user_id, 'user_country', $_POST['user_country'] );
	}
	if($_POST['user_phone'] != 'Phone') {
		update_usermeta( $user_id, 'user_phone', $_POST['user_phone'] );
	}
	if($_POST['user_tw'] != 'Twitter Handle') {
		update_usermeta( $user_id, 'user_tw', $_POST['user_tw'] );
	}
	if($_POST['user_fb'] != 'Facebook URL') {
		update_usermeta( $user_id, 'user_fb', $_POST['user_fb'] );
	}
	
}

add_action('register_form','sg_myplugin_register_form');
    function sg_myplugin_register_form (){
        $biz_name = ( isset( $_POST['biz_name'] ) ) ? $_POST['biz_name']: '';
        ?>
        <p>
            <label for="biz_name"><?php _e('Business Name','mydomain') ?> iiiiii<br />
                <input type="text" name="biz_name" id="biz_name" class="input" value="<?php echo esc_attr(stripslashes($biz_name)); ?>" size="25" /></label>
        </p>
        <?php
    }
	
add_filter('registration_errors', 'sg_myplugin_registration_errors', 10, 3);
    function sg_myplugin_registration_errors ($errors, $sanitized_user_login, $user_email) {

        if ( empty( $_POST['biz_name'] ) )
            $errors->add( 'biz_name_error', __('<strong>ERROR</strong>: You must include a business name.','mydomain') );
        if ( empty( $_POST['user_name'] ) )
            $errors->add( 'user_name_error', __('<strong>ERROR</strong>: You must include your full name.','mydomain') );
        if ( empty( $_POST['user_occupation'] ) )
            $errors->add( 'user_occupation_error', __('<strong>ERROR</strong>: You must include your occupation.','mydomain') );
        if ( empty( $_POST['user_address'] ) )
            $errors->add( 'user_address_error', __('<strong>ERROR</strong>: You must include your address.','mydomain') );
        if ( empty( $_POST['user_city'] ) )
            $errors->add( 'user_city_error', __('<strong>ERROR</strong>: You must include your city.','mydomain') );
        if ( empty( $_POST['user_state'] ) )
            $errors->add( 'user_state_error', __('<strong>ERROR</strong>: You must include your state.','mydomain') );
        if ( empty( $_POST['user_zip'] ) )
            $errors->add( 'user_zip_error', __('<strong>ERROR</strong>: You must include your zip/postal code.','mydomain') );
        if ( empty( $_POST['user_country'] ) )
            $errors->add( 'user_country_error', __('<strong>ERROR</strong>: You must include your country.','mydomain') );


        return $errors;
    }
	
    add_action('user_register', 'sg_myplugin_user_register');
    function sg_myplugin_user_register ($user_id) {
        if (( isset( $_POST['biz_name'] )) && ($_POST['biz_name'] != 'Company Name')) {
            update_user_meta($user_id, 'biz_name', $_POST['biz_name']); }
        
		if (( isset( $_POST['user_name'] ) ) && ($_POST['user_name'] != 'Your Name')) {
            $sg_u_names = explode(' ',$_POST['user_name']);
          	wp_update_user( array ( 'ID' => $user_id, 'first_name' => $sg_u_names[0] ) ) ;
          	wp_update_user( array ( 'ID' => $user_id, 'last_name' => $sg_u_names[1] ) ) ;
		}
		
		
        if (( isset( $_POST['user_occupation'] ) ) && ($_POST['user_occupation'] != 'Occupation')) {
            update_user_meta($user_id, 'user_occupation', $_POST['user_occupation']); }
			
        if (( isset( $_POST['user_address'] ) ) && ($_POST['user_address'] != 'Address')) {
            update_user_meta($user_id, 'user_address', $_POST['user_address']); }
			
        if (( isset( $_POST['user_address2'] ) ) && ($_POST['user_address2'] != 'Address 2')) {
            update_user_meta($user_id, 'user_address2', $_POST['user_address2']); }
			
        if (( isset( $_POST['user_city'] ) ) && ($_POST['user_city'] != 'City')) {
            update_user_meta($user_id, 'user_city', $_POST['user_city']); }
			
        if (( isset( $_POST['user_state'] ) ) && ($_POST['user_state'] != 'State')) {
            update_user_meta($user_id, 'user_state', $_POST['user_state']); }
			
        if (( isset( $_POST['user_zip'] ) ) && ($_POST['user_zip'] != 'Zip')) {
            update_user_meta($user_id, 'user_zip', $_POST['user_zip']); }
			
        if (( isset( $_POST['user_county'] ) ) && ($_POST['user_county'] != 'County')) {
            update_user_meta($user_id, 'user_county', $_POST['user_county']); }
			
        if (( isset( $_POST['user_country'] ) ) && ($_POST['user_country'] != 'Country')) {
            update_user_meta($user_id, 'user_country', $_POST['user_country']); }
			
        if (( isset( $_POST['user_phone'] ) ) && ($_POST['user_phone'] != 'Phone')) {
            update_user_meta($user_id, 'user_phone', $_POST['user_phone']); }
			
        if (( isset( $_POST['user_website'] ) ) && ($_POST['user_website'] != 'Website')) {
          	wp_update_user( array ( 'ID' => $user_id, 'user_url' => $_POST['user_website'] ) ) ; }
			
        if (( isset( $_POST['user_tw'] ) ) && ($_POST['user_tw'] != 'Twitter Handle')) {
            update_user_meta($user_id, 'user_tw', $_POST['user_tw']); }
			
        if (( isset( $_POST['user_fb'] ) ) && ($_POST['user_fb'] != 'Facebook URL')) {
            update_user_meta($user_id, 'user_fb', $_POST['user_fb']); }
		
    }
	

// END BUSINESS NAME FIELD
///////////////////////////







///////////////////////////
// MAIN PLUGIN ACTIVATION SCRIPT

function sg_install() {
	
	
	
	
	
add_option('sg_do_activation_redirect', true);

update_option( 'users_can_register', 1 );
update_option( 'show_avatars', 1 );	

ob_start(); ?>

<div id="sg_reg_form"> <!-- Registration -->
		<div id="sg-register-form">
		<div class="title">
			<div id="sg-statement" class="sg-statement">A password will be e-mailed to you.</div>
			<h1>Register your Account</h1>
			<span>If you do not have a Gravatar set up yet, please set it up <a href="http://gravatar.com" target="_blank">here</a>.</span>
		</div>
        
			<form action="<?php echo site_url("wp-login.php?action=register", "login_post"); ?>" method="post">
            
			<input type="text" name="user_login" value="Username" id="user_login" class="input" onfocus="repl_user()" onblur="repl_user()" />
			<input type="text" name="user_email" value="E-Mail" id="user_email" class="input" onfocus="repl_email()" onblur="repl_email()"  />
            <hr />
			<input type="text" name="user_name" value="Your Name" id="user_name" class="input" onfocus="repl_uname()" onblur="repl_uname()" />
			<input type="text" name="biz_name" value="Company Name" id="biz_name" class="input" onfocus="repl_titl()" onblur="repl_titl()" />
			<input type="text" name="user_occupation" value="Occupation" id="user_occupation" class="input" onfocus="repl_oc()" onblur="repl_oc()" />
            <hr />
			<input type="text" name="user_address" value="Address" id="user_address" class="input" onfocus="repl_addrs()" onblur="repl_addrs()" />
			<input type="text" name="user_address2" value="Address 2" id="user_address2" class="input" onfocus="repl_addrs2()" onblur="repl_addrs2()" />
			<input type="text" name="user_city" value="City" id="user_city" class="input" onfocus="repl_city()" onblur="repl_city()" /><br /><select name="user_state" id="user_state"c lass="input">
				<option value="AL">Alabama</option>
	            <option value="AK">Alaska</option>
	            <option value="AZ">Arizona</option>
	            <option value="AR">Arkansas</option>
	            <option value="CA">California</option>
	            <option value="CO">Colorado</option>
	            <option value="CT">Connecticut</option>
	            <option value="DE">Delaware</option>
	            <option value="DC">District of Columbia</option>
	            <option value="FL">Florida</option>
	            <option value="GA">Georgia</option>
	            <option value="HI">Hawaii</option>
	            <option value="ID">Idaho</option>
	            <option value="IL">Illinois</option>
	            <option value="IN">Indiana</option>
	            <option value="IA">Iowa</option>
	            <option value="KS">Kansas</option>
	            <option value="KY">Kentucky</option>
	            <option value="LA">Louisiana</option>
	            <option value="ME">Maine</option>
	            <option value="MD">Maryland</option>
	            <option value="MA">Massachusetts</option>
	            <option value="MI">Michigan</option>
	            <option value="MN">Minnesota</option>
	            <option value="MS">Mississippi</option>
	            <option value="MO">Missouri</option>
	            <option value="MT">Montana</option>
	            <option value="NE">Nebraska</option>
	            <option value="NV">Nevada</option>
	            <option value="NH">New Hampshire</option>
	            <option value="NJ">New Jersey</option>
	            <option value="NM">New Mexico</option>
	            <option value="NY">New York</option>
	            <option value="NC">North Carolina</option>
	            <option value="ND">North Dakota</option>
	            <option value="OH">Ohio</option>
	            <option value="OK">Oklahoma</option>
	            <option value="OR">Oregon</option>
	            <option value="PA">Pennsylvania</option>
	            <option value="RI">Rhode Island</option>
	            <option value="SC">South Carolina</option>
	            <option value="SD">South Dakota</option>
	            <option value="TN">Tennessee</option>
	            <option value="TX">Texas</option>
	            <option value="UT">Utah</option>
	            <option value="VT">Vermont</option>
	            <option value="VA">Virginia</option>
	            <option value="WA">Washington</option>
	            <option value="WV">West Virginia</option>
	            <option value="WI">Wisconsin</option>
	            <option value="WY">Wyoming</option>
			</select><br />
       
			<input type="text" name="user_zip" value="Zip" id="user_zip" class="input" onfocus="repl_zip()" onblur="repl_zip()" />
			<input type="text" name="user_county" value="County" id="user_county" class="input" onfocus="repl_cou()" onblur="repl_cou()" /><br /><select name="user_country" id="user_country" class="input">
	            <option value="Afghanistan" title="Afghanistan">Afghanistan</option>
	            <option value="Aland Islands" title="Aland Islands">Aland Islands</option>
	            <option value="Albania" title="Albania">Albania</option>
	            <option value="Algeria" title="Algeria">Algeria</option>
	            <option value="American Samoa" title="American Samoa">American Samoa</option>
	            <option value="Andorra" title="Andorra">Andorra</option>
	            <option value="Angola" title="Angola">Angola</option>
	            <option value="Anguilla" title="Anguilla">Anguilla</option>
	            <option value="Antarctica" title="Antarctica">Antarctica</option>
	            <option value="Antigua and Barbuda" title="Antigua and Barbuda">Antigua and Barbuda</option>
	            <option value="Argentina" title="Argentina">Argentina</option>
	            <option value="Armenia" title="Armenia">Armenia</option>
	            <option value="Aruba" title="Aruba">Aruba</option>
	            <option value="Australia" title="Australia">Australia</option>
	            <option value="Austria" title="Austria">Austria</option>
	            <option value="Azerbaijan" title="Azerbaijan">Azerbaijan</option>
	            <option value="Bahamas" title="Bahamas">Bahamas</option>
	            <option value="Bahrain" title="Bahrain">Bahrain</option>
	            <option value="Bangladesh" title="Bangladesh">Bangladesh</option>
	            <option value="Barbados" title="Barbados">Barbados</option>
	            <option value="Belarus" title="Belarus">Belarus</option>
	            <option value="Belgium" title="Belgium">Belgium</option>
	            <option value="Belize" title="Belize">Belize</option>
	            <option value="Benin" title="Benin">Benin</option>
	            <option value="Bermuda" title="Bermuda">Bermuda</option>
	            <option value="Bhutan" title="Bhutan">Bhutan</option>
	            <option value="Bolivia, Plurinational State of" title="Bolivia, Plurinational State of">Bolivia, Plurinational State of</option>
	            <option value="Bonaire, Sint Eustatius and Saba" title="Bonaire, Sint Eustatius and Saba">Bonaire, Sint Eustatius and Saba</option>
	            <option value="Bosnia and Herzegovina" title="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
	            <option value="Botswana" title="Botswana">Botswana</option>
	            <option value="Bouvet Island" title="Bouvet Island">Bouvet Island</option>
	            <option value="Brazil" title="Brazil">Brazil</option>
	            <option value="British Indian Ocean Territory" title="British Indian Ocean Territory">British Indian Ocean Territory</option>
	            <option value="Brunei Darussalam" title="Brunei Darussalam">Brunei Darussalam</option>
	            <option value="Bulgaria" title="Bulgaria">Bulgaria</option>
	            <option value="Burkina Faso" title="Burkina Faso">Burkina Faso</option>
	            <option value="Burundi" title="Burundi">Burundi</option>
	            <option value="Cambodia" title="Cambodia">Cambodia</option>
	            <option value="Cameroon" title="Cameroon">Cameroon</option>
	            <option value="Canada" title="Canada">Canada</option>
	            <option value="Cape Verde" title="Cape Verde">Cape Verde</option>
	            <option value="Cayman Islands" title="Cayman Islands">Cayman Islands</option>
	            <option value="Central African Republic" title="Central African Republic">Central African Republic</option>
	            <option value="Chad" title="Chad">Chad</option>
	            <option value="Chile" title="Chile">Chile</option>
	            <option value="China" title="China">China</option>
	            <option value="Christmas Island" title="Christmas Island">Christmas Island</option>
	            <option value="Cocos (Keeling) Islands" title="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
	            <option value="Colombia" title="Colombia">Colombia</option>
	            <option value="Comoros" title="Comoros">Comoros</option>
	            <option value="Congo" title="Congo">Congo</option>
	            <option value="Congo, the Democratic Republic of the" title="Congo, the Democratic Republic of the">Congo, the Democratic Republic of the</option>
	            <option value="Cook Islands" title="Cook Islands">Cook Islands</option>
	            <option value="Costa Rica" title="Costa Rica">Costa Rica</option>
	            <option value="Cote d'Ivoire" title="Cote d'Ivoire">Cote d'Ivoire</option>
	            <option value="Croatia" title="Croatia">Croatia</option>
	            <option value="Cuba" title="Cuba">Cuba</option>
	            <option value="Curacao" title="Curacao">Curacao</option>
	            <option value="Cyprus" title="Cyprus">Cyprus</option>
	            <option value="Czech Republic" title="Czech Republic">Czech Republic</option>
	            <option value="Denmark" title="Denmark">Denmark</option>
	            <option value="Djibouti" title="Djibouti">Djibouti</option>
	            <option value="Dominica" title="Dominica">Dominica</option>
	            <option value="Dominican Republic" title="Dominican Republic">Dominican Republic</option>
	            <option value="Ecuador" title="Ecuador">Ecuador</option>
	            <option value="Egypt" title="Egypt">Egypt</option>
	            <option value="El Salvador" title="El Salvador">El Salvador</option>
	            <option value="Equatorial Guinea" title="Equatorial Guinea">Equatorial Guinea</option>
	            <option value="Eritrea" title="Eritrea">Eritrea</option>
	            <option value="Estonia" title="Estonia">Estonia</option>
	            <option value="Ethiopia" title="Ethiopia">Ethiopia</option>
	            <option value="Falkland Islands (Malvinas)" title="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
	            <option value="Faroe Islands" title="Faroe Islands">Faroe Islands</option>
	            <option value="Fiji" title="Fiji">Fiji</option>
	            <option value="Finland" title="Finland">Finland</option>
	            <option value="France" title="France">France</option>
	            <option value="French Guiana" title="French Guiana">French Guiana</option>
	            <option value="French Polynesia" title="French Polynesia">French Polynesia</option>
	            <option value="French Southern Territories" title="French Southern Territories">French Southern Territories</option>
	            <option value="Gabon" title="Gabon">Gabon</option>
	            <option value="Gambia" title="Gambia">Gambia</option>
	            <option value="Georgia" title="Georgia">Georgia</option>
	            <option value="Germany" title="Germany">Germany</option>
	            <option value="Ghana" title="Ghana">Ghana</option>
	            <option value="Gibraltar" title="Gibraltar">Gibraltar</option>
	            <option value="Greece" title="Greece">Greece</option>
	            <option value="Greenland" title="Greenland">Greenland</option>
	            <option value="Grenada" title="Grenada">Grenada</option>
	            <option value="Guadeloupe" title="Guadeloupe">Guadeloupe</option>
	            <option value="Guam" title="Guam">Guam</option>
	            <option value="Guatemala" title="Guatemala">Guatemala</option>
	            <option value="Guernsey" title="Guernsey">Guernsey</option>
	            <option value="Guinea" title="Guinea">Guinea</option>
	            <option value="Guinea-Bissau" title="Guinea-Bissau">Guinea-Bissau</option>
	            <option value="Guyana" title="Guyana">Guyana</option>
	            <option value="Haiti" title="Haiti">Haiti</option>
	            <option value="Heard Island and McDonald Islands" title="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>
	            <option value="Holy See (Vatican City State)" title="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
	            <option value="Honduras" title="Honduras">Honduras</option>
	            <option value="Hong Kong" title="Hong Kong">Hong Kong</option>
	            <option value="Hungary" title="Hungary">Hungary</option>
	            <option value="Iceland" title="Iceland">Iceland</option>
	            <option value="India" title="India">India</option>
	            <option value="Indonesia" title="Indonesia">Indonesia</option>
	            <option value="Iran, Islamic Republic of" title="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
	            <option value="Iraq" title="Iraq">Iraq</option>
	            <option value="Ireland" title="Ireland">Ireland</option>
	            <option value="Isle of Man" title="Isle of Man">Isle of Man</option>
	            <option value="Israel" title="Israel">Israel</option>
	            <option value="Italy" title="Italy">Italy</option>
	            <option value="Jamaica" title="Jamaica">Jamaica</option>
	            <option value="Japan" title="Japan">Japan</option>
	            <option value="Jersey" title="Jersey">Jersey</option>
	            <option value="Jordan" title="Jordan">Jordan</option>
	            <option value="Kazakhstan" title="Kazakhstan">Kazakhstan</option>
	            <option value="Kenya" title="Kenya">Kenya</option>
	            <option value="Kiribati" title="Kiribati">Kiribati</option>
	            <option value="Korea, Democratic People's Republic of" title="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
	            <option value="Korea, Republic of" title="Korea, Republic of">Korea, Republic of</option>
	            <option value="Kuwait" title="Kuwait">Kuwait</option>
	            <option value="Kyrgyzstan" title="Kyrgyzstan">Kyrgyzstan</option>
	            <option value="Lao People's Democratic Republic" title="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
	            <option value="Latvia" title="Latvia">Latvia</option>
	            <option value="Lebanon" title="Lebanon">Lebanon</option>
	            <option value="Lesotho" title="Lesotho">Lesotho</option>
	            <option value="Liberia" title="Liberia">Liberia</option>
	            <option value="Libya" title="Libya">Libya</option>
	            <option value="Liechtenstein" title="Liechtenstein">Liechtenstein</option>
	            <option value="Lithuania" title="Lithuania">Lithuania</option>
	            <option value="Luxembourg" title="Luxembourg">Luxembourg</option>
	            <option value="Macao" title="Macao">Macao</option>
	            <option value="Macedonia, the former Yugoslav Republic of" title="Macedonia, the former Yugoslav Republic of">Macedonia, the former Yugoslav Republic of</option>
	            <option value="Madagascar" title="Madagascar">Madagascar</option>
	            <option value="Malawi" title="Malawi">Malawi</option>
	            <option value="Malaysia" title="Malaysia">Malaysia</option>
	            <option value="Maldives" title="Maldives">Maldives</option>
	            <option value="Mali" title="Mali">Mali</option>
	            <option value="Malta" title="Malta">Malta</option>
	            <option value="Marshall Islands" title="Marshall Islands">Marshall Islands</option>
	            <option value="Martinique" title="Martinique">Martinique</option>
	            <option value="Mauritania" title="Mauritania">Mauritania</option>
	            <option value="Mauritius" title="Mauritius">Mauritius</option>
	            <option value="Mayotte" title="Mayotte">Mayotte</option>
	            <option value="Mexico" title="Mexico">Mexico</option>
	            <option value="Micronesia, Federated States of" title="Micronesia, Federated States of">Micronesia, Federated States of</option>
	            <option value="Moldova, Republic of" title="Moldova, Republic of">Moldova, Republic of</option>
	            <option value="Monaco" title="Monaco">Monaco</option>
	            <option value="Mongolia" title="Mongolia">Mongolia</option>
	            <option value="Montenegro" title="Montenegro">Montenegro</option>
	            <option value="Montserrat" title="Montserrat">Montserrat</option>
	            <option value="Morocco" title="Morocco">Morocco</option>
	            <option value="Mozambique" title="Mozambique">Mozambique</option>
	            <option value="Myanmar" title="Myanmar">Myanmar</option>
	            <option value="Namibia" title="Namibia">Namibia</option>
	            <option value="Nauru" title="Nauru">Nauru</option>
	            <option value="Nepal" title="Nepal">Nepal</option>
	            <option value="Netherlands" title="Netherlands">Netherlands</option>
	            <option value="New Caledonia" title="New Caledonia">New Caledonia</option>
	            <option value="New Zealand" title="New Zealand">New Zealand</option>
	            <option value="Nicaragua" title="Nicaragua">Nicaragua</option>
	            <option value="Niger" title="Niger">Niger</option>
	            <option value="Nigeria" title="Nigeria">Nigeria</option>
	            <option value="Niue" title="Niue">Niue</option>
	            <option value="Norfolk Island" title="Norfolk Island">Norfolk Island</option>
	            <option value="Northern Mariana Islands" title="Northern Mariana Islands">Northern Mariana Islands</option>
	            <option value="Norway" title="Norway">Norway</option>
	            <option value="Oman" title="Oman">Oman</option>
	            <option value="Pakistan" title="Pakistan">Pakistan</option>
	            <option value="Palau" title="Palau">Palau</option>
	            <option value="Palestinian Territory, Occupied" title="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
	            <option value="Panama" title="Panama">Panama</option>
	            <option value="Papua New Guinea" title="Papua New Guinea">Papua New Guinea</option>
	            <option value="Paraguay" title="Paraguay">Paraguay</option>
	            <option value="Peru" title="Peru">Peru</option>
	            <option value="Philippines" title="Philippines">Philippines</option>
	            <option value="Pitcairn" title="Pitcairn">Pitcairn</option>
	            <option value="Poland" title="Poland">Poland</option>
	            <option value="Portugal" title="Portugal">Portugal</option>
	            <option value="Puerto Rico" title="Puerto Rico">Puerto Rico</option>
	            <option value="Qatar" title="Qatar">Qatar</option>
	            <option value="Reunion" title="Reunion">Reunion</option>
	            <option value="Romania" title="Romania">Romania</option>
	            <option value="Russian Federation" title="Russian Federation">Russian Federation</option>
	            <option value="Rwanda" title="Rwanda">Rwanda</option>
	            <option value="Saint Barthelemy" title="Saint Barthelemy">Saint Barthelemy</option>
	            <option value="Saint Helena, Ascension and Tristan da Cunha" title="Saint Helena, Ascension and Tristan da Cunha">Saint Helena, Ascension and Tristan da Cunha</option>
	            <option value="Saint Kitts and Nevis" title="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
	            <option value="Saint Lucia" title="Saint Lucia">Saint Lucia</option>
	            <option value="Saint Martin (French part)" title="Saint Martin (French part)">Saint Martin (French part)</option>
	            <option value="Saint Pierre and Miquelon" title="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
	            <option value="Saint Vincent and the Grenadines" title="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
	            <option value="Samoa" title="Samoa">Samoa</option>
	            <option value="San Marino" title="San Marino">San Marino</option>
	            <option value="Sao Tome and Principe" title="Sao Tome and Principe">Sao Tome and Principe</option>
	            <option value="Saudi Arabia" title="Saudi Arabia">Saudi Arabia</option>
	            <option value="Senegal" title="Senegal">Senegal</option>
	            <option value="Serbia" title="Serbia">Serbia</option>
	            <option value="Seychelles" title="Seychelles">Seychelles</option>
	            <option value="Sierra Leone" title="Sierra Leone">Sierra Leone</option>
	            <option value="Singapore" title="Singapore">Singapore</option>
	            <option value="Sint Maarten (Dutch part)" title="Sint Maarten (Dutch part)">Sint Maarten (Dutch part)</option>
	            <option value="Slovakia" title="Slovakia">Slovakia</option>
	            <option value="Slovenia" title="Slovenia">Slovenia</option>
	            <option value="Solomon Islands" title="Solomon Islands">Solomon Islands</option>
	            <option value="Somalia" title="Somalia">Somalia</option>
	            <option value="South Africa" title="South Africa">South Africa</option>
	            <option value="South Georgia and the South Sandwich Islands" title="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
	            <option value="South Sudan" title="South Sudan">South Sudan</option>
	            <option value="Spain" title="Spain">Spain</option>
	            <option value="Sri Lanka" title="Sri Lanka">Sri Lanka</option>
	            <option value="Sudan" title="Sudan">Sudan</option>
	            <option value="Suriname" title="Suriname">Suriname</option>
	            <option value="Svalbard and Jan Mayen" title="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
	            <option value="Swaziland" title="Swaziland">Swaziland</option>
	            <option value="Sweden" title="Sweden">Sweden</option>
	            <option value="Switzerland" title="Switzerland">Switzerland</option>
	            <option value="Syrian Arab Republic" title="Syrian Arab Republic">Syrian Arab Republic</option>
	            <option value="Taiwan, Province of China" title="Taiwan, Province of China">Taiwan, Province of China</option>
	            <option value="Tajikistan" title="Tajikistan">Tajikistan</option>
	            <option value="Tanzania, United Republic of" title="Tanzania, United Republic of">Tanzania, United Republic of</option>
	            <option value="Thailand" title="Thailand">Thailand</option>
	            <option value="Timor-Leste" title="Timor-Leste">Timor-Leste</option>
	            <option value="Togo" title="Togo">Togo</option>
	            <option value="Tokelau" title="Tokelau">Tokelau</option>
	            <option value="Tonga" title="Tonga">Tonga</option>
	            <option value="Trinidad and Tobago" title="Trinidad and Tobago">Trinidad and Tobago</option>
	            <option value="Tunisia" title="Tunisia">Tunisia</option>
	            <option value="Turkey" title="Turkey">Turkey</option>
	            <option value="Turkmenistan" title="Turkmenistan">Turkmenistan</option>
	            <option value="Turks and Caicos Islands" title="Turks and Caicos Islands">Turks and Caicos Islands</option>
	            <option value="Tuvalu" title="Tuvalu">Tuvalu</option>
	            <option value="Uganda" title="Uganda">Uganda</option>
	            <option value="Ukraine" title="Ukraine">Ukraine</option>
	            <option value="United Arab Emirates" title="United Arab Emirates">United Arab Emirates</option>
	            <option value="United Kingdom" title="United Kingdom">United Kingdom</option>
	            <option value="United States" title="United States" selected>United States</option>
	            <option value="United States Minor Outlying Islands" title="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
	            <option value="Uruguay" title="Uruguay">Uruguay</option>
	            <option value="Uzbekistan" title="Uzbekistan">Uzbekistan</option>
	            <option value="Vanuatu" title="Vanuatu">Vanuatu</option>
	            <option value="Venezuela, Bolivarian Republic of" title="Venezuela, Bolivarian Republic of">Venezuela, Bolivarian Republic of</option>
	            <option value="Viet Nam" title="Viet Nam">Viet Nam</option>
	            <option value="Virgin Islands, British" title="Virgin Islands, British">Virgin Islands, British</option>
	            <option value="Virgin Islands, U.S." title="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
	            <option value="Wallis and Futuna" title="Wallis and Futuna">Wallis and Futuna</option>
	            <option value="Western Sahara" title="Western Sahara">Western Sahara</option>
	            <option value="Yemen" title="Yemen">Yemen</option>
	            <option value="Zambia" title="Zambia">Zambia</option>
	            <option value="Zimbabwe" title="Zimbabwe">Zimbabwe</option>
			</select>
            
            <hr />
			<input type="text" name="user_phone" value="Phone" id="user_phone" class="input" onfocus="repl_phone()" onblur="repl_phone()" />
			<input type="text" name="user_website" value="Website" id="user_website" class="input" onfocus="repl_web()" onblur="repl_web()" />
			<input type="text" name="user_tw" value="Twitter Handle" id="user_tw" class="input" onfocus="repl_tw()" onblur="repl_tw()" />
			<input type="text" name="user_fb" value="Facebook URL" id="user_fb" class="input" onfocus="repl_fb()" onblur="repl_fb()" />
       
				<input type="submit" value="Register" id="register" />
			
			</form>
		</div>
</div>

<?php
$r_new_page_content = ob_get_contents();
  ob_end_clean();

$m_new_page_content = '<div id="sg_members">[subscriber_gallery sortby="registered"]</div>';



	if (is_admin()){
		
		// Registration page setup
        $r_new_page_title = 'Register';
        $r_new_page_content = $r_new_page_content;
        $r_new_page_template = ''; //ex. template-custom.php. Leave blank if you don't want a custom page template.
        //don't change the code bellow, unless you know what you're doing
        $r_page_check = get_page_by_title($r_new_page_title);
        $r_new_page = array(
                'post_type' => 'page',
                'post_title' => $r_new_page_title,
                'post_content' => $r_new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
        );
		
        if(!isset($r_page_check->ID)){
                $r_new_page_id = wp_insert_post($r_new_page);
                if(!empty($r_new_page_template)){
                        update_post_meta($r_new_page_id, '_wp_page_template', $r_new_page_template);
                }
        }
		
		// Members page setup
        $m_new_page_title = 'Members';
        $m_new_page_content = $m_new_page_content;
        $m_new_page_template = ''; //ex. template-custom.php. Leave blank if you don't want a custom page template.
        //don't change the code bellow, unless you know what you're doing
        $m_page_check = get_page_by_title($m_new_page_title);
        $m_new_page = array(
                'post_type' => 'page',
                'post_title' => $m_new_page_title,
                'post_content' => $m_new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
        );
		
        if(!isset($m_page_check->ID)){
                $m_new_page_id = wp_insert_post($m_new_page);
                if(!empty($m_new_page_template)){
                        update_post_meta($m_new_page_id, '_wp_page_template', $m_new_page_template);
                }
        }
		
		
	}
	
	
}


// END MAIN PLUGIN ACTIVATION SCRIPT
///////////////////////////



///////////////////////////
// MAIN ADMIN AREA

if ( is_admin() ){

	add_action('admin_menu', 'sg_create_menu');

	function sg_create_menu() {

		add_menu_page('Subscriber Gallery', 'Subscriber Ga', 'administrator', __FILE__, 'sg_settings_page',plugins_url('/images/sgicon.png', __FILE__));

		add_action( 'admin_init', 'sg_register_mysettings' );
	}


	function sg_register_mysettings() {
		register_setting( 'sg-settings-group', 'cg_tyurl' );
	}

	function sg_settings_page() { ?>

		<div class="wrap">
		
        	<h2>Subscriber Gallery</h2>
            
            
            <form method="post" action="options.php">
    			<?php settings_fields( 'sg-settings-group' ); ?>
    			<?php //do_settings( 'sg-settings-group' ); ?>
            
            <h2>Custom Thank You Page</h2>
            
    			<table class="form-table">
        			<tr valign="top">
        				<th scope="row">Page URL</th>
        				<td><input type="text" name="cg_tyurl" value="<?php echo get_option('cg_tyurl'); ?>" /></td>
        			</tr>
    			</table>
    
    			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>

			</form>
            
            <hr />
            
            <h2>Installation Instructions</h2>

			<p>A Registration and a Members page has been created automatically.</p>
            
            <p>You still need to create a template for individual profiles. <strong>It's easy!</strong></p>
            
            <p>All you need to do is copy the author.php file from this plugin's directory,<br />right into your theme's directory <strong>OR</strong> create a new file called author.php<br />inside your theme's directory and insert the following code into it:</p>
            
            <p><textarea style="color:#09F;width:600px;height:200px;"><?php echo file_get_contents(plugins_url('inc/author.txt', __FILE__)); ?></textarea></p>
            
            <p>Upload the file and that's it.</p>
            
            <p>&nbsp;</p>
            
            <h2>Your Pages</strong></h2>
            
            <p>&nbsp;</p>
            
            <p><strong>Registration Page:</strong> <a href="<?php echo site_url("register", "login_post"); ?>" target="_blank"><?php echo site_url("register", "login_post"); ?></a></p>
            
            <p>In case you need to recreate your Registration page, just add this into the content area of your Registration page:</p>
            
            <p><textarea style="color:#09F;width:600px;height:200px;"><div id="sg_reg_form"> <!-- Registration -->
		<div id="sg-register-form">
		<div class="title">
			<div id="sg-statement" class="sg-statement">A password will be e-mailed to you.</div>
			<h1>Register your Account</h1>
			<span>If you do not have a Gravatar set up yet, please set it up <a href="http://gravatar.com" target="_blank">here</a>.</span>
		</div>
        
			<form action="<?php echo site_url("wp-login.php?action=register", "login_post"); ?>" method="post">
            
			<input type="text" name="user_login" value="Username" id="user_login" class="input" onfocus="repl_user()" onblur="repl_user()" />
			<input type="text" name="user_email" value="E-Mail" id="user_email" class="input" onfocus="repl_email()" onblur="repl_email()"  />
            <hr />
			<input type="text" name="user_name" value="Your Name" id="user_name" class="input" onfocus="repl_uname()" onblur="repl_uname()" />
			<input type="text" name="biz_name" value="Company Name" id="biz_name" class="input" onfocus="repl_titl()" onblur="repl_titl()" />
			<input type="text" name="user_occupation" value="Occupation" id="user_occupation" class="input" onfocus="repl_oc()" onblur="repl_oc()" />
            <hr />
			<input type="text" name="user_address" value="Address" id="user_address" class="input" onfocus="repl_addrs()" onblur="repl_addrs()" />
			<input type="text" name="user_address2" value="Address 2" id="user_address2" class="input" onfocus="repl_addrs2()" onblur="repl_addrs2()" />
			<input type="text" name="user_city" value="City" id="user_city" class="input" onfocus="repl_city()" onblur="repl_city()" /><br /><select name="user_state" id="user_state"c lass="input">
				<option value="AL">Alabama</option>
	            <option value="AK">Alaska</option>
	            <option value="AZ">Arizona</option>
	            <option value="AR">Arkansas</option>
	            <option value="CA">California</option>
	            <option value="CO">Colorado</option>
	            <option value="CT">Connecticut</option>
	            <option value="DE">Delaware</option>
	            <option value="DC">District of Columbia</option>
	            <option value="FL">Florida</option>
	            <option value="GA">Georgia</option>
	            <option value="HI">Hawaii</option>
	            <option value="ID">Idaho</option>
	            <option value="IL">Illinois</option>
	            <option value="IN">Indiana</option>
	            <option value="IA">Iowa</option>
	            <option value="KS">Kansas</option>
	            <option value="KY">Kentucky</option>
	            <option value="LA">Louisiana</option>
	            <option value="ME">Maine</option>
	            <option value="MD">Maryland</option>
	            <option value="MA">Massachusetts</option>
	            <option value="MI">Michigan</option>
	            <option value="MN">Minnesota</option>
	            <option value="MS">Mississippi</option>
	            <option value="MO">Missouri</option>
	            <option value="MT">Montana</option>
	            <option value="NE">Nebraska</option>
	            <option value="NV">Nevada</option>
	            <option value="NH">New Hampshire</option>
	            <option value="NJ">New Jersey</option>
	            <option value="NM">New Mexico</option>
	            <option value="NY">New York</option>
	            <option value="NC">North Carolina</option>
	            <option value="ND">North Dakota</option>
	            <option value="OH">Ohio</option>
	            <option value="OK">Oklahoma</option>
	            <option value="OR">Oregon</option>
	            <option value="PA">Pennsylvania</option>
	            <option value="RI">Rhode Island</option>
	            <option value="SC">South Carolina</option>
	            <option value="SD">South Dakota</option>
	            <option value="TN">Tennessee</option>
	            <option value="TX">Texas</option>
	            <option value="UT">Utah</option>
	            <option value="VT">Vermont</option>
	            <option value="VA">Virginia</option>
	            <option value="WA">Washington</option>
	            <option value="WV">West Virginia</option>
	            <option value="WI">Wisconsin</option>
	            <option value="WY">Wyoming</option>
			</select><br />
       
			<input type="text" name="user_zip" value="Zip" id="user_zip" class="input" onfocus="repl_zip()" onblur="repl_zip()" />
			<input type="text" name="user_county" value="County" id="user_county" class="input" onfocus="repl_cou()" onblur="repl_cou()" /><br /><select name="user_country" id="user_country" class="input">
	            <option value="Afghanistan" title="Afghanistan">Afghanistan</option>
	            <option value="Aland Islands" title="Aland Islands">Aland Islands</option>
	            <option value="Albania" title="Albania">Albania</option>
	            <option value="Algeria" title="Algeria">Algeria</option>
	            <option value="American Samoa" title="American Samoa">American Samoa</option>
	            <option value="Andorra" title="Andorra">Andorra</option>
	            <option value="Angola" title="Angola">Angola</option>
	            <option value="Anguilla" title="Anguilla">Anguilla</option>
	            <option value="Antarctica" title="Antarctica">Antarctica</option>
	            <option value="Antigua and Barbuda" title="Antigua and Barbuda">Antigua and Barbuda</option>
	            <option value="Argentina" title="Argentina">Argentina</option>
	            <option value="Armenia" title="Armenia">Armenia</option>
	            <option value="Aruba" title="Aruba">Aruba</option>
	            <option value="Australia" title="Australia">Australia</option>
	            <option value="Austria" title="Austria">Austria</option>
	            <option value="Azerbaijan" title="Azerbaijan">Azerbaijan</option>
	            <option value="Bahamas" title="Bahamas">Bahamas</option>
	            <option value="Bahrain" title="Bahrain">Bahrain</option>
	            <option value="Bangladesh" title="Bangladesh">Bangladesh</option>
	            <option value="Barbados" title="Barbados">Barbados</option>
	            <option value="Belarus" title="Belarus">Belarus</option>
	            <option value="Belgium" title="Belgium">Belgium</option>
	            <option value="Belize" title="Belize">Belize</option>
	            <option value="Benin" title="Benin">Benin</option>
	            <option value="Bermuda" title="Bermuda">Bermuda</option>
	            <option value="Bhutan" title="Bhutan">Bhutan</option>
	            <option value="Bolivia, Plurinational State of" title="Bolivia, Plurinational State of">Bolivia, Plurinational State of</option>
	            <option value="Bonaire, Sint Eustatius and Saba" title="Bonaire, Sint Eustatius and Saba">Bonaire, Sint Eustatius and Saba</option>
	            <option value="Bosnia and Herzegovina" title="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
	            <option value="Botswana" title="Botswana">Botswana</option>
	            <option value="Bouvet Island" title="Bouvet Island">Bouvet Island</option>
	            <option value="Brazil" title="Brazil">Brazil</option>
	            <option value="British Indian Ocean Territory" title="British Indian Ocean Territory">British Indian Ocean Territory</option>
	            <option value="Brunei Darussalam" title="Brunei Darussalam">Brunei Darussalam</option>
	            <option value="Bulgaria" title="Bulgaria">Bulgaria</option>
	            <option value="Burkina Faso" title="Burkina Faso">Burkina Faso</option>
	            <option value="Burundi" title="Burundi">Burundi</option>
	            <option value="Cambodia" title="Cambodia">Cambodia</option>
	            <option value="Cameroon" title="Cameroon">Cameroon</option>
	            <option value="Canada" title="Canada">Canada</option>
	            <option value="Cape Verde" title="Cape Verde">Cape Verde</option>
	            <option value="Cayman Islands" title="Cayman Islands">Cayman Islands</option>
	            <option value="Central African Republic" title="Central African Republic">Central African Republic</option>
	            <option value="Chad" title="Chad">Chad</option>
	            <option value="Chile" title="Chile">Chile</option>
	            <option value="China" title="China">China</option>
	            <option value="Christmas Island" title="Christmas Island">Christmas Island</option>
	            <option value="Cocos (Keeling) Islands" title="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
	            <option value="Colombia" title="Colombia">Colombia</option>
	            <option value="Comoros" title="Comoros">Comoros</option>
	            <option value="Congo" title="Congo">Congo</option>
	            <option value="Congo, the Democratic Republic of the" title="Congo, the Democratic Republic of the">Congo, the Democratic Republic of the</option>
	            <option value="Cook Islands" title="Cook Islands">Cook Islands</option>
	            <option value="Costa Rica" title="Costa Rica">Costa Rica</option>
	            <option value="Cote d'Ivoire" title="Cote d'Ivoire">Cote d'Ivoire</option>
	            <option value="Croatia" title="Croatia">Croatia</option>
	            <option value="Cuba" title="Cuba">Cuba</option>
	            <option value="Curacao" title="Curacao">Curacao</option>
	            <option value="Cyprus" title="Cyprus">Cyprus</option>
	            <option value="Czech Republic" title="Czech Republic">Czech Republic</option>
	            <option value="Denmark" title="Denmark">Denmark</option>
	            <option value="Djibouti" title="Djibouti">Djibouti</option>
	            <option value="Dominica" title="Dominica">Dominica</option>
	            <option value="Dominican Republic" title="Dominican Republic">Dominican Republic</option>
	            <option value="Ecuador" title="Ecuador">Ecuador</option>
	            <option value="Egypt" title="Egypt">Egypt</option>
	            <option value="El Salvador" title="El Salvador">El Salvador</option>
	            <option value="Equatorial Guinea" title="Equatorial Guinea">Equatorial Guinea</option>
	            <option value="Eritrea" title="Eritrea">Eritrea</option>
	            <option value="Estonia" title="Estonia">Estonia</option>
	            <option value="Ethiopia" title="Ethiopia">Ethiopia</option>
	            <option value="Falkland Islands (Malvinas)" title="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
	            <option value="Faroe Islands" title="Faroe Islands">Faroe Islands</option>
	            <option value="Fiji" title="Fiji">Fiji</option>
	            <option value="Finland" title="Finland">Finland</option>
	            <option value="France" title="France">France</option>
	            <option value="French Guiana" title="French Guiana">French Guiana</option>
	            <option value="French Polynesia" title="French Polynesia">French Polynesia</option>
	            <option value="French Southern Territories" title="French Southern Territories">French Southern Territories</option>
	            <option value="Gabon" title="Gabon">Gabon</option>
	            <option value="Gambia" title="Gambia">Gambia</option>
	            <option value="Georgia" title="Georgia">Georgia</option>
	            <option value="Germany" title="Germany">Germany</option>
	            <option value="Ghana" title="Ghana">Ghana</option>
	            <option value="Gibraltar" title="Gibraltar">Gibraltar</option>
	            <option value="Greece" title="Greece">Greece</option>
	            <option value="Greenland" title="Greenland">Greenland</option>
	            <option value="Grenada" title="Grenada">Grenada</option>
	            <option value="Guadeloupe" title="Guadeloupe">Guadeloupe</option>
	            <option value="Guam" title="Guam">Guam</option>
	            <option value="Guatemala" title="Guatemala">Guatemala</option>
	            <option value="Guernsey" title="Guernsey">Guernsey</option>
	            <option value="Guinea" title="Guinea">Guinea</option>
	            <option value="Guinea-Bissau" title="Guinea-Bissau">Guinea-Bissau</option>
	            <option value="Guyana" title="Guyana">Guyana</option>
	            <option value="Haiti" title="Haiti">Haiti</option>
	            <option value="Heard Island and McDonald Islands" title="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>
	            <option value="Holy See (Vatican City State)" title="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
	            <option value="Honduras" title="Honduras">Honduras</option>
	            <option value="Hong Kong" title="Hong Kong">Hong Kong</option>
	            <option value="Hungary" title="Hungary">Hungary</option>
	            <option value="Iceland" title="Iceland">Iceland</option>
	            <option value="India" title="India">India</option>
	            <option value="Indonesia" title="Indonesia">Indonesia</option>
	            <option value="Iran, Islamic Republic of" title="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
	            <option value="Iraq" title="Iraq">Iraq</option>
	            <option value="Ireland" title="Ireland">Ireland</option>
	            <option value="Isle of Man" title="Isle of Man">Isle of Man</option>
	            <option value="Israel" title="Israel">Israel</option>
	            <option value="Italy" title="Italy">Italy</option>
	            <option value="Jamaica" title="Jamaica">Jamaica</option>
	            <option value="Japan" title="Japan">Japan</option>
	            <option value="Jersey" title="Jersey">Jersey</option>
	            <option value="Jordan" title="Jordan">Jordan</option>
	            <option value="Kazakhstan" title="Kazakhstan">Kazakhstan</option>
	            <option value="Kenya" title="Kenya">Kenya</option>
	            <option value="Kiribati" title="Kiribati">Kiribati</option>
	            <option value="Korea, Democratic People's Republic of" title="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
	            <option value="Korea, Republic of" title="Korea, Republic of">Korea, Republic of</option>
	            <option value="Kuwait" title="Kuwait">Kuwait</option>
	            <option value="Kyrgyzstan" title="Kyrgyzstan">Kyrgyzstan</option>
	            <option value="Lao People's Democratic Republic" title="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
	            <option value="Latvia" title="Latvia">Latvia</option>
	            <option value="Lebanon" title="Lebanon">Lebanon</option>
	            <option value="Lesotho" title="Lesotho">Lesotho</option>
	            <option value="Liberia" title="Liberia">Liberia</option>
	            <option value="Libya" title="Libya">Libya</option>
	            <option value="Liechtenstein" title="Liechtenstein">Liechtenstein</option>
	            <option value="Lithuania" title="Lithuania">Lithuania</option>
	            <option value="Luxembourg" title="Luxembourg">Luxembourg</option>
	            <option value="Macao" title="Macao">Macao</option>
	            <option value="Macedonia, the former Yugoslav Republic of" title="Macedonia, the former Yugoslav Republic of">Macedonia, the former Yugoslav Republic of</option>
	            <option value="Madagascar" title="Madagascar">Madagascar</option>
	            <option value="Malawi" title="Malawi">Malawi</option>
	            <option value="Malaysia" title="Malaysia">Malaysia</option>
	            <option value="Maldives" title="Maldives">Maldives</option>
	            <option value="Mali" title="Mali">Mali</option>
	            <option value="Malta" title="Malta">Malta</option>
	            <option value="Marshall Islands" title="Marshall Islands">Marshall Islands</option>
	            <option value="Martinique" title="Martinique">Martinique</option>
	            <option value="Mauritania" title="Mauritania">Mauritania</option>
	            <option value="Mauritius" title="Mauritius">Mauritius</option>
	            <option value="Mayotte" title="Mayotte">Mayotte</option>
	            <option value="Mexico" title="Mexico">Mexico</option>
	            <option value="Micronesia, Federated States of" title="Micronesia, Federated States of">Micronesia, Federated States of</option>
	            <option value="Moldova, Republic of" title="Moldova, Republic of">Moldova, Republic of</option>
	            <option value="Monaco" title="Monaco">Monaco</option>
	            <option value="Mongolia" title="Mongolia">Mongolia</option>
	            <option value="Montenegro" title="Montenegro">Montenegro</option>
	            <option value="Montserrat" title="Montserrat">Montserrat</option>
	            <option value="Morocco" title="Morocco">Morocco</option>
	            <option value="Mozambique" title="Mozambique">Mozambique</option>
	            <option value="Myanmar" title="Myanmar">Myanmar</option>
	            <option value="Namibia" title="Namibia">Namibia</option>
	            <option value="Nauru" title="Nauru">Nauru</option>
	            <option value="Nepal" title="Nepal">Nepal</option>
	            <option value="Netherlands" title="Netherlands">Netherlands</option>
	            <option value="New Caledonia" title="New Caledonia">New Caledonia</option>
	            <option value="New Zealand" title="New Zealand">New Zealand</option>
	            <option value="Nicaragua" title="Nicaragua">Nicaragua</option>
	            <option value="Niger" title="Niger">Niger</option>
	            <option value="Nigeria" title="Nigeria">Nigeria</option>
	            <option value="Niue" title="Niue">Niue</option>
	            <option value="Norfolk Island" title="Norfolk Island">Norfolk Island</option>
	            <option value="Northern Mariana Islands" title="Northern Mariana Islands">Northern Mariana Islands</option>
	            <option value="Norway" title="Norway">Norway</option>
	            <option value="Oman" title="Oman">Oman</option>
	            <option value="Pakistan" title="Pakistan">Pakistan</option>
	            <option value="Palau" title="Palau">Palau</option>
	            <option value="Palestinian Territory, Occupied" title="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
	            <option value="Panama" title="Panama">Panama</option>
	            <option value="Papua New Guinea" title="Papua New Guinea">Papua New Guinea</option>
	            <option value="Paraguay" title="Paraguay">Paraguay</option>
	            <option value="Peru" title="Peru">Peru</option>
	            <option value="Philippines" title="Philippines">Philippines</option>
	            <option value="Pitcairn" title="Pitcairn">Pitcairn</option>
	            <option value="Poland" title="Poland">Poland</option>
	            <option value="Portugal" title="Portugal">Portugal</option>
	            <option value="Puerto Rico" title="Puerto Rico">Puerto Rico</option>
	            <option value="Qatar" title="Qatar">Qatar</option>
	            <option value="Reunion" title="Reunion">Reunion</option>
	            <option value="Romania" title="Romania">Romania</option>
	            <option value="Russian Federation" title="Russian Federation">Russian Federation</option>
	            <option value="Rwanda" title="Rwanda">Rwanda</option>
	            <option value="Saint Barthelemy" title="Saint Barthelemy">Saint Barthelemy</option>
	            <option value="Saint Helena, Ascension and Tristan da Cunha" title="Saint Helena, Ascension and Tristan da Cunha">Saint Helena, Ascension and Tristan da Cunha</option>
	            <option value="Saint Kitts and Nevis" title="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
	            <option value="Saint Lucia" title="Saint Lucia">Saint Lucia</option>
	            <option value="Saint Martin (French part)" title="Saint Martin (French part)">Saint Martin (French part)</option>
	            <option value="Saint Pierre and Miquelon" title="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
	            <option value="Saint Vincent and the Grenadines" title="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
	            <option value="Samoa" title="Samoa">Samoa</option>
	            <option value="San Marino" title="San Marino">San Marino</option>
	            <option value="Sao Tome and Principe" title="Sao Tome and Principe">Sao Tome and Principe</option>
	            <option value="Saudi Arabia" title="Saudi Arabia">Saudi Arabia</option>
	            <option value="Senegal" title="Senegal">Senegal</option>
	            <option value="Serbia" title="Serbia">Serbia</option>
	            <option value="Seychelles" title="Seychelles">Seychelles</option>
	            <option value="Sierra Leone" title="Sierra Leone">Sierra Leone</option>
	            <option value="Singapore" title="Singapore">Singapore</option>
	            <option value="Sint Maarten (Dutch part)" title="Sint Maarten (Dutch part)">Sint Maarten (Dutch part)</option>
	            <option value="Slovakia" title="Slovakia">Slovakia</option>
	            <option value="Slovenia" title="Slovenia">Slovenia</option>
	            <option value="Solomon Islands" title="Solomon Islands">Solomon Islands</option>
	            <option value="Somalia" title="Somalia">Somalia</option>
	            <option value="South Africa" title="South Africa">South Africa</option>
	            <option value="South Georgia and the South Sandwich Islands" title="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
	            <option value="South Sudan" title="South Sudan">South Sudan</option>
	            <option value="Spain" title="Spain">Spain</option>
	            <option value="Sri Lanka" title="Sri Lanka">Sri Lanka</option>
	            <option value="Sudan" title="Sudan">Sudan</option>
	            <option value="Suriname" title="Suriname">Suriname</option>
	            <option value="Svalbard and Jan Mayen" title="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
	            <option value="Swaziland" title="Swaziland">Swaziland</option>
	            <option value="Sweden" title="Sweden">Sweden</option>
	            <option value="Switzerland" title="Switzerland">Switzerland</option>
	            <option value="Syrian Arab Republic" title="Syrian Arab Republic">Syrian Arab Republic</option>
	            <option value="Taiwan, Province of China" title="Taiwan, Province of China">Taiwan, Province of China</option>
	            <option value="Tajikistan" title="Tajikistan">Tajikistan</option>
	            <option value="Tanzania, United Republic of" title="Tanzania, United Republic of">Tanzania, United Republic of</option>
	            <option value="Thailand" title="Thailand">Thailand</option>
	            <option value="Timor-Leste" title="Timor-Leste">Timor-Leste</option>
	            <option value="Togo" title="Togo">Togo</option>
	            <option value="Tokelau" title="Tokelau">Tokelau</option>
	            <option value="Tonga" title="Tonga">Tonga</option>
	            <option value="Trinidad and Tobago" title="Trinidad and Tobago">Trinidad and Tobago</option>
	            <option value="Tunisia" title="Tunisia">Tunisia</option>
	            <option value="Turkey" title="Turkey">Turkey</option>
	            <option value="Turkmenistan" title="Turkmenistan">Turkmenistan</option>
	            <option value="Turks and Caicos Islands" title="Turks and Caicos Islands">Turks and Caicos Islands</option>
	            <option value="Tuvalu" title="Tuvalu">Tuvalu</option>
	            <option value="Uganda" title="Uganda">Uganda</option>
	            <option value="Ukraine" title="Ukraine">Ukraine</option>
	            <option value="United Arab Emirates" title="United Arab Emirates">United Arab Emirates</option>
	            <option value="United Kingdom" title="United Kingdom">United Kingdom</option>
	            <option value="United States" title="United States" selected>United States</option>
	            <option value="United States Minor Outlying Islands" title="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
	            <option value="Uruguay" title="Uruguay">Uruguay</option>
	            <option value="Uzbekistan" title="Uzbekistan">Uzbekistan</option>
	            <option value="Vanuatu" title="Vanuatu">Vanuatu</option>
	            <option value="Venezuela, Bolivarian Republic of" title="Venezuela, Bolivarian Republic of">Venezuela, Bolivarian Republic of</option>
	            <option value="Viet Nam" title="Viet Nam">Viet Nam</option>
	            <option value="Virgin Islands, British" title="Virgin Islands, British">Virgin Islands, British</option>
	            <option value="Virgin Islands, U.S." title="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
	            <option value="Wallis and Futuna" title="Wallis and Futuna">Wallis and Futuna</option>
	            <option value="Western Sahara" title="Western Sahara">Western Sahara</option>
	            <option value="Yemen" title="Yemen">Yemen</option>
	            <option value="Zambia" title="Zambia">Zambia</option>
	            <option value="Zimbabwe" title="Zimbabwe">Zimbabwe</option>
			</select>
            
            <hr />
			<input type="text" name="user_phone" value="Phone" id="user_phone" class="input" onfocus="repl_phone()" onblur="repl_phone()" />
			<input type="text" name="user_website" value="Website" id="user_website" class="input" onfocus="repl_web()" onblur="repl_web()" />
			<input type="text" name="user_tw" value="Twitter Handle" id="user_tw" class="input" onfocus="repl_tw()" onblur="repl_tw()" />
			<input type="text" name="user_fb" value="Facebook URL" id="user_fb" class="input" onfocus="repl_fb()" onblur="repl_fb()" />
       
				<input type="submit" value="Register" id="register" />
			
			</form>
		</div>
</div></textarea></p>
            
            <p>&nbsp;</p>
            
            
            <p><strong>Members Page:</strong> <a href="<?php echo site_url("members", "login_post"); ?>" target="_blank"><?php echo site_url("members", "login_post"); ?></a></p>
            
            <p>In case you need to recreate your Members page, just add this into the content area of your Members page:</p>
            
            <p><textarea style="color:#09F;width:600px;height:50px;"><div id="sg_members">[subscriber_gallery sortby="registered"]</div></textarea></p>
            
            
            
		</div><?php 
	
	} 


}


// END ADMIN AREA
///////////////////////////

?>