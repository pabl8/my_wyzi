"use strict";

jQuery.fn.isAboveScreen = function(){
	var viewport = {};
	viewport.top =jQuery(window).scrollTop();
	var bounds = {};
	bounds.bottom = bounds.top + jQuery(this).outerHeight();
	return (bounds.bottom < viewport.top);
}


var canScroll = true;
jQuery(document).ready(function() {
	// cicle all the divs with 'postswrapper' class to get those specific dev ids
			
	var $jq = jQuery.noConflict();
	$jq('div[id^="postswrapper"]').each( function() { 

    	var $div = $jq(this); // the sdiv jquery object
	  	var token = $div.data('token');
		
		//Now we can read the name of the variable passed by PHP
	  	var walll = window['walll' + token];
	  	var wall = window['wall' + token];
	
		var LoginDropdown = {
		    getDropdown: function(a, b, c) {
		        return this.x = a, this.y = b, this.dropdown = '<div class="login-dropdown list-group'+(c?' post-comm-dropdown':'')+'" style="display:block; position: absolute; '+(c?'right':'left')+': ' + a + "px; top: " + b + 'px;" >' + (!c?'<span>'+wall.likePostq+'</span>':'')+'<p>'+wall.loginLike+'</p><a class="wyz-button wyz-primary-color  wyz-prim-color icon action-btn btn-bg-blue btn-rounded" href="' + wall.loginPermalink + '" title="Sign in"> Sign up<i class="fa fa-angle-right" aria-hidden="true"></i></a></div>', jQuery.parseHTML(this.dropdown);
		    }
		};
		if (true == wall.hasPosts) {
			var page = 1,DropDn, h = !1;

			if ( 'manual' != walll.pull_method ) {
				jQuery(walll.randomIDfooter).bind('inview', function(event, visible){
					ajax_loadmore(visible);
				});
				//case page reload and loadmoreajaxloader is above screen
				if(jQuery(walll.randomIDfooter).isAboveScreen())
					ajax_loadmore(true);
			} else {
				ajax_loadmore(true);
			}


			toastr.options.closeMethod = 'fadeOut';
			toastr.options.showEasing = 'swing';
			toastr.options.hideEasing = 'swing';
			toastr.options.closeDuration = 300;
			toastr.options.preventDuplicates = true;
			toastr.options.timeOut = 1000;

			jQuery("body").click(function() {
	            h && DropDn!=undefined&&(DropDn.slideUp("fast"), DropDn.remove(), h = !1);
	        });

	        jQuery(walll.randomID + ' .load-more-btn').live('click',function(){
	    		this.parentElement.removeChild(this);
	        	ajax_loadmore(true);
	        });
			

			jQuery(walll.randomID + " .com-view-more").live({
				click: function (event) {
					event.preventDefault();
					jQuery(this).addClass('fade-loading');
					var This = jQuery(this);
					var offset = parseInt( This.data('offset') );
					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						data: "action=bus_load_comments&nonce=" + ajaxnonce + "&post-id=" + This.data('id') + '&offset=' + offset,
						success: function(result) {
							This.removeClass('fade-loading');
							if ( result ) {
								This.parents('.the-post-comments').append(result);
								//This.closest('.the-comment').before(result);
								This.closest('.the-comment-more').css({"display":"none"});
							} else {
								This.closest('.the-comment-more').css({"display":"none"});
							}
						}
					});
				}
			});

			var currentTarget, currentCommInput;
			jQuery(walll.randomID + " .post_footer_comment_btn, " + walll.randomID + " .post_footer_comment_btn").live("click", function() {
				if('false'==(wall.loggedInUser)){
					if(jQuery(this).hasClass('action-btn')){
						DropDn = LoginDropdown.getDropdown(70, 
		                	jQuery(this).position().top + 53, true
		                );
					} else {
		                DropDn = LoginDropdown.getDropdown(35, 
		                	jQuery(this).position().top + jQuery(this).parent().parent().position().top + 40, true
		                );
		            }
	                jQuery(walll.randomID).append(DropDn);
	                DropDn = jQuery(walll.randomID).find(".login-dropdown"), DropDn.slideDown("slow");
	                h=1;
		            return;
		        }

				currentCommInput = jQuery(this).prev();
				var inputContent = currentCommInput.val(),
				non_c = jQuery(this).next().val();
				if(inputContent==''){
					toastr.warning("can't publish an empty comment");
				} else{
					var id = jQuery(this).data('id'),
					currentTarget = jQuery(this);
					currentTarget.prop("disabled", !0);
					currentTarget.addClass("busi_post_submit-dis");
					var label = currentTarget.text();
					currentTarget.text("Posting...");
					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						data: "action=bus_post_comm&nonce=" + non_c + "&id=" + id + "&comment=" + inputContent,
						success: function(msg) {
							if(!msg){
								toastr.error("Post comment failed");
							}
							else {
								currentTarget.closest('.post-footer-comments').find('.the-post-comments').prepend(msg);
								currentTarget.prop("disabled", 0);
								currentTarget.removeClass("busi_post_submit-dis");
								currentCommInput.val('');
								currentTarget.text(label);
							}
						}
					});
				}
			});

			//handle liking when user is not logged in
			jQuery(document).on( walll.randomID + " click", walll.randomID + " .like-btn-no-log", function() {
	        	if(h){DropDn.slideUp("fast")}
	            event.preventDefault(), h = true, DropDn = LoginDropdown.getDropdown(35, jQuery(this).position().top + ( jQuery(this).find('.fa-heart-o').length ? jQuery(this).parent().position().top + 60 : jQuery(this).parent().parent().position().top + 40), false), jQuery(walll.randomID).append(DropDn), 
	        	DropDn = jQuery(walll.randomID).find(".login-dropdown"), DropDn.slideDown("slow")
	        });

	        jQuery('.show-post-comments').live('click', function(e){
	        	e.preventDefault();
	        	var target = jQuery(this).parents('.sin-busi-post').find('.the-post-comments .the-comment:last-child');
	        	if (0 == target.length )
	        		target = jQuery(this).parents('.sin-busi-post').find('.the-post-comments .the-comment:first-child');
	        	if (0 != target.length ) {
		        	 jQuery('html, body').animate({
						scrollTop: target.offset().top - 400
					}, 500);
	        	}
	        });
		}

		function ajax_loadmore(visible) {  
			if (visible && canScroll) { 
				

				jQuery(walll.randomIDfooter).fadeTo("fast", 1);
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: "action=all_bus_inf_scrll&nonce=" + ajaxnonce + "&post_index=" + walll.ind + "&page=" + page + "&logged-in-user=" + wall.loggedInUser + '&posts_pull=' + walll.posts_pull+"&category="+walll.category+'&only_fav='+walll.onlyFav,
					success: function(html) {   
						if ('' !== html) {
							html = JSON.parse(html);
							if(html['status']==0){
								//user not logged in
								//canScroll = false;
								jQuery(walll.divrandomIDfooter).html('<center>'+html.content+'</center>');
							}else{
								page++;
								canScroll = true;
								html = html['content'];
								var spaceIndex = html.indexOf('wyz_space');
								walll.ind = parseInt(html.substring(0, spaceIndex));
								jQuery(walll.randomID).append(html.substring(spaceIndex + 9, html.length));
								jQuery(walll.randomIDfooter).fadeTo("fast", 0);
								if ( 'manual' == walll.pull_method ) {
									jQuery(walll.randomID).append('<center><button class="load-more-btn wyz-primary-color-text">'+wall.loadMoreMsg+'</button></center>');
								}
							}
						} else {
							jQuery(walll.divrandomIDfooter).html('<center>'+wall.noPostsMsg+'</center>');
						}
					}
				});
			}
		}
	}); // end each cycle
});

function getBaseUrl() {
	var re = new RegExp(/^.*\//);
	return re.exec(window.location.href);
}
