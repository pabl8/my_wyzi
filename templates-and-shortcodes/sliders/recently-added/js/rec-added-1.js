"use strict";
jQuery(document).ready(function() {
	jQuery(document).on('wyz_rec_added_slider',wyz_trigger_rec_added_slider);
	jQuery(document).trigger('wyz_rec_added_slider');
});

function wyz_trigger_rec_added_slider(){
	var nav = (recAddSlide.nav == 'true') ? true : false;
	var autoplay = (recAddSlide.autoplay == 'true') ? true : false;
	var autoplay_timeout = (autoplay && recAddSlide.autoplay_timeout > 0 ) ? recAddSlide.autoplay_timeout : 0;
	var loop = autoplay ? true : ( (recAddSlide.loop == 'true') ? true : false );
	var rtl = jQuery('html[dir="rtl"]').length ? true : false;
	var nOwl = jQuery('.recently-added-slider');
	var resp;
	var w = nOwl.parent().width();
		if(!isMobile.matches){
			if(w<600){
				resp = {
					1200:{
						items:recAddSlide.cssClasses.xs
					},
					970:{
						items:recAddSlide.cssClasses.xs
					},
					768:{
						items:recAddSlide.cssClasses.xs
					},
					0:{
						items:recAddSlide.cssClasses.xs
					},
				};
			}
			else if(w<900){
				resp = {
					1200:{
						items:recAddSlide.cssClasses.sm
					},
					970:{
						items:recAddSlide.cssClasses.sm
					},
					768:{
						items:recAddSlide.cssClasses.xs
					},
					0:{
						items:recAddSlide.cssClasses.xs
					},
				};
			}
			else{
				resp = {
					1200:{
						items:recAddSlide.cssClasses.lg
					},
					970:{
						items:recAddSlide.cssClasses.md
					},
					768:{
						items:recAddSlide.cssClasses.sm
					},
					0:{
						items:recAddSlide.cssClasses.xs
					},
				}
			}
		}else{
			resp = {
				1200:{
					items:recAddSlide.cssClasses.lg
				},
				970:{
					items:recAddSlide.cssClasses.md
				},
				768:{
					items:recAddSlide.cssClasses.sm
				},
				0:{
					items:recAddSlide.cssClasses.xs
				},
			}
		}

		
	nOwl.owlCarousel({
		margin: 30,
		loop: loop,
		nav: nav,
		rtl: rtl,
		autoplay: autoplay,
		autoplayTimeout: autoplay_timeout,
		autoplayHoverPause: autoplay,
		dots: false,
		navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
		responsive: resp,
	});
	
	if(autoplay)
		nOwl.trigger('play.owl.autoplay',[autoplay_timeout]);
}