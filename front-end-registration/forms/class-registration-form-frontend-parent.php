<?php

abstract class WyzRegistrationForm_Frontend_Parent {

	private $data;
	private $profile_data;
	protected $offset;
	private $open;
	private $is_profile;
	protected $user_id;
	private $alone = [ 'textarea', 'password', 'wysiwyg', 'file' ];

	public function __construct() {
		$this->data = apply_filters( 'wyz_user_profile_form_data', get_option( 'wyz_registration_form_data', array() ) );
		$this->profile_data = apply_filters( 'wyz_user_profile_form_profile_data', get_option( 'wyz_registration_form_data', array() ) );
		$this->alone = apply_filters( 'wyz_user_profile_alone_data', $this->alone );
		$dis_prof = array( 'username', 'subscription' );
		$i=0;
		foreach ($this->profile_data as $key => $value) {
			if ( $value['type'] == 'username' || $value['type'] == 'subscription' )
				unset( $this->profile_data[ $i ] );
			$i++;
		}
		$this->offset = '';
		$this->user_id = get_current_user_id();
		if ( is_page_template( 'templates/full-width-page.php' ) || ( is_page_template( 'default_template' ) && wyz_get_option( 'sidebar-layout' ) == 'full-width' ) ) {
			$this->offset = 'col-lg-6 col-lg-offset-3 ';
		}
	}


	protected function the_profile_form() {
		$count = 0;
		$length = count( $this->profile_data );

		$keys = array_keys( $this->profile_data );
		$i = -1;
		$nxt;
		$this->is_profile = true;
		$this->open_profile_form();

		foreach ( $this->profile_data as $key => $value ) {
			$i++;
			$nxt = ( ( $i < $length -1 ) ? $this->profile_data[ $keys[ $i+1 ] ]['type'] : '' );
			$crnt = ( ( $i < $length -1 ) ? $this->profile_data[ $keys[ $i ] ]['type'] : '' );
			$this->input_sided_open( $count++%2, $length, $nxt, $crnt );
			$this->open_field( $value['type'] );
			do_action('wyz_before_registration_form_field', $value);
			switch( $value['type'] ) {
				case 'pemail':
					$this->email_field($value['id'], $value);
				break;
				case 'fname':
					$this->fname_field($value['id'], $value);
				break;
				case 'lname':
					$this->lname_field($value['id'], $value);
				break;
				case 'password':
					$this->pass_field( $value['id'], $value, true );
				break;
				case 'billing_company':
				case 'billing_address_1':
				case 'billing_address_2':
				case 'billing_city':
				case 'billing_state':
					$this->woo_field( $value['id'], $value, true, 'text' );
				break;
				case 'billing_country':
					$this->woo_country_field($value['id'],$value);
				break;
				case 'billing_phone':
				case 'billing_postcode':
					$this->woo_field( $value['id'], $value, true, 'number' );
				break;
				case 'text':
				case 'number':
				case 'email':
				case 'url':
				case 'date':
					$this->text_field( $value['id'], $value );
				break;
				case 'textarea':
					$this->textarea_field( $value['id'], $value );
				break;
				case 'wysiwyg':
					$this->wysiwyg_field( $value['id'], $value );
				break;
				case 'file':
					$this->file_field( $value['id'], $value );
				break;
				case 'selectbox':
					$this->select_field( $value['id'], $value );
				break;
				default:
					do_action( 'wyz_user_profile_form_field', $value );
			}
			do_action('wyz_after_registration_form_field', $value);
			$this->close_field( $value['type'] );
			$this->input_sided_close( $count%2, $length, $crnt );
			if ( in_array( $crnt, $this->alone ) )
				$count =0;
		}
		$this->is_profile = false;

		$this->after_profile_fields();
		$this->close_form();
	}


	protected function the_form() {
		$count = 0;
		$length = count( $this->data );

		$keys = array_keys( $this->data );
		$i = -1;
		$nxt;
		do_action('wyz_before_registration_form');
		$this->open_form();

		foreach ( $this->data as $key => $value ) {
			$i++;
			$nxt = ( ( $i < $length -1 ) ? $this->data[ $keys[ $i+1 ] ]['type'] : '' );
			$crnt = ( ( $i < $length -1 ) ? $this->data[ $keys[ $i ] ]['type'] : '' );
			$this->input_sided_open( $count++%2, $length, $nxt, $crnt );
			$this->open_field( $value['type'] );
			do_action('wyz_before_registration_form_field', $value);
			switch( $value['type'] ) {
				case 'username':
					$this->username_field($value['id'], $value);
				break;
				case 'pemail':
					$this->email_field($value['id'], $value);
				break;
				case 'fname':
					$this->fname_field($value['id'], $value);
				break;
				case 'lname':
					$this->lname_field($value['id'], $value);
				break;
				case 'password':
					$this->pass_field( $value['id'], $value, false );
				break;
				case 'subscription':
					$this->subscription_field($value['id'], $value);
				break;
				case 'recaptcha':
					$this->recaptcha_field( $value['id'], $value );
				break;
				case 'billing_company':
				case 'billing_address_1':
				case 'billing_address_2':
				case 'billing_city':
				case 'billing_state':
					$this->woo_field( $value['id'], $value, true, 'text' );
				break;
				case 'billing_country':
					$this->woo_country_field($value['id'],$value);
				break;
				case 'billing_phone':
				case 'billing_postcode':
					$this->woo_field( $value['id'], $value, true, 'number' );
				break;
				case 'text':
				case 'number':
				case 'email':
				case 'date':
				case 'url':
					$this->text_field( $value['id'], $value );
				break;
				case 'textarea':
					$this->textarea_field( $value['id'], $value );
				break;
				case 'wysiwyg':
					$this->wysiwyg_field( $value['id'], $value );
				break;
				case 'file':
					$this->file_field( $value['id'], $value );
				break;
				case 'selectbox':
					$this->select_field( $value['id'], $value );
				break;
				default:
					do_action( 'wyz_user_profile_form_field', $value );
			}
			do_action('wyz_after_registration_form_field', $value);
			$this->close_field( $value['type'] );
			$this->input_sided_close( $count%2, $length, $crnt );
			if ( in_array( $crnt, $this->alone ) )
				$count =0;
		}
		do_action( 'wyz_before_close_registration_fields', $this->data );
		$this->after_fields();
		do_action( 'wyz_after_close_registration_fields', $this->data );
		$this->close_form();
		do_action( 'wyz_after_registration_form', $this->data );
	}

	protected abstract function open_form();
	protected abstract function open_profile_form();
	protected abstract function after_fields();
	protected abstract function after_profile_fields();
	protected abstract function close_form();
	protected abstract function open_field( $type );
	protected abstract function close_field( $type );
	protected abstract function open_separate();
	protected abstract function pass_field( $key, $value,$is_profile );


	protected function get_field_value( $key1, $key2) {
		if ( $this->is_profile ) {
			if ( 'email' == $key1 )
				return esc_attr( get_the_author_meta( 'user_email', $this->user_id ) );
			return get_user_meta( $this->user_id, $key1, true );
		}
		return isset( $_POST[ $key2 ] ) ? $_POST[ $key2 ] : '';
	}


	protected function input_sided_open( $count, $length, $next, $current ) {
		if ( !( $count%2 ) && ! empty($current)&& $count != ( $length - 1 ) && ! in_array( $next, $this->alone )&& ! in_array( $current, $this->alone ) ) {
			$this->open_separate();
			$this->open = true;
			return true;
		}
		return false;
	}

	protected function input_sided_close( $count, $length, $current ) {
		if ( ( !( $count%2 ) || in_array( $current, $this->alone ) || empty( $current ) ) && $this->open ) {
			echo '</div>';
			$this->open = false;
		}
	}

	protected function the_tooltip( $value, $return = false ){
		if ( $return )ob_start();
		if ( isset( $value['tooltip'] ) && ! empty( $value['tooltip'] ) )
			echo 'title="'.$value['tooltip'].'"';
		if($return)return ob_get_clean();
	}

	protected function username_field($key, $value) {
		$user_login = isset( $_POST['wyz_user_register'] ) ? $_POST['wyz_user_register'] : '';
		?>
			<label for="wyz_user_register"><?php  echo $value['label']; ?><span class="req"> *</span></label>
			<input name="wyz_user_register" class="text-input<?php echo $value['cssClass'];?>" <?php $this->the_tooltip( $value );?>  id="wyz_user_register" type="text" value="<?php echo esc_attr( $user_login );?>" required/>
		<?php
	}

	protected function email_field($key, $value) {
		$user_email = $this->get_field_value( 'email', 'wyz_user_email');
		?>
			<label for="wyz_user_email"><?php echo $value['label']; ?><span class="req"> *</span></label>
			<input name="wyz_user_email" class="text-input <?php echo $value['cssClass'];?>" <?php $this->the_tooltip( $value );?> id="wyz_user_email" type="email" value="<?php echo esc_attr( $user_email );?>" required/>
		<?php
	}

	protected function fname_field($key, $value) {
		$user_first = $this->get_field_value( 'first_name', 'wyz_user_first');
		?>
			<label for="wyz_user_first"><?php echo $value['label']; ?><span class="req"> *</span></label>
			<input name="wyz_user_first" id="wyz_user_first" class="text-input <?php echo $value['cssClass'];?>" <?php $this->the_tooltip( $value );?> type="text" value="<?php echo esc_attr( $user_first );?>" required/>
		<?php
	}

	protected function lname_field($key, $value) {
		$user_last =  $this->get_field_value( 'last_name', 'wyz_user_last');
		?>
			<label for="wyz_user_last"><?php echo $value['label']; ?><span class="req"> *</span></label>
			<input class="text-input <?php echo $value['cssClass'];?>" name="wyz_user_last" <?php $this->the_tooltip( $value );?> id="wyz_user_last" type="text" value="<?php echo esc_attr( $user_last );?>" required/>
		<?php
	}

	protected function woo_field($key, $value, $type) {
		$user_data = $this->get_field_value( $value['type'], $value['type']);
		?>
			<label for="<?php echo $value['type'];?>"><?php echo $value['label'] .( ! empty( $value['required'] ) ? '<span class="req"> *</span>' : '' );?></label>
			<input class="<?php echo $value['cssClass'];?>" name="<?php echo $value['type']?>" <?php $this->the_tooltip( $value );?> id="<?php echo $value['type']?>" type="<?php echo $type;?>" value="<?php echo esc_attr( $user_data );?>" <?php echo ! empty( $value['required'] ) ? 'required' : '';?>/>
		<?php
	}

	protected function woo_country_field($key, $value) {

		$user_data = $this->get_field_value( $value['type'], $value['type']);

		$countries = array("AX"=> 'Åland Islands',"AF"=> 'Afghanistan',"AL"=> 'Albania',"DZ"=> 'Algeria',"AS"=> 'American Samoa',"AD"=> 'Andorra',"AO"=> 'Angola',"AI"=> 'Anguilla',"AQ"=> 'Antarctica',"AG"=> 'Antigua and Barbuda',"AR"=> 'Argentina',"AM"=> 'Armenia',"AW"=> 'Aruba',"AU"=> 'Australia',"AT"=> 'Austria',"AZ"=> 'Azerbaijan',"BS"=> 'Bahamas',"BH"=> 'Bahrain',"BD"=> 'Bangladesh',"BB"=> 'Barbados',"BY"=> 'Belarus',"PW"=> 'Belau',"BE"=> 'Belgium',"BZ"=> 'Belize',"BJ"=> 'Benin',"BM"=> 'Bermuda',"BT"=> 'Bhutan',"BO"=> 'Bolivia',"BQ"=> 'Bonaire, Saint Eustatius and Saba',"BA"=> 'Bosnia and Herzegovina',"BW"=> 'Botswana',"BV"=> 'Bouvet Island',"BR"=> 'Brazil',"IO"=> 'British Indian Ocean Territory',"VG"=> 'British Virgin Islands',"BN"=> 'Brunei',"BG"=> 'Bulgaria',"BF"=> 'Burkina Faso',"BI"=> 'Burundi',"KH"=> 'Cambodia',"CM"=> 'Cameroon',"CA"=> 'Canada',"CV"=> 'Cape Verde',"KY"=> 'Cayman Islands',"CF"=> 'Central African Republic',"TD"=> 'Chad',"CL"=> 'Chile',"CN"=> 'China',"CX"=> 'Christmas Island',"CC"=> 'Cocos (Keeling) Islands',"CO"=> 'Colombia',"KM"=> 'Comoros',"CG"=> 'Congo (Brazzaville)',"CD"=> 'Congo (Kinshasa)',"CK"=> 'Cook Islands',"CR"=> 'Costa Rica',"HR"=> 'Croatia',"CU"=> 'Cuba',"CW"=> 'Curaçao',"CY"=> 'Cyprus',"CZ"=> 'Czech Republic',"DK"=> 'Denmark',"DJ"=> 'Djibouti',"DM"=> 'Dominica',"DO"=> 'Dominican Republic',"EC"=> 'Ecuador',"EG"=> 'Egypt',"SV"=> 'El Salvador',"GQ"=> 'Equatorial Guinea',"ER"=> 'Eritrea',"EE"=> 'Estonia',"ET"=> 'Ethiopia',"FK"=> 'Falkland Islands',"FO"=> 'Faroe Islands',"FJ"=> 'Fiji',"FI"=> 'Finland',"FR"=> 'selected="selected">France',"GF"=> 'French Guiana',"PF"=> 'French Polynesia',"TF"=> 'French Southern Territories',"GA"=> 'Gabon',"GM"=> 'Gambia',"GE"=> 'Georgia',"DE"=> 'Germany',"GH"=> 'Ghana',"GI"=> 'Gibraltar',"GR"=> 'Greece',"GL"=> 'Greenland',"GD"=> 'Grenada',"GP"=> 'Guadeloupe',"GU"=> 'Guam',"GT"=> 'Guatemala',"GG"=> 'Guernsey',"GN"=> 'Guinea',"GW"=> 'Guinea-Bissau',"GY"=> 'Guyana',"HT"=> 'Haiti',"HM"=> 'Heard Island and McDonald Islands',"HN"=> 'Honduras',"HK"=> 'Hong Kong',"HU"=> 'Hungary',"IS"=> 'Iceland',"IN"=> 'India',"ID"=> 'Indonesia',"IR"=> 'Iran',"IQ"=> 'Iraq',"IE"=> 'Ireland',"IM"=> 'Isle of Man',"IL"=> 'Israel',"IT"=> 'Italy',"CI"=> 'Ivory Coast',"JM"=> 'Jamaica',"JP"=> 'Japan',"JE"=> 'Jersey',"JO"=> 'Jordan',"KZ"=> 'Kazakhstan',"KE"=> 'Kenya',"KI"=> 'Kiribati',"KW"=> 'Kuwait',"KG"=> 'Kyrgyzstan',"LA"=> 'Laos',"LV"=> 'Latvia',"LB"=> 'Lebanon',"LS"=> 'Lesotho',"LR"=> 'Liberia',"LY"=> 'Libya',"LI"=> 'Liechtenstein',"LT"=> 'Lithuania',"LU"=> 'Luxembourg',"MO"=> 'Macao S.A.R., China',"MK"=> 'Macedonia',"MG"=> 'Madagascar',"MW"=> 'Malawi',"MY"=> 'Malaysia',"MV"=> 'Maldives',"ML"=> 'Mali',"MT"=> 'Malta',"MH"=> 'Marshall Islands',"MQ"=> 'Martinique',"MR"=> 'Mauritania',"MU"=> 'Mauritius',"YT"=> 'Mayotte',"MX"=> 'Mexico',"FM"=> 'Micronesia',"MD"=> 'Moldova',"MC"=> 'Monaco',"MN"=> 'Mongolia',"ME"=> 'Montenegro',"MS"=> 'Montserrat',"MA"=> 'Morocco',"MZ"=> 'Mozambique',"MM"=> 'Myanmar',"NA"=> 'Namibia',"NR"=> 'Nauru',"NP"=> 'Nepal',"NL"=> 'Netherlands',"NC"=> 'New Caledonia',"NZ"=> 'New Zealand',"NI"=> 'Nicaragua',"NE"=> 'Niger',"NG"=> 'Nigeria',"NU"=> 'Niue',"NF"=> 'Norfolk Island',"KP"=> 'North Korea',"MP"=> 'Northern Mariana Islands',"NO"=> 'Norway',"OM"=> 'Oman',"PK"=> 'Pakistan',"PS"=> 'Palestinian Territory',"PA"=> 'Panama',"PG"=> 'Papua New Guinea',"PY"=> 'Paraguay',"PE"=> 'Peru',"PH"=> 'Philippines',"PN"=> 'Pitcairn',"PL"=> 'Poland',"PT"=> 'Portugal',"PR"=> 'Puerto Rico',"QA"=> 'Qatar',"RE"=> 'Reunion',"RO"=> 'Romania',"RU"=> 'Russia',"RW"=> 'Rwanda',"ST"=> 'São Tomé and Príncipe',"BL"=> 'Saint Barthélemy',"SH"=> 'Saint Helena',"KN"=> 'Saint Kitts and Nevis',"LC"=> 'Saint Lucia',"SX"=> 'Saint Martin (Dutch part)',"MF"=> 'Saint Martin (French part)',"PM"=> 'Saint Pierre and Miquelon',"VC"=> 'Saint Vincent and the Grenadines',"WS"=> 'Samoa',"SM"=> 'San Marino',"SA"=> 'Saudi Arabia',"SN"=> 'Senegal',"RS"=> 'Serbia',"SC"=> 'Seychelles',"SL"=> 'Sierra Leone',"SG"=> 'Singapore',"SK"=> 'Slovakia',"SI"=> 'Slovenia',"SB"=> 'Solomon Islands',"SO"=> 'Somalia',"ZA"=> 'South Africa',"GS"=> 'South Georgia/Sandwich Islands',"KR"=> 'South Korea',"SS"=> 'South Sudan',"ES"=> 'Spain',"LK"=> 'Sri Lanka',"SD"=> 'Sudan',"SR"=> 'Suriname',"SJ"=> 'Svalbard and Jan Mayen',"SZ"=> 'Swaziland',"SE"=> 'Sweden',"CH"=> 'Switzerland',"SY"=> 'Syria',"TW"=> 'Taiwan',"TJ"=> 'Tajikistan',"TZ"=> 'Tanzania',"TH"=> 'Thailand',"TL"=> 'Timor-Leste',"TG"=> 'Togo',"TK"=> 'Tokelau',"TO"=> 'Tonga',"TT"=> 'Trinidad and Tobago',"TN"=> 'Tunisia',"TR"=> 'Turkey',"TM"=> 'Turkmenistan',"TC"=> 'Turks and Caicos Islands',"TV"=> 'Tuvalu',"UG"=> 'Uganda',"UA"=> 'Ukraine',"AE"=> 'United Arab Emirates',"GB"=> 'United Kingdom (UK)',"US"=> 'United States (US)',"UM"=> 'United States (US) Minor Outlying Islands',"VI"=> 'United States (US) Virgin Islands',"UY"=> 'Uruguay',"UZ"=> 'Uzbekistan',"VU"=> 'Vanuatu',"VA"=> 'Vatican',"VE"=> 'Venezuela',"VN"=> 'Vietnam',"WF"=> 'Wallis and Futuna',"EH"=> 'Western Sahara',"YE"=> 'Yemen',"ZM"=> 'Zambia',"ZW"=> 'Zimbabwe');
		?>
			<label for="<?php echo $value['type'];?>"><?php echo $value['label'] .( ! empty( $value['required'] ) ? '<span class="req"> *</span>' : '' );?></label>
			<select name="<?php echo $value['type']?>" <?php $this->the_tooltip( $value );?> id="<?php echo $value['type']?>" class="country_to_state country_select <?php echo $value['cssClass'];?>"<?php echo ! empty( $value['required'] ) ? 'required' : '';?>>
				<option value=""><?php esc_html_e( 'Select a country','wyzi-business-finder');?></option>
				<?php foreach ($countries as $key => $v) {
					echo '<option value="'.$key.'"'.($user_data==$key?' selected':'').'>' . esc_html__( $v,'wyzi-business-finder').'</option>';
				}?>
			</select>
		<?php
	}
	

	protected function subscription_field($key, $value) {
		$subscription = isset( $_POST['subscription'] ) ? $_POST['subscription'] : '';
		$roles = apply_filters( 'wyz_user_roles', array(
			'client' => get_option( 'wyz_businesses_user_client', 'Client' ),
			'business_owner' => get_option( 'wyz_businesses_user_owner' )
		));

		$user = wp_get_current_user();
		$def_role = get_option( 'wyz_reg_def_user_role' );
		if ( 'client' != $def_role && 'business_owner' != $def_role ) {?>
			<label for="subscription"><?php echo $value['label'];?><span class="req"> *</span></label>
			<select name="subscription" id="subscription" <?php $this->the_tooltip( $value );?> required class="<?php echo $value['cssClass'];?>">
				<option value=""><?php esc_html_e( 'Select your subscription...', 'wyzi-business-finder' );?></option>
				<?php foreach( $roles as $val => $name ){
					echo "<option value=\"$val\" " . ( $val == $subscription ? 'selected ' : '' ) . ">$name</option>";
				}?>
			</select>
		<?php }
	}

	protected function recaptcha_field( $key, $value) {
		if ( ! isset( $value['recaptchaSiteKey'] ) || ! isset( $value['recaptchaSecretKey'] ) || empty( $value['recaptchaSiteKey'] ) || empty( $value['recaptchaSecretKey'] ) ) return '';
		?>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
			<label><?php echo $value['label']; ?><span class="req"> *</span></label>
			<div class="g-recaptcha" data-sitekey="<?php echo $value['recaptchaSiteKey'];?>"></div>
		<?php
	}

	//type: text, number, email, url, date
	protected function text_field( $key, $value ) {
		$id = "wyz_register_fields_$key";
		$val = $this->get_field_value( $id, $id );
		echo '<label for="' . $id . '">' . $value['label'] . ( ! empty( $value['required'] ) ? '<span class="req"> *</span>' : '' ) . '</label>' .
			'<input name="' . $id . '" id="' . $id . '" '. $this->the_tooltip( $value, true ).' class="text-input ' . $value['cssClass'] . '" type="' . $value['type'] . '" value="' .  esc_attr( $val ) . '" placeholder="' . $value['placeholder'] . '" ' . ( ! empty( $value['required'] ) ? 'required' : '' ) . '/>';
	}


	protected function textarea_field( $key, $value) {
		$id = "wyz_register_fields_$key";
		$val = $this->get_field_value( $id, $id );
		echo '<label for="' . $id . '">' . $value['label']  . ( ! empty( $value['required'] ) ? '<span class="req"> *</span>': '' ) . '</label>' . 
			'<textarea name="' . $id . '" '. $this->the_tooltip( $value, true ) .' id="' . $id . '" class="text-input ' . $value['cssClass'] . '" ' . ( ! empty( $value['required'] ) ? 'required' : '' ) . '>' .  esc_attr( $val ) . '</textarea>';
	}



	protected function wysiwyg_field( $key, $value ) {
		$id = "wyz_register_fields_$key";

		if ( $this->is_profile )
			$val = get_user_meta( $this->user_id, $id, true );
		else
			$val = isset( $_POST[ $id ] ) ? $_POST[ $id ] : '';

		echo '<label for="' . $id . '" '.$this->the_tooltip($value,true).'>' . $value['label']  . ( ! empty( $value['required'] ) ? '<span class="req"> *</span>': '' ) . '</label>';
		wp_editor( esc_attr( $val ), $id, array( 'editor_class' => 'text-input ' . $value['cssClass'], 'media_buttons' => false ) );
	}

	protected function select_field( $key, $value ) {
		$id = "wyz_register_fields_$key";
		$val = $this->get_field_value( $id, $id );
		$class = '';
		$type = '';

		echo '<label for="' . $id . '">' . $value['label'] . ( ! empty( $value['required'] ) ? '<span class="req"> *</span>': '' ) . '</label>';

		switch ( $value['selecttype'] ) {
			case 'dropdown':
				$type = 'select';
				echo '<select name="'.$id.'" class="wyz-select" ' .  $this->the_tooltip( $value, true ) . '>';
				break;
			case 'radio':
				$type = 'radio';
				break;
			case 'checkboxes':
				$type = 'multicheck';
				echo '<select name="'.$id.'" class="wyz-select" multiple="multiple" '. $this->the_tooltip( $value, true ).'>';
				break;
		}

		$i_id = 0;
		foreach( $value['options'] as $option ) {
			if($type=='radio')
				echo '<div class="radio-item radio-item-id-'.$key.'"><label class="radio-label" for="'.$id.'_'.$i_id.'">'.$option['label'].'</label><input type="radio" ' . ( ( $option['value'] == $val || ( empty( $val ) && isset( $option['selected'] ) && $option['selected'] ) )  ? 'checked="checked"' : '' ) . ' id='.$id.'_'.$i_id++.' value="'.$option['value'].'" name="'.$id.'" ' . ( ! empty( $value['required'] ) ? 'required' : '' ) . '></div>';
			else
				echo '<option class="' . $value['cssClass'] . '" value="'.$option['value'].'"' . ( ( $option['value'] == $val || ( empty( $val ) && isset( $option['selected'] ) && $option['selected'] ) ) ? 'selected="selected"' : '' ) . '>'.$option['label'].'</option>';
		}
		

		if($type!='radio')
			echo '</select>';
	}

	protected function file_field( $key, $value ) {
		$id = "wyz_register_fields_$key";
		$val = $this->get_field_value( $id, $id );
		$title = esc_html__( 'Drag your files here or click in this area.', 'wyzi-business-finder' );
		echo '<label for="' . $id . '">' . $value['label'] . ( ! empty( $value['required'] ) ? '<span class="req"> *</span>' : '' ) . '</label>';
		if ( ! empty( $val ) ){
			$type = get_post_mime_type($val);
			$attch_content = '';
			$attch_link = '';
			$attch_ttl = get_the_title( $val );
			switch ($type) {
				case 'image/jpeg':
				case 'image/png':
				case 'image/gif':
					$attch_content = '<img src="'.wp_get_attachment_url($val).'"/>';
					$attch_link = wp_get_attachment_url( $val, 'full' );
					break;
				case 'application/pdf':
					$attch_content = '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
					$attch_link = wp_get_attachment_url( $val );
					break;
				case 'application/msword':
				case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
					$attch_content = '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
					$attch_link = wp_get_attachment_url( $val );
					break;
				case 'video/x-flv':
				case 'video/mp4':
				case 'application/x-mpegURL':
				case 'video/MP2T':
				case 'video/3gpp':
				case 'video/quicktime':
				case 'video/x-msvideo':
				case 'video/x-ms-wmv':
					$attch_content = '<i class="fa fa-file-video-o" aria-hidden="true"></i>';
					$attch_link = wp_get_attachment_url( $val );
				  break;
				case 'application/zip':
				case 'application/octet-stream':
					$attch_content = '<i class="fa fa-file-archive-o" aria-hidden="true"></i>';
					$attch_link = wp_get_attachment_url( $val );
					break;
				default:
					$attch_content = '<i class="fa fa-file" aria-hidden="true"></i>';
					$attch_link = wp_get_attachment_url( $val );
			}
			echo '<div class="profile-attachment"><a href="'.$attch_link.'" download>'.$attch_content.
				 "<p class=\"attch-title\">$attch_ttl</p>".'</a></div>';
		}
		echo '<div class="file-upload-cont'.(!empty($val)?' has-attch':'').'"><input name="' . $id . '" id="' . $id . '" value="' .  esc_attr( $val ) . '"  class="file-upload ' . $value['cssClass'] . '" type="file"' . ( ! empty( $value['required'] ) ? ' required' : '' ) .'><p>'. $title . '</p></div>';
	}
}