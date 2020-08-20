<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( plugin_dir_path( __FILE__ ) . 'business-filters.php' );

class WyzCategoriesFilter extends WyzBusinessFilter {

	public function __construct() {
		parent::__construct( 'category', esc_html__('Categories', 'wyzi-business-finder'), '' );
	}

	/*
	 *
	 * @Override
	 */
	public function content( $attr, $count_attr ) {

		$this->parse_params( $attr );
		$this->count_attr = $count_attr;
		$category = $this->get_value();
		$taxonomies = WyzHelpers::get_business_categories();
		ob_start();
	
		$len = count( $taxonomies );
		$selected =  get_post_meta( get_the_ID(), 'wyz_default_map_category', true );
		if ( 0 == $category ) $category = $selected;
		?>
		<div class="<?php $this->css_classes();?>">
			<select id="wyz-cat-filter" name="<?php echo $this->name;?>" class="wyz-input wyz-select">
				<option value=""><?php
				echo apply_filters( 'wyz_categories_filter_placeholder', esc_html__( 'categories...', 'wyzi-business-finder' ) );?></option>
				<?php for ( $i = 0; $i < $len; $i++ ) {
					$url = WyzHelpers::get_category_icon( $taxonomies[ $i ]['id'] );//wp_get_attachment_image_src(  get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_icon_upload', true ), 'thumbnail', true )[0];
					$bgc = get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_cat_bg_color', true );
					echo '<option value="'.$taxonomies[ $i ]['id'].'" ' . ( $category == $taxonomies[ $i ]['id'] ? 'selected ' : '' ) . ( false != $url ? 'data-left="<div class=\'cat-prnt-icn\' ' . ( '' != $bgc ? 'style=\'background-color:'.$bgc.';\' ' : '' ) .'><img class=\'lazyload\' data-src=\''.$url.'\'/></div>"' : '') . ' >&nbsp;'.$taxonomies[$i]['name'].'</option>';
					if ( isset( $taxonomies[ $i ]['children'] ) && ! empty( $taxonomies[ $i ]['children'] ) ) {
						foreach ( $taxonomies[ $i ]['children'] as $chld ) {
							echo '<option ' . ( $category == $chld['id'] ? 'selected ' : '' ) . 'value="' . $chld['id'] . '">' . $chld['name'] . '</option>';
						}
					}
				}?>
			</select>
		</div>
		<?php

		return ob_get_clean();
	}

	/*
	 *
	 * @Override
	 */
	public function options() {
		return '';
	}

	public function options_values() {
		return '';
	}
}