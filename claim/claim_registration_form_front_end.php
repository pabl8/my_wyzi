<?php
// We require this file first to check submitted data
require_once( plugin_dir_path( __FILE__ ) . 'do_claim_check_then_save.php' );

//Lets check of Only Buisness Owners can Claim option is on first

$only_business_owners_can_claim = false;
$show_wyzi_claim_form = true;

if ( ! isset($_GET['id']) || 'yes' == get_post_meta( $_GET['id'], 'wyz_business_claimed', true ) )
    $show_wyzi_claim_form = false;

if ( $show_wyzi_claim_form && 'on' === get_option( 'wyz_claim_should_be_business_owner' ) ){
	$only_business_owners_can_claim = true;
	if ( ! is_user_logged_in() ) {
		echo esc_html__('You need to register as a Business Owner first','wyzi-business-finder');
		$show_wyzi_claim_form = false; 
	} else {
		if ( current_user_can('publish_businesses') ) {

			if ( !WyzHelpers::user_can_create_business( get_current_user_id() ) ) {
				$show_wyzi_claim_form = false;
				echo sprintf( esc_html__( 'You cannot claim your own Business','wyzi-business-finder' ) );
			} else {
				$show_wyzi_claim_form = true;
			}

		}else {
		    echo esc_html__( 'You need to upgrade to a Business Owner first','wyzi-business-finder' );
			$show_wyzi_claim_form = false;
		}
	}

}

// Lets Check if Business Claimer is The Owner of this Business
$user_is_owner_of_business = false;

if ( WyzHelpers::wyz_the_business_author_id($_GET['id']) == get_current_user_id() ) 
    $user_is_owner_of_business =true;



?>
   <form role="form" method="post" class="wyz_claim_form_form" enctype="multipart/form-data">
   <?php
if ( ! empty( $wyz_claim_registration_form_data ) && is_array( $wyz_claim_registration_form_data ) && ! $new_claim_cpt_saved && $show_wyzi_claim_form && 'off' != get_option( 'wyz_business_claiming' ) ) { 
    $wyz_primary_color = wyz_get_option( 'primary-color' );

    if ( '' == $wyz_primary_color ) {
        $wyz_primary_color = '#00aeff';
    }
?>
    <div class="claim_header" style="background-color: <?php echo $wyz_primary_color;?>">
        <div class="vc_empty_space_inner_span_claim"><?php echo sprintf( esc_html__( 'Claim %s', 'wyzi-business-finder' ), get_the_title( $_GET['id'] ) );?></div>
    </div>
<div class="wyz_claim_form_main">
    <div class="clami_img"><?php echo get_the_post_thumbnail( $_GET['id'] );?></div>
    <?php
    $sep_count = 0; 
    foreach ( $wyz_claim_registration_form_data as $key => $value ) {
        switch ($value['type']) {
            case 'separator':
                ?>
                <div class="clearboth"></div>
                </div>
                <div class="cmb-row">
                <h3><?php echo $value['label']; ?></h3>
                <?php
                break;
            case 'textbox':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wyz_claim_cont'; } ?>">
                    <label><?php echo $value['label']; ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="text" value="<?php if (!empty($_POST['wyzi_claim_fields'][$key]["value"])) echo esc_attr($_POST['wyzi_claim_fields'][$key]["value"]); ?>" name="wyzi_claim_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][type]" value="textbox" />
                </div>
                <?php
                break;
            case 'email':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wyz_claim_cont'; } ?>">
                    <label><?php echo $value['label']; ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="email" value="<?php if (!empty($_POST['wyzi_claim_fields'][$key]["value"])) echo esc_attr($_POST['wyzi_claim_fields'][$key]["value"]); ?>" name="wyzi_claim_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][type]" value="email" />
                </div>
                <?php
                break;
            case 'textarea':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wyz_claim_cont'; } ?>">
                    <label class="text-area-label"><?php echo $value['label']; ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <textarea <?php if(!empty($value['limit'])){ echo 'maxlength="'.$value['limit'].'"'; } ?> name="wyzi_claim_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['defaultValue']; ?>"><?php if (!empty($_POST['wyzi_claim_fields'][$key]["value"])){ echo esc_attr($_POST['wyzi_claim_fields'][$key]["value"]); } ?></textarea>
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][type]" value="textarea" />
                </div>
                <?php
                break;
            case 'url': 
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wyz_claim_cont'; } ?>">
                    <label><?php echo $value['label']; ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="url" value="<?php if (!empty($_POST['wyzi_claim_fields'][$key]["value"])) echo esc_attr($_POST['wyzi_claim_fields'][$key]["value"]); ?>" name="wyzi_claim_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][type]" value="url" />
                </div>
                <?php
                break;
            case 'selectbox':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'claim-section-box'; } ?>">
                    <label><?php echo $value['label']; ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][type]" value="selectbox" />
                    <?php
                     switch ($value['selecttype']){
                         case 'dropdown':
                            ?>
                            <select class="select_box" name="wyzi_claim_fields[<?php echo $key; ?>][value]" <?php if($value['required']){ echo 'required="required"'; }?>>
                            <?php
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <option value="<?php echo $option_value['value']; ?>" <?php if($option_value['selected']){ echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                            </select>
                             <?php
                             break;
                         case 'radio':
                             if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <p> <input type="radio" <?php if($option_value['selected']){ echo 'checked="checked"'; } ?> name="wyzi_claim_fields[<?php echo $key; ?>][value]" value="<?php echo $option_value['value']; ?>"> <?php echo $option_value['label']; ?></p>
                                    <?php
                                }
                            }
                             break;
                         case 'checkboxes':
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <p> <input type="checkbox" <?php if($option_value['selected']){ echo 'checked="checked"'; } ?> name="wyzi_claim_fields[<?php echo $key; ?>][value][]" value="<?php echo $option_value['value']; ?>"> <?php echo $option_value['label']; ?></p>
                                    <?php
                                }
                            }
                             break;
                         case 'multi-select':
                             ?>
                            <select class="select_box" style="min-height: 59px;" name="wyzi_claim_fields[<?php echo $key; ?>][value][]" <?php if($value['required']){ echo 'required="required"'; }?> multiple="">
                            <?php
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <option value="<?php echo $option_value['value']; ?>" <?php if($option_value['selected']){ echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                            </select>
                            <?php
                            break;
                     }
                    ?>
                </div>
                <?php
                break;

            case 'checkbox':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wyz_claim_cont'; } ?>">
                    <input type="checkbox" name="wyzi_claim_fields[<?php echo $key; ?>][value]" <?php if($value['defaultValue'] == 'checked'){ echo 'checked="checked"';} ?>  <?php if($value['required']){ echo 'required="required"'; }?> />
                    <label><?php echo $value['label']; ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][type]" value="checkbox" />
                </div>
                <?php
                break;
            case 'recaptcha':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wyz_claim_cont'; } ?>">
                    <label><?php echo $value['label']; ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <?php echo $value['script']; ?>
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][value]" value="Verified" />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][type]" value="checkbox" />
                </div>
                <?php
                break;
            case 'file':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wyz_claim_cont'; } ?>">
                    <label><?php echo $value['label']; ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="file" name="wyzi_claim_fields[<?php echo $key; ?>][]" <?php if($value['required']){ echo 'required="required"'; }?> <?php if($value['muliple']){ echo 'multiple="true"'; }?> />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wyzi_claim_fields[<?php echo $key; ?>][type]" value="file" />
                </div>
                <?php
                break;
        }
        $sep_count++;
    } ?>
    <input type="hidden" value="<?php if (isset($_GET['id'])) echo esc_attr($_GET['id']); ?>" name="wyzi_claim_fields[<?php echo $sep_count; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" />
    <input type="hidden" name="wyzi_claim_fields[<?php echo $sep_count; ?>][label]" value="Business ID Claimed" />
    <input type="hidden" name="wyzi_claim_fields[<?php echo $sep_count; ?>][type]" value="textbox" />
    <input type="submit" class="wyz-primary-color wyz-prim-color btn-square" name="register" value="<?php esc_html_e('Claim this business','wyzi-business-finder'); ?>" <?php echo $user_is_owner_of_business ? 'disabled' : ''; ?> Title="<?php $user_is_owner_of_business ? esc_html_e('You cannot claim your own Business','wyzi-business-finder') : ''; ?>" />
      </div>
      </form>

      <?php if ($user_is_owner_of_business) { ?>
      <div class="wyz-info"><p><?php echo esc_html__( 'You cannot claim your own Business', 'wyzi-business-finder' ); ?></p></div>
      <?php } ?>

    <?php
    
}