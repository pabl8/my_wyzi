"use strict";
jQuery(document).ready(function() {
	var nav = (featuredSlide.nav == 'true') ? true : false;
	var autoplay = (featuredSlide.autoplay == 'true') ? true : false;
	var autoplay_timeout = (autoplay && featuredSlide.autoplay_timeout > 0 ) ? featuredSlide.autoplay_timeout : 0;
	var loop = autoplay ? true : ( (featuredSlide.loop == 'true') ? true : false );
	var nOwl = jQuery('.featured-slider');
	var resp;
	var w = nOwl.parent().width();
		if(!isMobile.matches){
			if(w<600){
				resp = {
					1200:{
						items:featuredSlide.cssClasses.xs
					},
					970:{
						items:featuredSlide.cssClasses.xs
					},
					768:{
						items:featuredSlide.cssClasses.xs
					},
					0:{
						items:featuredSlide.cssClasses.xs
					},
				};
			}
			else if(w<900){
				resp = {
					1200:{
						items:featuredSlide.cssClasses.sm
					},
					970:{
						items:featuredSlide.cssClasses.sm
					},
					768:{
						items:featuredSlide.cssClasses.xs
					},
					0:{
						items:featuredSlide.cssClasses.xs,
					},
				};
			}
			else{
				resp = {
					1200:{
						items:featuredSlide.cssClasses.lg
					},
					970:{
						items:featuredSlide.cssClasses.md
					},
					768:{
						items:featuredSlide.cssClasses.sm
					},
					0:{
						items:featuredSlide.cssClasses.xs
					},
				}
			}
		}else{
			resp = {
				1200:{
					items:featuredSlide.cssClasses.lg
				},
				970:{
					items:featuredSlide.cssClasses.md
				},
				768:{
					items:featuredSlide.cssClasses.sm
				},
				0:{
					items:featuredSlide.cssClasses.xs
				},
			}
		}


	var rtl = jQuery('html[dir="rtl"]').length ? true : false;

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
		responsive: resp
	});

	if(autoplay)
		nOwl.trigger('play.owl.autoplay',[autoplay_timeout]);
});
