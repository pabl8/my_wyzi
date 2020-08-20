"use strict";

jQuery(document).ready(function() {
	addCatSliderData(catSlide.taxs);

	jQuery('#categories-search-text').bind("propertychange change click keyup input paste", function() {
		var txt = jQuery('#categories-search-text').val();
		if ('' === txt) {
			addCatSliderData(catSlide.taxs);
			return;
		}
		var taxs = new Array();

		var l = catSlide.taxs.length;
		for (var i = 0; i < l; i++) {
			if (null !== catSlide.taxs[i].name.match(new RegExp(txt, 'i')))
				taxs.push(catSlide.taxs[i]);
			else if (catSlide.taxs[i].has_children) {
				var k = catSlide.taxs[i].all_children.length;
				for (var j = 0; j < k; j++) {
					if (null !== catSlide.taxs[i].all_children[j].match(new RegExp(txt, 'i'))) {
						taxs.push(catSlide.taxs[i]);
						break;
					}
				}
			}
		}
		addCatSliderData(taxs);
	});


	function addCatSliderData(taxs) {

		var nOwl = jQuery('.category-search-slider');
		nOwl.empty();
		nOwl.trigger('destroy.owl.carousel');
		nOwl.html(nOwl.find('.owl-stage-outer').html()).removeClass('owl-loaded');
		var nav = (catSlide.nav == 'true') ? true : false;
		var autoplay = (catSlide.autoplay == 'true') ? true : false;
		var autoplay_timeout = (autoplay && catSlide.autoplay_timeout > 0 ) ? catSlide.autoplay_timeout : 0;
		var loop = autoplay ? true : ( (catSlide.loop == 'true') ? true : false );

		var l = taxs.length;
		if (l === 0) {
			nOwl.append('<div class="section-title"><h1>Nothing matched your search!</h1></div>');
			catInitOwl(nOwl, nav, loop, autoplay, autoplay_timeout, {});
			return;
		}
		var rows = catSlide.rows;
		var rcount = 0;
		var data = "";
		var i;
		for (i = 0; i < l; i++) {

			if (0 === rcount)
				data += '<div class="sin-cat-item">';
			var currTax = taxs[i];
			data += '<div class="category-item"><div class="cat-icon float-left"' + (currTax.color !== '' ? 'style="background-color:' + currTax.color + ';"' : '') + ' >';
			if (currTax.img !== '')
				data += '<img src="' + currTax.img + '"/>';
			data += '</div>';
			data += '<div class="cat-list-cont">';
			data += '<h3><a href="' + currTax.link + '" '+(catSlide.style==2?'style="border-color:'+currTax.color+';"':'')+'>' + currTax.name + '</a></h3>';
			
			if (currTax.has_children) {
				data += '<ul>';
				var currChildren = currTax.children;
				var len = currChildren.length;
				for (var j = 0; j < len; j++)
					data += '<li><a href="' + currChildren[j].link + '">' + currChildren[j].name + (currChildren[j].bus_count > 0 ? ' (' + currChildren[j].bus_count + ')' : '') + '</a></li>';
			}
			if (currTax.view_all) {

				data += '<li><a href="' + currTax.link + '">' + catSlide.viewAll + ' »</a></li>';
			}
			if (currTax.has_children)
				data += '</ul>';
			data += '</div></div>';


			if ((rows - 1) == rcount) {
				data += '</div>';
				rcount = -1;
			}
			rcount++;
		}

		if (rcount !== 0)
			data += '</div>';

		nOwl.append(data);

		var resp;
		var w = nOwl.parent().width();
		if(!isMobile.matches){
			if(w<600){
				resp = {
					1200:{
						items:catSlide.cssClasses.xs
					},
					970:{
						items:catSlide.cssClasses.xs
					},
					768:{
						items:catSlide.cssClasses.xs
					},
					0:{
						items:catSlide.cssClasses.xs
					},
				};
			}
			else if(w<900){
				resp = {
					1200:{
						items:catSlide.cssClasses.sm
					},
					970:{
						items:catSlide.cssClasses.sm
					},
					768:{
						items:catSlide.cssClasses.xs
					},
					0:{
						items:catSlide.cssClasses.xs
					},
				};
			}
			else{
				resp = {
					1200:{
						items:catSlide.cssClasses.lg
					},
					970:{
						items:catSlide.cssClasses.md
					},
					768:{
						items:catSlide.cssClasses.sm
					},
					0:{
						items:catSlide.cssClasses.xs
					},
				}
			}
		}else{
			resp = {
				1200:{
					items:catSlide.cssClasses.lg
				},
				970:{
					items:catSlide.cssClasses.md
				},
				768:{
					items:catSlide.cssClasses.sm
				},
				0:{
					items:catSlide.cssClasses.xs
				},
			}
		}
		
		var rtl = jQuery('html[dir="rtl"]').length ? true : false;

		catInitOwl(nOwl, nav, loop,autoplay, autoplay_timeout, resp, rtl);
	}
});

function catInitOwl(nOwl, nav,loop,autoplay, autoplay_timeout,  resp, rtl){
	nOwl.owlCarousel({
		loop: loop,
		nav: nav,
		rtl: rtl,
		autoplay: autoplay,
		autoplayTimeout: autoplay_timeout,
		autoplayHoverPause: autoplay,
		dots: false,
		navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
		margin: 30,
		responsive: resp
	});

	if(autoplay)
		nOwl.trigger('play.owl.autoplay',[autoplay_timeout]);
}