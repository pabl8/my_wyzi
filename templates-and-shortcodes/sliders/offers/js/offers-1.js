"use strict";
jQuery(document).ready(function() {

	var img = jQuery('.our-offer-slider').find('.image:first').find('img');

	if (img.height() === 0 || img.height() === '') {
		img.on('load', function() {
			initializeOffersSlider();
		});
	} else {
		initializeOffersSlider();
	}
});

function initializeOffersSlider() {
	var nav = (offerSlide.nav == 'true') ? true : false;
	var autoplay = (offerSlide.autoplay == 'true') ? true : false;
	var autoplay_timeout = (autoplay && offerSlide.autoplay_timeout > 0 ) ? offerSlide.autoplay_timeout : 0;
	var loop = autoplay ? true : ( (offerSlide.loop == 'true') ? true : false );
	var autoHeight = (offerSlide.autoHeight == 'true') ? true : false;
	var rtl = jQuery('html[dir="rtl"]').length ? true : false;
	jQuery('.our-offer-slider').owlCarousel({
		margin: 60,
		loop: loop,
		autoplay: autoplay,
		rtl: rtl,
		autoplayTimeout: autoplay_timeout,
		autoplayHoverPause: autoplay,
		nav: nav,
		dots: false,
		autoHeight: autoHeight,
		navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
		items: 1
	});
}
