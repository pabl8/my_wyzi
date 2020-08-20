<?php
/**
 * Businesses'Add product Front End
 *
 * @package wyz
 */

/**
 * Display the Product Form
 *
 * 
 */
if (!defined('ABSPATH'))
    exit;

global $wyz_product_id;




if ( ! function_exists('wyz_product_frontend') ) {
function wyz_product_frontend() {
	global $wyz_product_id;
	$product = null;
	if ( 'product' == get_post_type( $wyz_product_id ) ) {
		$product = get_post( $wyz_product_id );
	}

	$wc_product = wc_get_product($wyz_product_id);

	if(!$wc_product||is_wp_error($wc_product))$wc_product=null;

	$title = !$product ? '' : get_the_title($wyz_product_id);
	$stock_status = !$product ? '' : get_post_meta($wyz_product_id, '_stock_status', true);
	$regular_price = !$product ? '' : get_post_meta($wyz_product_id, '_regular_price', true);
	$price = !$product ? '' : get_post_meta($wyz_product_id, '_price', true);
	$sale_price = !$product ? '' : get_post_meta($wyz_product_id, '_sale_price', true);
	$purchase_note = !$product ? '' : get_post_meta($wyz_product_id, '_purchase_note', true);
	$weight = !$product ? '' : get_post_meta($wyz_product_id, '_weight', true);
	$length = !$product ? '' : get_post_meta($wyz_product_id, '_length', true);
	$width = !$product ? '' : get_post_meta($wyz_product_id, '_width', true);
	$height = !$product ? '' : get_post_meta($wyz_product_id, '_height', true);
	$sku = !$product ? '' : get_post_meta($wyz_product_id, '_sku', true);
	$sold_individually = !$product ? '' : get_post_meta($wyz_product_id, '_sold_individually', true);
	$menu_order = $product ? $product->menu_order : '';
	$post_content = $product ? $product->post_content : '';
	$post_excerpt = $product ? $product->post_excerpt : '';
	$comment_status = $product ? $product->comment_status : '';
	$manage_stock = !$product ? '' : get_post_meta($wyz_product_id, '_manage_stock', true);
	$stock = !$product ? '' : get_post_meta($wyz_product_id, '_stock', true);
	$backorders = !$product ? '' : get_post_meta($wyz_product_id, '_backorders', true);
	$productdata_options = !$product ? array() : get_post_meta($wyz_product_id, 'wc_productdata_options', true);
	$productdata_options = !empty($productdata_options)?$productdata_options[0]:$productdata_options;
	$product_cat = !$product ? array() : wp_get_object_terms( $wyz_product_id, 'product_cat', array( 'fields' => 'ids' ) );
	$product_tag = !$product ? array() : wp_get_object_terms( $wyz_product_id, 'product_tag', array( 'fields' => 'names' ) );
	$thumbnail_id = !$product ? array() : get_post_meta($wyz_product_id, '_thumbnail_id', true);
	$thumb_name = ! empty( $thumbnail_id ) ? get_the_title( $thumbnail_id ) : '';
	$product_attributes = !$product ? array() : get_post_meta($wyz_product_id, '_product_attributes', true);
	$image_gallery = !$product ? array() : get_post_meta($wyz_product_id, '_product_image_gallery', true);
	$business_id = !$product ? array() : get_post_meta($wyz_product_id, 'business_id', true);
	$booked_appointment = !$product ? false : 'yes' == get_post_meta($wyz_product_id, '_booked_appointment', true);
	?>
	 
	 <div id="ns-container-add-product-frontend">
		<form name="form1" action="" method="post" class="" enctype="multipart/form-data">
			<div id="ns-product-data-container" class="ns-big-box">
				<div class="ns-center">
					<h2><span><?php
	echo esc_html__('Product Data', 'wyzi-business-finder');
	?></span></h2> <span type='button' id='ns-post-prod-data-hide-show' class="dashicons dashicons-arrow-down ns-pointer"></span>
				</div>
				<div id="ns-product-data-inner-container" class="ns-border-margin">
					<div class="ns-left-list-data-container">
						<ul id="right-menu-product">
							<li id="ns-general" class="ns-active"><a href="#ns-prod-data" class="ns-link"><?php
	echo esc_html__('General', 'wyzi-business-finder');
	?></a></li>
							<li id="ns-inventory"><a href="#ns-prod-data" class="ns-link"><?php
	echo esc_html__('Inventory', 'wyzi-business-finder');
	?></a></li>
							<li id="ns-shipping"><a href="#ns-prod-data" class="ns-link"><?php
	echo esc_html__('Shipping', 'wyzi-business-finder');
	?></a></li>
							
							<li id="ns-attributes"><a href="#ns-prod-data" class="ns-link"><?php
	echo esc_html__('Attributes', 'wyzi-business-finder');
	?></a></li>
							<li id="ns-advanced"><a href="#ns-prod-data" class="ns-link"><?php
	echo esc_html__('Advanced', 'wyzi-business-finder');
	?></a></li>
							<li id="ns-extra"><a href="#ns-prod-extra" class="ns-link"><?php
	echo esc_html__('Extra', 'wyzi-business-finder');
	?></a></li>
						</ul>
					</div>
					<div class="ns-prod-data-tab ns-general">
						<div>
						
						<div><label><?php
	echo esc_html__('Product Name', 'wyzi-business-finder');
	?></label> <br><input class="ns-input-width" name="ns-product-name" id="ns-product-name" value="<?php echo $title;?>" placeholder="<?php
	echo esc_html__('Product name', 'wyzi-business-finder');
	?>" type="text" required="true"></div>
							<div><label><?php
	echo esc_html__('Product Listing', 'wyzi-business-finder');
	?></label> <br>
	<?php
	global $post;

	$temp_post = $post;

	$curr_owner_bus_id = get_current_user_id();

	$args = array(
	    'post_type' => 'wyz_business',
	    'post_status' => array( 'publish', 'pending' ),
	    'posts_per_page' => -1,
	    'fields' => 'ids',
	    'author' => $curr_owner_bus_id
	);

	$query = new WP_Query($args);

	$output = '';
	// Loop through all available published businesses
	if ($query->have_posts()) {
	    $output = '' . '

			<select d="ns-stock-status" name="ns-listing-connected" class="ns-input-width">';
	    while ($query->have_posts()) {
	        $query->the_post();
	        $bus_id = get_the_ID();
	        $output .= '<option value="' . $bus_id . '" ' . ($curr_owner_bus_id == $bus_id ? 'selected="selected"' : '') . '>' . get_the_title() . " - $bus_id</option>";
	    }
	?>
			
		<?php
	    ;
	}
	echo $output . '</select>';
	wp_reset_postdata();

	$post = $temp_post;


	?>
							</div>
							<?php if ( 'off' != get_option( 'wyz_vendors_can_booking_products' ) ) { ?>
							<div><label><?php
	echo esc_html__('Is this a Booking Service Product?', 'wyzi-business-finder');
	?> </label><br>
							<select id="ns-booked_appointment" name="ns-booked_appointment" class="ns-input-width" >
									<option value="no" <?php echo !$booked_appointment ? 'selected="selected"' : '';?>><?php
	echo esc_html__('No', 'wyzi-business-finder');
	?></option>
									<option value="yes" <?php echo $booked_appointment ? 'selected="selected"' : '';?>><?php
	echo esc_html__('Yes', 'wyzi-business-finder');
	?></option>
								</select>
							<br></div>
						<?php }?>
							<div><label><?php
	echo esc_html__('Regular price', 'wyzi-business-finder');
	?> (<?php
	echo get_woocommerce_currency_symbol();
	?>)</label> <br><input class="ns-input-width" name="ns-regular-price" id="ns-regular-price" value="<?php echo $regular_price;?>" placeholder="4.20" type="text" pattern="[0-9]+([\.][0-9]+)?" title="This should be a number with up to 2 decimal places."></div>
							<div><label><?php
	echo esc_html__('Sale price', 'wyzi-business-finder');
	?> (<?php
	echo get_woocommerce_currency_symbol();
	?>)</label> <br><input class="ns-input-width" name="ns-sale-price" id="ns-sale-price" value="<?php echo $sale_price;?>" placeholder="3.00" type="text" pattern="[0-9]+([\.][0-9]+)?" title="This should be a number with up to 2 decimal places."></div>
						</div>
					</div>
					<div class="ns-prod-data-tab ns-inventory ns-hidden">
						<div>
							<div>
								<label><?php
	echo esc_html__('SKU', 'wyzi-business-finder');
	?></label><br> <input class="ns-input-width" name="ns-sku" id="ns-sku" value="<?php echo $sku;?>" placeholder="" type="text">
							</div>
							<div>
								<label><?php
	echo esc_html__('Manage Stock?', 'wyzi-business-finder');
	?></label> <input name="ns-manage-stock" id="ns-manage-stock" value="no"<?php echo $manage_stock ? 'checked="checked"' : '';?> type="checkbox"><br> <span class="ns-add-product-frontend-span-text"><?php
	echo esc_html__('Enable stock management at product level', 'wyzi-business-finder');
	?></span>
							</div>
							<div id="ns-manage-stock-div" style="display: none;">
								<div>
									<label><?php
	echo esc_html__('Stock quantity', 'wyzi-business-finder');
	?></label><br><input class="" value="<?php echo $stock;?>" name="ns-stock" id="ns-stock" step="any" type="number"> 
								</div>
								<div class="">
								<label><?php
	echo esc_html__('Allow backorders?', 'wyzi-business-finder');
	?></label>
									<select id="ns-backorders" name="ns-backorders" class="">
										<option value="no"<?php echo 'no' == $backorders ? ' selected="selected"' : '';?>><?php
	echo esc_html__('Do not allow', 'wyzi-business-finder');
	?></option>
										<option value="notify"<?php echo 'notify' == $backorders ? ' selected="selected"' : '';?>><?php
	echo esc_html__('Allow, but notify customer', 'wyzi-business-finder');
	?></option>
										<option value="yes"<?php echo 'yes' == $backorders ? ' selected="selected"' : '';?>><?php
	echo esc_html__('Allow', 'wyzi-business-finder');
	?></option>
									</select> 
								</div>
							</div>
							<div>
								<label><?php
	echo esc_html__('Stock Status', 'wyzi-business-finder');
	?></label><br>
								<select id="ns-stock-status" name="ns-stock-status" class="ns-input-width" >
									<option value="instock"<?php echo 'instock' == $stock_status ? ' selected="selected"' : '';?>><?php
	echo esc_html__('In stock', 'wyzi-business-finder');
	?></option>
									<option value="outofstock"<?php echo 'outofstock' == $stock_status ? ' selected="selected"' : '';?>><?php
	echo esc_html__('Out of stock', 'wyzi-business-finder');
	?></option>
								</select>
							</div>
							<div>
								<div style="margin-left: 0px;"><label><?php
	echo esc_html__('Sold individually', 'wyzi-business-finder');
	?> </label><input class="checkbox" name="ns-sold-individually" id="ns-sold-individually" value="yes" type="checkbox" <?php echo $sold_individually ? ' checked="checked"' : '';?>><br><span class="ns-add-product-frontend-span-text"><?php
	echo esc_html__('Enable this to only allow one of this item to be bought in a single order', 'wyzi-business-finder');
	?></span></div>
							</div>
						</div>				
					</div>

					<div class="ns-prod-data-tab ns-shipping ns-hidden">
						<div class="">
							<div><label><?php
	echo sprintf( esc_html__('Weight (%s)', 'wyzi-business-finder'), get_option( 'woocommerce_weight_unit', 'kg' ) );
	?></label><br><input class="ns-input-width" name="ns-weight" id="ns-weight" placeholder="0" value="<?php echo $weight;?>" type="text"></div>								
							<div><label><?php
	echo esc_html__('Dimensions (cm)', 'wyzi-business-finder');
	?></label><div style="margin-left: 0px;"><input class="ns-input-width" id="ns-product-length" placeholder="<?php echo esc_html__('Length', 'wyzi-business-finder');?>" size="6" name="ns-product-length" value="<?php echo $length;?>"  type="text"><br><input class="ns-input-width" placeholder="<?php
	echo esc_html__('Width', 'wyzi-business-finder');
	?>" size="6" id="ns-width" name="ns-width" value="<?php echo $width;?>"  type="text"><br><input class="ns-input-width" placeholder="<?php
	echo esc_html__('Height', 'wyzi-business-finder');
	?>" size="6" id="ns-height" name="ns-height" value="<?php echo $height;?>"  type="text"><hr></div>		</div>


			<div><label><?php echo esc_html__('Shipping class', 'wyzi-business-finder');?></label>
				<div style="margin-left: 0px;">
					<select class="ns-input-width" id="product_shipping_class" name="product_shipping_class">
					<?php 
					$current_user_id = get_current_user_id();
					//$shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );

					$product_shipping_class = get_terms('product_shipping_class', array('hide_empty' => 0));
					$shipping_classes = array('_no_shipping_class' => __('No shipping class', 'dc-woocommerce-multi-vendor'));
					foreach ($product_shipping_class as $product_shipping) {
					    if (apply_filters('wcmp_allowed_only_vendor_shipping_class', true)) {
					        $vendor_id = get_woocommerce_term_meta($product_shipping->term_id, 'vendor_id', true);
					        if (!$vendor_id) {}
					        else {
					            if ($vendor_id == $current_user_id) {
					                $variation_shipping_option_array[$product_shipping->term_id] = $product_shipping->name;
					                $shipping_classes[$product_shipping->term_id] = $product_shipping->name;
					            }
					        }
					    } else {
					        $shipping_classes[$product_shipping->term_id] = $product_shipping->name;
					    }
					}


					$old_id = $wc_product ? $wc_product->get_shipping_class_id() : -1;
					echo '<option value=""></option>';
					foreach ($shipping_classes as $id => $label) {
						echo '<option value="'.$id.'"' . ($id == $old_id ? 'selected' : '') . '>'.$label.'</option>';
					}?>
					</select>
					<br>
				</div>
			</div>		


						</div>
						
					</div>

					<div class="ns-prod-data-tab ns-attributes ns-hidden">
						<div id="ns-inner-attributes">
							<select id="ns-attribute-taxonomy" name="ns-attribute-taxonomy" class="ns-attribute-taxonomy ns-input-width">
								<option value="ns-cus-prod-att"><?php
	echo esc_html__('Custom product attribute', 'wyzi-business-finder');
	?></option>					
								<?php $terms = wc_get_attribute_taxonomies();
								foreach ($terms as $term) {
									echo '<option value="'.$term->attribute_name.'">'.$term->attribute_label.'</option>';
								}?>
							</select><br>
							
							<button id="ns-add-attribute-btn" type="button" class="button"><?php
	echo esc_html__('Add', 'wyzi-business-finder');
	?></button>
							<?php 
							$ii =0;
							if ( is_array( $product_attributes ) && ! empty( $product_attributes ) ) {
								foreach ($product_attributes as $i => $pa ) {
									if ( isset( $pa['name'] ) && isset( $pa['value'] ) ){ $ii = $i+1;?>
								<div>
									<div>
										<label>Name:</label><br>
										<input class="ns-input-width" name="ns-attr-names<?php echo $i;?>" id="ns-attr-names<?php echo $i;?>" value="<?php echo $pa['name'];?>" type="text"/>
									</div>
									<div>
										<label>Value(s)</label>
										<textarea name="ns-attribute-values<?php echo $i;?>" placeholder="Enter some text, or some attributes by &quot;|&quot; separating values."><?php echo $pa['value'];?></textarea>
									</div>
									<div>
										<label>Visible on product page </label>
										<input class="checkbox" name="ns-attr-visibility-status<?php echo $i;?>" id="ns-attr-visibility-status<?php echo $i;?>" <?php echo $pa['is_visible'] ? 'checked="checked"' : '';?> type="checkbox"/>
									</div>
									<button id="ns-attribute-btn-remove" type="button" class="button" style="float:left">Remove</button>
								</div>
									<?php }
								}
							}?>
							<input id="ns-attribute-list" name="ns-attribute-list" value="<?php echo $ii;?>" type="hidden" />
						</div>
						
					</div>
					<div class="ns-prod-data-tab ns-advanced ns-hidden">
						<div>
							<label><?php
	echo esc_html__('Purchase note', 'wyzi-business-finder');
	?></label><textarea name="ns-purchase-note" id="ns-purchase-note" ><?php echo $purchase_note;?></textarea>			
						</div>
						<div>
							<label><?php
	echo esc_html__('Menu order', 'wyzi-business-finder');
	?></label><br><input class="ns-input-width" name="ns-menu-order" id="ns-menu-order" placeholder="" value="<?php echo $menu_order;?>" step="1" type="number">
						</div>
						<div>
							<label><?php
	echo esc_html__('Enable reviews', 'wyzi-business-finder');
	?></label><input class="checkbox" name="ns-comment-status" id="ns-comment-status"<?php echo $comment_status ? ' checked="checked"' : '';?> type="checkbox">				
						</div>
					</div>
					<div class="ns-prod-data-tab ns-extra ns-hidden">
						<div id="ns-wc-productdata-options-tab">
							
							<div><label><?php
	echo esc_html__('Custom Bubble Title', 'wyzi-business-finder');
	?></label><br><input class="ns-input-width" name="ns-bubble-text" id="ns-bubble-text" value="<?php echo isset( $productdata_options['_bubble_text'] ) ? $productdata_options['_bubble_text'] : '';?>" placeholder="<?php
	echo esc_html__('NEW', 'wyzi-business-finder');
	?>" type="text"></div>
							<div><label><?php
	echo esc_html__('Custom Tab Title', 'wyzi-business-finder');
	?></label><br><input class="ns-input-width" name="ns-custom-tab" id="ns-custom-tab" placeholder="" value="<?php echo isset( $productdata_options['_custom_tab_title'] ) ? $productdata_options['_custom_tab_title'] : '';?>"  type="text"></div>
							<div><label><?php
	echo esc_html__('Custom Tab Content', 'wyzi-business-finder');
	?></label><textarea  id="ns-cus-tab-content" name="ns-cus-tab-content" class="short" placeholder="<?php
	echo esc_html__('Enter content for custom product tab here. Shortcodes are allowed', 'wyzi-business-finder');
	?>"><?php echo isset( $productdata_options['_custom_tab'] ) ? $productdata_options['_custom_tab'] : '';?></textarea></div>
							<div><div style="margin-left: 0px;"><label><?php
	echo esc_html__('Product Video', 'wyzi-business-finder');
	?></label><br><input id="ns-video" name="ns-video" class="short ns-input-width" placeholder="<?php
	echo esc_html__('https://www.youtube.com/watch?v=Ra_iiSIn4OI', 'wyzi-business-finder');
	?>" type="text" value="<?php echo isset( $productdata_options['_product_video'] ) ? $productdata_options['_product_video'] : '';?>"><br><span class="ns-add-product-frontend-span-text"><?php
	echo esc_html__('Enter a Youtube or Vimeo Url of the product video here. We recommend uploading your video to Youtube.', 'wyzi-business-finder');
	?></span></div></div>
							<div><label><?php
	echo esc_html__('Product Video Size', 'wyzi-business-finder');
	?></label><br><input id="ns-video-size" name="ns-video-size" class="ns-input-width" placeholder="<?php
	echo esc_html__('900x900', 'wyzi-business-finder');
	?>" type="text" value="<?php echo isset( $productdata_options['_product_video_size'] ) ? $productdata_options['_product_video_size'] : '';?>"></div>
							<div><label><?php
	echo esc_html__('Top Content', 'wyzi-business-finder');
	?></label><textarea id="ns-top-content" name="ns-top-content" placeholder="<?php
	echo esc_html__('Enter content that will show after the header and before the product. Shortcodes are allowed', 'wyzi-business-finder');
	?>"><?php echo isset( $productdata_options['_top_content'] ) ? $productdata_options['_top_content'] : '';?></textarea></div>
							<div><label><?php
	echo esc_html__('Bottom Content', 'wyzi-business-finder');
	?></label><textarea id="ns-bottom-content" name="ns-bottom-content" placeholder="<?php
	echo esc_html__('Enter content that will show after the product info. Shortcodes are allowed', 'wyzi-business-finder');
	?>"><?php echo isset( $productdata_options['_bottom_content'] ) ? $productdata_options['_bottom_content'] : '';?></textarea></div>
						</div>
					</div>
				</div>
			</div>
			<div id="ns-post-content" class="ns-big-box">
				<div>
					<h2><?php
	echo esc_html__('Post Content', 'wyzi-business-finder');
	?></h2><span type='button' id='ns-post-content-hide-show' class="dashicons dashicons-arrow-down ns-pointer"></span>
				</div>
				<div id="ns-wp-post-content-div" class="ns-border-margin ns-padding-container">
					<p class="ns-add-product-frontend-span-text"><?php
	echo esc_html__('Here you can add the complete description of your product', 'wyzi-business-finder');
	?></p>
					<textarea id="ns-post-content-text" name="ns-post-content-text" class="ns-display-block"><?php echo $post_content;?></textarea>
				</div>
			</div>
			<div id="ns-short-desc-container" class="ns-big-box">
				<div>
					<h2><?php
	echo esc_html__('Product Short Description', 'wyzi-business-finder');
	?></h2><span type='button' id='ns-short-desc-hide-show' class="dashicons dashicons-arrow-down ns-pointer"></span>
				</div>
				<div id="ns-wp-editor-div" class="ns-border-margin ns-padding-container">
					<p class="ns-add-product-frontend-span-text"><?php
	echo esc_html__('Here you can add a short description to your product', 'wyzi-business-finder');
	?></p>
					<textarea id="ns-short-desc-text" name="ns-short-desc-text" class="ns-display-block"><?php echo $post_excerpt;?></textarea>
				</div>
			</div>
			
			<div class="ns-left ns-little-container">
				<div id="ns-product-tags" class="ns-little-box ns-margin-right">
					<div>
						<h2><?php
	echo esc_html__('Product Tags', 'wyzi-business-finder');
	?></h2><span type='button' id='ns-prod-tags-hide-show' class="dashicons dashicons-arrow-down ns-pointer"></span>
					</div>
					<div id="ns-prod-tags-div" class="ns-padding-container ns-border-margin">
						<div><input id="ns-new-tag-product" name="ns-new-tag-product"  size="16" value="<?php echo !empty( $product_tag ) ? implode(', ', $product_tag ) : '';?>" type="text"></div>
						<div>
							<p class="ns-add-product-frontend-span-text"><?php
	echo esc_html__('Separate Product Tags with commas', 'wyzi-business-finder');
	?></p>
						</div>
					</div>
				</div>
				<div id="ns-image-container" class="ns-little-box">
					<div>
						<h2><?php
	echo esc_html__('Product Image', 'wyzi-business-finder');
	?></h2><span type='button' id='ns-prod-image-hide-show' class="dashicons dashicons-arrow-down ns-pointer"></span>
					</div>
					<div id = "ns-image-container-0"class="ns-border-margin ns-padding-container">
						<div id="ns-image-container1">
							<div class="ns-margin-top"><p>
								<input id="thumb-upload-button" type="button" value="<?php esc_html_e( 'add/remove image', 'wyzi-business-finder' );?>" />
								</p>
								<div class="attch-meta"><p id="attch-name"><?php echo $thumb_name;?></p><a href="#" id="attch-rmv">X</a></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="ns-left ns-little-container">
				<div id="ns-product-categories" class="ns-little-box ns-margin-right">
					<div>
						<h2><?php
	echo esc_html__('Product Categories', 'wyzi-business-finder');
	?></h2><span type='button' id='ns-prod-categories-hide-show' class="dashicons dashicons-arrow-down ns-pointer"></span>
					</div>
					<div id="ns-prod-cat-inner" class="ns-border-margin ns-padding-container">
						<div>
						<?php
	// Lets exculude Points-Category from Displaying to Busienss Owners
	$points_category_term = get_term_by( 'slug','points-category','product_cat');
	$points_category_term_id = '';
	if ($points_category_term) $points_category_term_id = $points_category_term->term_id;
	
	$all_existent_cat = get_terms(array(
	    'taxonomy' => 'product_cat',
	    'hide_empty' => false,
	    'exclude'    => array($points_category_term_id),
	));
	?>
							<table class="table-product-categories">
							<?php
	foreach ($all_existent_cat as $cat_obj) {
	    echo '<tr>';
	    echo '<td>';
	    echo $cat_obj->name . '<input id="ns-add-product-frontend-ca-checkbox" type="checkbox" name="' . $cat_obj->name . '"' . ( in_array( $cat_obj->term_id, $product_cat ) ? ' checked="checked"' : '' ) . ' class="ns-add-product-frontend-ca-checkbox" value="' . $cat_obj->name . '"/>';
	    echo '</td>';
	    echo '</tr>';
	    
	}
	?>
								
							</table>
						</div>
					</div>
				</div>
				<div id="ns-product-gallery" class="ns-little-box">
					<div>
						<h2><?php
	echo esc_html__('Product Gallery', 'wyzi-business-finder');
	?></h2><span id='ns-prod-gallery-hide-show' class="dashicons dashicons-arrow-down ns-pointer"></span>
					</div>
					<div id="ns-prod-gallery-inner" class="ns-border-margin ns-padding-container">
						<div>
							<p class="ns-add-product-frontend-span-text"><?php
	echo esc_html__('Add product gallery images', 'wyzi-business-finder');
	?></p>
			<input id="gallery-upload-button" type="button" value="<?php esc_html_e( 'add/remove images', 'wyzi-business-finder' );?>" />
							<!-- <input type="file" name="ns_gallery[]" id="ns_image_files" style="width:90%; margin-left:5px;" multiple> -->
						</div>
					</div>
					
					<input id="ns-image-from-list" name="ns-image-from-list" type="hidden" value="<?php echo $image_gallery;?>" />
					<input id="ns-image-from-thumb" name="ns-image-from-thumb" type="hidden" value="<?php echo $thumbnail_id;?>" />
					<input id="ns-attr-from-list" name="ns-attr-from-list" type="hidden" value="" />
				</div>
			</div>
			<button type="submit" id="new-product-save" class="wyz-primary-color wyz-prim-color btn-square" name="submit"><?php
	echo esc_html__('Save', 'wyzi-business-finder');
	?></button>			
	</div>

	</form>	


	<?php

}
}


if (isset($_GET[ WyzQueryVars::AddProduct ]) && isset($_GET['action']) && 'edit' == $_GET['action']) {
	
    if (!current_user_can('edit_post', $_GET[ WyzQueryVars::AddProduct ])) {
        WyzHelpers::wyz_error(esc_html__('You don\'t have the right to edit this product.', 'wyzi-business-finder'));
    } else {
    	$wyz_product_id = $_GET[ WyzQueryVars::AddProduct ];
        wyz_product_frontend();
    }
} else {
    wyz_product_frontend();
}


if (isset($_POST['submit'])) {
	$saved_id = ns_save_product();
    if (!$saved_id) { //error found, return empty html;
        echo esc_html__('Error: cannot add product.', 'wyzi-business-finder');
        
    } else {
		$product_status = get_post_status( $saved_id );
		if ($product_status == 'publish')
			WyzHelpers::wyz_success(esc_html__('Your product has been added.', 'wyzi-business-finder'));
		else
			WyzHelpers::wyz_success(esc_html__('Your product has been submitted for review.', 'wyzi-business-finder'));
    }
}

function ns_save_product()
{
    /*Create a new post*/
    $post_id = ns_save_post();
    if (is_wp_error($post_id) || ! $post_id) {
        return false;
    }
    
    /*Product data*/
    $regular_price = null;
    if (isset($_POST["ns-regular-price"])) {
        if (is_numeric($_POST["ns-regular-price"]) || $_POST["ns-regular-price"] == '') {
            $regular_price = sanitize_text_field($_POST["ns-regular-price"]);
        } else {
            wp_delete_post($post_id, true);
            return false;
        }
    }
    $sale_price = null;
    if (isset($_POST["ns-sale-price"])) {
        if (is_numeric($_POST["ns-sale-price"]) || $_POST["ns-sale-price"] == '') {
            $sale_price = sanitize_text_field($_POST["ns-sale-price"]);
        } else {
            wp_delete_post($post_id, true);
            return false;
        }
    }
    $sku = null;
    if (isset($_POST["ns-sku"])) {
        $sku = sanitize_text_field($_POST["ns-sku"]);
    }
    
    $manage_stock      = null;
    $stock_quantity    = null;
    $stock_back_orders = "no";
    if (isset($_POST["ns-manage-stock"])) {
        $manage_stock      = sanitize_text_field($_POST["ns-manage-stock"]);
        $stock_quantity    = sanitize_text_field($_POST["ns-stock"]);
        $stock_back_orders = sanitize_text_field($_POST["ns-backorders"]);
    }
    
    $stock_status = null;
    if (isset($_POST["ns-stock-status"])) {
        $stock_status = $_POST["ns-stock-status"];
    }
    
    $sold_individually = null;
    if (isset($_POST["ns-sold-individually"])) {
        $sold_individually = $_POST["ns-sold-individually"];
    }
    
    $weight = null;
    if (isset($_POST["ns-weight"])) {
        $weight = sanitize_text_field($_POST["ns-weight"]);
    }
    
    $length = null;
    if (isset($_POST["ns-product-length"])) {
        $length = sanitize_text_field($_POST["ns-product-length"]);
    }
    
    $width = null;
    if (isset($_POST["ns-width"])) {
        $width = sanitize_text_field($_POST["ns-width"]);
    }
    
    $height = null;
    if (isset($_POST["ns-height"])) {
        $height = sanitize_text_field($_POST["ns-height"]);
    }

    $shipping_class=null;
    if (isset($_POST["product_shipping_class"])) {
        $shipping_class = absint( $_POST['product_shipping_class'] );
    }
    
    
    $purchase_note = null;
    if (isset($_POST["ns-purchase-note"])) {
        $purchase_note = sanitize_text_field($_POST["ns-purchase-note"]);
    }
    
    if ($stock_status)
        update_post_meta($post_id, '_stock_status', $stock_status);
    if ($regular_price)
        update_post_meta($post_id, '_regular_price', $regular_price);
    update_post_meta($post_id, '_price', $regular_price);
    if (!empty($sale_price)) {
        update_post_meta($post_id, '_sale_price', $sale_price);
        update_post_meta($post_id, '_price', $sale_price);
    }

    if ($purchase_note)
        update_post_meta($post_id, '_purchase_note', $purchase_note);
    
    update_post_meta($post_id, '_featured', "no");
    if ($weight)
        update_post_meta($post_id, '_weight', $weight);
    if ($length)
        update_post_meta($post_id, '_length', $length);
    if ($width)
        update_post_meta($post_id, '_width', $width);
    if ($height)
        update_post_meta($post_id, '_height', $height);
    if( $shipping_class ) {
    	wp_set_post_terms( $post_id, array($shipping_class) , 'product_shipping_class' );
    	$prdct = wc_get_product( $post_id );
	    $prdct->set_props( array('shipping_class_id'  => $shipping_class ) );
    }
    if ($sku)
        update_post_meta($post_id, '_sku', $sku);
    
    update_post_meta($post_id, '_sale_price_dates_from', "");
    update_post_meta($post_id, '_sale_price_dates_to', "");
    
    if ($sold_individually)
        update_post_meta($post_id, '_sold_individually', $sold_individually);
    
    if ($manage_stock == "yes") {
        update_post_meta($post_id, '_manage_stock', $manage_stock);
        update_post_meta($post_id, '_stock', $stock_quantity);
        update_post_meta($post_id, '_backorders', $stock_back_orders);
    }
    
    update_post_meta($post_id, '_visibility', 'visible');
    update_post_meta($post_id, 'total_sales', '0');
    
    
    /*Bubbles*/
    ns_save_bubble($post_id);
    
    /*Categories*/
    ns_save_categories($post_id);
    
    /*Tags*/
    ns_save_tags($post_id);
    
    /*Images*/
    $ns_attachment_id = ns_add_image($post_id);
    
    
    if ($ns_attachment_id)
        update_post_meta($post_id, '_thumbnail_id', $ns_attachment_id);
    else
    	update_post_meta($post_id, '_thumbnail_id', '');
    
    /*Attributes*/
    ns_add_attributes($post_id);
    
    /*Gallery*/
    ns_add_gallery_images($post_id);
    
    update_post_meta($post_id, 'business_id', $_POST["ns-listing-connected"]);
    

    $vendor_term = get_user_meta(wp_get_current_user()->ID, '_vendor_term_id', true);
    $term        = get_term($vendor_term, 'dc_vendor_shop');
    if(!is_wp_error($term)){
	    wp_delete_object_term_relationships($post_id, 'dc_vendor_shop');
	    wp_set_post_terms($post_id, $term->name, 'dc_vendor_shop', true);
	}
    
    if ( 'off' != get_option( 'wyz_vendors_can_booking_products' ) ) {
    	update_post_meta($post_id, '_booked_appointment', $_POST["ns-booked_appointment"]);
    	if( $_POST["ns-booked_appointment"] != 'yes' )
			update_post_meta($post_id, '_virtual', 'yes');
    }
    $post = get_post($post_id);
    $product_status = get_post_status($post_id);
    on_all_status_transitions($product_status, 'draft', $post);
    return $post_id;
}

function on_all_status_transitions($new_status, $old_status, $post)
{
    
    if ($new_status != $old_status && $post->post_status == 'pending') {
        $current_user = get_current_user_id();
        if ($current_user)
            $current_user_is_vendor = is_user_wcmp_vendor($current_user);
        if ($current_user_is_vendor) {
            //send mails to admin for new vendor product
            $vendor      = get_wcmp_vendor_by_term(get_user_meta($current_user, '_vendor_term_id', true));
            $email_admin = WC()->mailer()->emails['WC_Email_Vendor_New_Product_Added'];
            $email_admin->trigger($post->post_id, $post, $vendor);
        }
    } else if ($new_status != $old_status && $post->post_status == 'publish') {
        
        $current_user = get_current_user_id();
        if ($current_user)
            $current_user_is_vendor = is_user_wcmp_vendor($current_user);
        if ($current_user_is_vendor) {
            //send mails to admin for new vendor product
            $vendor      = get_wcmp_vendor_by_term(get_user_meta($current_user, '_vendor_term_id', true));
            $email_admin = WC()->mailer()->emails['WC_Email_Vendor_New_Product_Added'];
            $email_admin->trigger($post->post_id, $post, $vendor);
        }
    }
    if (current_user_can('administrator') && $new_status != $old_status && $post->post_status == 'publish') {
        if (isset($_POST['choose_vendor']) && !empty($_POST['choose_vendor'])) {
            $term = get_term($_POST['choose_vendor'], 'dc_vendor_shop');
            if ($term) {
                $vendor      = get_wcmp_vendor_by_term($term->term_id);
                $email_admin = WC()->mailer()->emails['WC_Email_Admin_Added_New_Product_to_Vendor'];
                $email_admin->trigger($post->post_id, $post, $vendor);
            }
        }
    }
}

function ns_save_post()
{
    /*Checking if user is logged in*/
    
    $user_id = get_current_user_id();
    
    /*Get the inserted product title*/
    $ns_title = "New Product";
    if (isset($_POST["ns-product-name"])) {
        $ns_title = sanitize_text_field($_POST["ns-product-name"]);
    }
    
    /*Get the inserted product short description*/
    $ns_short_desc = null;
    if (isset($_POST["ns-short-desc-text"])) {
        $ns_short_desc = esc_html($_POST["ns-short-desc-text"]);
    }
    
    /*Get the inserted product post content*/
    $ns_post_content = null;
    if (isset($_POST["ns-post-content-text"])) {
        $ns_post_content = esc_html($_POST["ns-post-content-text"]);
    }
    
    /*If user wanna activate the reviews*/
    $ns_is_reviews = "closed";
    if (isset($_POST["ns-comment-status"])) {
        $ns_is_reviews = "open";
    }
    
    /*Get the menu order inserted by user*/
    $ns_menu_order = 0;
    if (isset($_POST["ns-menu-order"])) {
        $ns_menu_order = $_POST["ns-menu-order"];
    }
    
    // Check Publish Capability 
    
    $wcmp_settings = get_option('wcmp_capabilities_product_settings_name', array());
	$imm_publish = is_array( $wcmp_settings ) && array_key_exists('is_published_product', $wcmp_settings );
    
	if ( ! $imm_publish )
		$product_status = 'pending';
	else
		$product_status = 'publish';

    
    $post = array(
        'post_author' => $user_id,
        'post_content' => $ns_post_content,
        'post_status' => $product_status,
        'post_title' => $ns_title,
        'post_name' => sanitize_title( $ns_title ),
        'post_parent' => '',
        'post_type' => "product",
        'post_excerpt' => $ns_short_desc,
        'post_date'   => current_time('mysql'),
        'comment_status' => $ns_is_reviews,
        'menu_order' => $ns_menu_order
    );
    
    global $wyz_product_id;

    if ( FALSE === get_post_status( $wyz_product_id ) ) {
    	$wyz_product_id = wp_insert_post($post, true);
    } else {
    	$post['ID'] = $wyz_product_id;
    	$wyz_product_id = wp_update_post( $post );
    }
    //Create post
    return $wyz_product_id;
}


function ns_save_categories($post_id)
{
    $ns_cat_array = array();
    
    $all_existent_cat = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false
    ));
    
    foreach ($all_existent_cat as $cat_obj) {
        /*already saved categories*/
        $remove_spaces = str_replace(' ', '_', $cat_obj->name);
        if (isset($_POST[$remove_spaces])) {
            $cat = sanitize_text_field($_POST[$remove_spaces]);
            array_push($ns_cat_array, $cat);
        }
        
        /*set product categories*/
        if ($ns_cat_array) {
            wp_set_object_terms($post_id, $ns_cat_array, 'product_cat');
        }
        
    }
    
    
    
}


function ns_save_tags($post_id)
{
    /*First need to sanitize the post variables, then explode the string on the comma to have the array*/
    $ns_tags_comma = null;
    if (isset($_POST["ns-new-tag-product"]))
        $ns_tags_comma = sanitize_text_field($_POST["ns-new-tag-product"]);
    
    $ns_tags = explode(",", $ns_tags_comma);
    
    /*set the product tags*/
    if ($ns_tags) {
        wp_set_object_terms($post_id, $ns_tags, 'product_tag');
    }
    
}


function ns_save_bubble($post_id)
{
    $is_any       = false;
    
    $bubble_title = null;
    if (isset($_POST["ns-bubble-text"])) {
        $bubble_title = sanitize_text_field($_POST["ns-bubble-text"]);
        $is_any       = true;
    }
    
    $cus_tab_title = null;
    if (isset($_POST["ns-custom-tab"])) {
        $cus_tab_title = sanitize_text_field($_POST["ns-custom-tab"]);
        $is_any        = true;
    }
    
    $cus_tab_content = null;
    if (isset($_POST["ns-cus-tab-content"])) {
        $cus_tab_content = sanitize_text_field($_POST["ns-cus-tab-content"]);
        $is_any          = true;
    }
    
    $cus_tab_top = null;
    if (isset($_POST["ns-top-content"])) {
        $cus_tab_top = sanitize_text_field($_POST["ns-top-content"]);
        $is_any      = true;
    }
    
    $cus_tab_bottom = null;
    if (isset($_POST["ns-bottom-content"])) {
        $cus_tab_bottom = sanitize_text_field($_POST["ns-bottom-content"]);
        $is_any         = true;
    }
    
    $ns_video = null;
    if (isset($_POST["ns-video"])) {
        $ns_video = sanitize_text_field($_POST["ns-video"]);
        $is_any   = true;
    }
    
    $ns_video_size = null;
    if (isset($_POST["ns-video-size"])) {
        $ns_video_size = sanitize_text_field($_POST["ns-video-size"]);
        $is_any        = true;
    }
    
    if ($is_any) {
        $ns_bubble_arr = Array(
            Array(
                '_bubble_new' => "yes",
                '_bubble_text' => $bubble_title,
                '_custom_tab_title' => $cus_tab_title,
                '_custom_tab' => $cus_tab_content,
                '_product_video' => $ns_video,
                '_product_video_size' => $ns_video_size,
                '_top_content' => $cus_tab_top,
                '_bottom_content' => $cus_tab_bottom
            )
        );
        
        update_post_meta($post_id, 'wc_productdata_options', $ns_bubble_arr);
    }
    
    
}

function ns_add_image($post_id)
{
    if ( ! empty( $_POST['ns-image-from-thumb'] ) && wp_attachment_is_image( $_POST['ns-image-from-thumb'] ) )
    	return $_POST['ns-image-from-thumb'];
    return false;
    
}

function ns_save_gallery_images($post_id)
{
    
    if ($_FILES) {
        $files = $_FILES['ns_gallery'];
        foreach ($files['name'] as $key => $value) {
            if ($files['name'][$key]) {
                $file = array(
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                );
                
                $_FILES = array(
                    "agp_gallery" => $file
                );
                
                foreach ($_FILES as $file => $array) {
                    $newupload = agp_process_wooimage($file, $post_id);
                }
            }
        }
    }
    
}


function ns_add_attributes($post_id)
{
    $ns_outer_array = array();
    if (isset($_POST["ns-color-attr"])) { //There's could be only one color attribute field
        //if is set then create the array(array) 
        $color_attributes = sanitize_text_field($_POST["ns-color-attr"]);
        $is_visible       = 0;
        if (isset($_POST["ns-attr-visibility-status"])) {
            $is_visible = 1;
        }
        $ns_attr = Array(
            'name' => "pa_color",
            'value' => "",
            'position' => "0",
            'is_visible' => $is_visible,
            'is_variation' => 0,
            'is_taxonomy' => 1
        );
        
        $ns_outer_array['pa_color'] = $ns_attr; //adding the color with key 'pa_color' to let framework knows it is color
        wp_set_object_terms($post_id, $_POST["ns-color-attr"], 'pa_color', false);
        
    }

    
    if (isset($_POST['ns-attribute-list'])) { //Check if user inserted custom attributes and loop over them
        $num_custom_attr = intval(sanitize_text_field($_POST['ns-attribute-list']));
        
        if ($num_custom_attr >= 0) {
        	
            for ($i = 0; $i < $num_custom_attr; $i++) {
                $is_visible    = 1;

                $ns_attr_name  = sanitize_text_field($_POST['ns-attr-names' . $i . '']);
                $ns_attr_value = sanitize_text_field($_POST['ns-attribute-values' . $i . '']);
                
                
                if (isset($_POST['ns-attr-visibility-status' . $i . ''])) {
                    $is_visible = 1;
                }
                
                
                $ns_attr = Array(
                    'name' => $ns_attr_name,
                    'value' => $ns_attr_value,
                    'position' => "1",
                    'is_visible' => $is_visible,
                    'is_variation' => 0,
                    'is_taxonomy' => 0
                );
                array_push($ns_outer_array, $ns_attr);
            }
        }
    }

    if ($ns_outer_array)
        update_post_meta($post_id, '_product_attributes', $ns_outer_array);
    
    $arr_to_terms;
    if (isset($_POST["ns-attr-from-list"])) { //user selected an already saved color
        $arr_to_terms = explode(",", $_POST["ns-attr-from-list"]);
    }
    if (isset($_POST["ns-color-attr"])) { //user has inserted another new color
        array_push($arr_to_terms, $_POST["ns-color-attr"]);
    }
    if ($arr_to_terms) //if the array is not empty we have a new color or a already existing one
        wp_set_object_terms($post_id, $arr_to_terms, 'pa_color');

	if(isset($_POST['attribute_values_tax'])&&!empty($_POST['attribute_values_tax'])&&isset($_POST['attribute_values'])&&!empty($_POST['attribute_values'])){
		$attrs_data = array( 
    		'attribute_names' => array(),
    		'attribute_position' => array(),
    		'attribute_values' => array(),
    		'attribute_visibility' => array()
		);
		for ($i=0; $i <count($_POST['attribute_values_tax']) ; $i++) { 
			$attrs_data['attribute_names'][] = $_POST['attribute_values_tax'][$i];
			$attrs_data['attribute_values'][] = $_POST['attribute_values'][$i];
			$attrs_data['attribute_position'][] = $i;
			$attrs_data['attribute_visibility'][] = 1;
		}
		$attributes   = WC_Meta_Box_Product_Data::prepare_attributes( $attrs_data );
		$classname    = WC_Product_Factory::get_product_classname( $post_id, 'simple' );
		$product      = new $classname( $post_id );

		$product->set_attributes( $attributes );
		$product->save();

	}
}

/*Used to get all the colors already inserted by user*/
function ns_get_all_color_terms()
{
    $term_array = Array();
    $term_list  = get_terms('pa_color');
    foreach ($term_list as $classTerm) {
        array_push($term_array, $classTerm->name);
    }
    return $term_array;
}


function ns_add_gallery_images($post_id)
{
    $images_ids = null;
    if (isset($_POST["ns-image-from-list"])) {
        $images_ids = sanitize_text_field($_POST["ns-image-from-list"]);
        $valid_ids = array();
        $ids = explode(',', $images_ids);
        foreach ($ids as $id) {
        	if(wp_attachment_is_image($id))
        		$valid_ids[] = $id;
        }
        $valid_ids = implode(',', $valid_ids);
        update_post_meta($post_id, '_product_image_gallery', $valid_ids);
    }
}

?>

