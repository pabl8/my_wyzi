<?php
class WyzPostShare{

	public static function the_js_scripts() {

		$enabled_options = get_option( 'wyz_business_post_social_share' );

		if ( isset( $enabled_options['twitter'] ) ) {?>
			<script>window.twttr = (function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0],
			t = window.twttr || {};
			if (d.getElementById(id)) return t;
			js = d.createElement(s);
			js.id = id;
			js.src = "https://platform.twitter.com/widgets.js";
			fjs.parentNode.insertBefore(js, fjs);

			t._e = [];
			t.ready = function(f) {
			t._e.push(f);
			};

			return t;
			}(document, "script", "twitter-wjs"));</script>
		<?php 
		}

		if ( isset( $enabled_options['facebook'] ) ) {?>

			<div id="fb-root"></div>
			<script>
			window.fbAsyncInit = function() {
	          FB.init({
	            appId      : '<?php echo ( function_exists( 'wyz_get_option' ) ? wyz_get_option( 'businesses_fb_app_ID' ) : '' );?>', // App ID
	            status     : true, // check login status
	            cookie     : true, // enable cookies to allow the server to access the session
	            xfbml      : true  // parse XFBML
	          });
	        };

			/*(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));*/

			(function(d){
			var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
			js = d.createElement('script'); js.id = id; js.async = true;
			js.src = "//connect.facebook.net/en_US/all.js";
			d.getElementsByTagName('head')[0].appendChild(js);
			}(document));

			</script>
		<?php
		}

		if ( isset( $enabled_options['google'] ) ) {?>
			<script src="https://apis.google.com/js/platform.js" async defer>{parsetags: 'explicit'}</script>


		<?php
		}

		if ( isset( $enabled_options['linkedin'] ) ) {?>
			<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
		<?php
		}

		if ( isset( $enabled_options['pinterest'] ) ) {?>
			<script async defer data-pin-build="parsePinBtns" src="//assets.pinterest.com/js/pinit.js"></script>
		<?php
		}
	}

	public static function the_share_buttons( $id, $template_type = 1, $echo = true ){
		if ( ! $echo ) ob_start();
		$enabled_options = get_option( 'wyz_business_post_social_share' );
		if ( empty( $enabled_options ) )return;
		if ( $template_type == 1 ) {
			echo '<button class="busi-post-share-btn wyz-primary-color-text">' . esc_html__( 'SHARE', 'wyzi-business-finder' ) . '</button>';
		} elseif ( $template_type == 2 ) {
			echo '<a class="busi-post-share-btn wyz-prim-color-txt" href="#"><i class="fa fa-share-alt"></i><span>' . esc_html__( 'SHARE', 'wyzi-business-finder' ) . '</span></a>';
		}
		echo '<div class="animated fadeInUp business-post-share-cont">';
		self::the_buttons( $id, $enabled_options );
		echo '</div>';
		if ( ! $echo ) return ob_get_clean();
	}



	private static function the_buttons( $id, $enabled_options ) {
		
		$pic = '';
		if ( 'wyz_offers' == get_post_type( $id ) ) {
			$pic = wp_get_attachment_image_url( get_post_meta( $id, 'wyz_offers_image_id', true ),'large');
		} else {
			$pic = get_the_post_thumbnail_url( $id, 'large' );
			if ( empty( $pic ) ) {
				$pic = get_the_post_thumbnail_url( get_post_meta( $id, 'business_id', true ), 'large' );
			}
		}
		if ( isset( $enabled_options['facebook'] ) && function_exists( 'wyz_get_option' ) && '' != wyz_get_option( 'businesses_fb_app_ID' ) ) {?>
			<div class="post-share share-facebook">
				<a href="<?php echo 'https://www.facebook.com/dialog/feed?app_id=' . wyz_get_option( 'businesses_fb_app_ID' ) . '&link=' . get_the_permalink( $id ) /*. '&source=' . $pic*/;?>" target="_blank">
					<i class="fa fa-facebook" aria-hidden="true"></i>
				</a>
			</div>
		<?php
		}

		if ( isset( $enabled_options['google'] ) ) {?>
			<div class="post-share share-google">
				<a href="https://plus.google.com/share?url=<?php echo get_the_permalink( $id );?>" target="_blank">
					<i class="fa fa-google" aria-hidden="true"></i>
				</a>
			</div>
		<?php
		}

		if ( isset( $enabled_options['twitter'] ) ) {?>
			<div class="post-share share-twitter">
				<a href="https://twitter.com/share?url=<?php echo get_the_permalink( $id );?>&amp;text=<?php echo esc_url( get_the_excerpt( $id ) );?>&amp;" target="_blank">
        			<i class="fa fa-twitter" aria-hidden="true"></i>
        		</a>
				
			</div>
		<?php 
		}


		if ( isset( $enabled_options['linkedin'] ) ) {?>
			<div class="post-share share-linkedin">
			<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo get_the_permalink( $id );?>" target="_blank">
				<i class="fa fa-linkedin" aria-hidden="true"></i>
			</a>
			</div>
		<?php
		}

		if ( isset( $enabled_options['pinterest'] ) ) {?>
			<div class="post-share share-pinterest">
				<a href="http://pinterest.com/pin/create/button/?url=<?php echo get_the_permalink( $id );?>&media=<?php echo $pic;?>&description=<?php get_the_excerpt( $id )?>" class="pin-it-button">
					<i class="fa fa-pinterest-p" aria-hidden="true"></i>
				</a>
			</div>
		<?php
		}
	}

	private static $user_id;

	public static function the_like_button( $id, $template , $likes = -1, $liked = -1 ) {

		if ( empty( self::$user_id ) ) 
			$user_id = get_current_user_id();

		if ( -1 == $liked ) {
			$liked = get_post_meta( $id, 'wyz_business_post_likes', true );
			if ( empty( $liked ) )
				$liked = false;
			else
				$liked = ( is_array( $liked ) && in_array( $user_id, $liked ) ) || $liked ==  $user_id;
		}
		if ( -1 == $likes ) {
			$likes = get_post_meta( $id, 'wyz_business_post_likes_count', true );
			if ( ! empty( $likes ) )
				$likes = intval( $likes );
			else
				$likes = 0;
		}
		1 == $template ? self::the_like_button_1( $id, $likes, $liked ) : self::the_like_button_2( $id, $likes, $liked );
	}

	private static function the_like_button_1( $id, $likes, $liked ) { ?>
		<div class="post-like">
		<?php
		if ( is_user_logged_in() ) { ?>
			<button class="wyz-primary-color-text wyz-prim-color<?php
			if ( $liked ) {
				echo ' liked';
			} else {
				echo ' like-button';
			}?>" data-likes="<?php echo( $likes > 0 ? esc_attr( $likes ) : 0 ); ?>" data-postid="<?php echo esc_attr( $id ); ?>">
			
		<?php
		} else { ?>
			<button class="wyz-primary-color-text disabled like-btn-no-log">
		<?php }?>
			<i class="fa fa-check"></i><?php esc_html_e( 'like', 'wyzi-business-finder' );?></button>
			<span id="pl_<?php echo esc_attr( $id ); ?>" class="bubble"><?php echo esc_html( $likes ); ?></span>
		</div>
		<?php
	}

	private static function the_like_button_2( $id, $likes, $liked ) {
		if ( is_user_logged_in() ) { ?>
			<a data-likes="<?php echo( $likes > 0 ? esc_attr( $likes ) : 0 ); ?>" data-postid="<?php echo esc_attr( $id ); ?>" class="<?php echo $liked ? 'liked' : 'like-button';?> wyz-prim-color-txt">
			<i class="fa fa-heart<?php if ( $liked );
			else {
				echo '-o';
			}?>"></i>
		<?php
		} else { ?>
			<a class="disabled like-btn-no-log"><i class="fa fa-heart-o"></i>
		<?php }?>
		</a>
		<span class="likes-count wyz-prim-color-txt" id="pl_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $likes ); ?></span>
		<?php
	}

	public static function the_like_button_look( $id , $likes = -1 ) {

		if ( empty( self::$user_id ) ) 
			$user_id = get_current_user_id();
		if ( -1 == $likes ) {
			$likes = get_post_meta( $id, 'wyz_business_post_likes_count', true );
			if ( ! empty( $likes ) )
				$likes = intval( $likes );
			else
				$likes = 0;
		}
		?>

		<div style="float: left;margin-right: 13px;position: relative;">
			<div style="display: inline-block;text-transform: lowercase;line-height: 28px;margin-top: 4px;float: left;margin-right: 9px;color: #00aeff;padding: 0 12px;"><?php esc_html_e( 'likes', 'wyzi-business-finder' );?></div>
			<span style="border:0;color: #99a2aa;display: block;float: left;font-size: 12px;height: 24px;line-height: 22px;margin: 4px 0 0 3px;padding: 0 5px;position: relative;"><?php echo esc_html( $likes ); ?></span>
		</div>
		<?php
	}


	public static $user_favorites;
	public static function the_favorite_button( $id, $echo = true, $no_cont = false ) {
		if ( 'on' != get_option( 'wyz_enable_favorite_business' ) ) return '';
		if( empty( self::$user_favorites ) )
			self::$user_favorites = WyzHelpers::get_user_favorites();

		$is_fav = in_array( $id, self::$user_favorites );

		$fav_log_cls = is_user_logged_in() ? '' : ' fav-no-log';

		if ( ! $echo )ob_start();
		?>

		<?php if ( ! $no_cont )
		echo '<div class="bus-fav-cont float-right">';?>
			<a href="#" id="fav-bus" title="<?php esc_html_e( 'Favorite', 'wyzi-business-finder' );?>" class="ajax-click fav-bus<?php echo $fav_log_cls;?>" data-action="<?php echo $is_fav? 0 : 1;?>" data-busid="<?php echo $id;?>">
					<i class="fa fa-heart<?php echo $is_fav ? '' : '-o';?>"></i><span></span></a>
		<?php if ( ! $no_cont )echo '</div>';?>
		
		<?php
		if ( ! $echo ) return ob_get_clean();
	}
	
}
?>