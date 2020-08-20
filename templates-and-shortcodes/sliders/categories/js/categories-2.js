"use strict";

jQuery(document).ready(function() {
	addCatSliderData(catSlide.taxs);

	function addCatSliderData(taxs) {

		var nOwl = jQuery('.category-search-slider');
		nOwl.empty();
		nOwl.trigger('destroy.owl.carousel');
		nOwl.html(nOwl.find('.owl-stage-outer').html()).removeClass('owl-loaded');
		var nav = (catSlide.nav == 'true') ? true : false;var autoplay = (catSlide.autoplay == 'true') ? true : false;
		var autoplay_timeout = (autoplay && catSlide.autoplay_timeout > 0 ) ? catSlide.autoplay_timeout : 0;
		var loop = autoplay ? true : ( (catSlide.loop == 'true') ? true : false );


		var l = taxs.length;
		if (l === 0) {
			nOwl.append('<div class="section-title"><h1>Nothing matched your search!</h1></div>');
			initOwlCat(nOwl, nav, loop, autoplay, autoplay_timeout, {});
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
			if(currTax.child_count == undefined || currTax.child_count == '') currTax.child_count = 0; 
			data += '<div class="col-xs-12"><div class="cat-slide-item"><div class="parent-cat text-center">';
			if (currTax.img !== '')
				data += '<img src="' + currTax.img + '"/>';
			else
				data += '<div></div>';
			data += '<h4>' + currTax.name + '</h4></div><div class="total-cat-info text-center"><p>('+currTax.child_count+')  '+catSlide.translations.categories+'</p></div>';
			data += '<div class="cat-list"' + (currTax.color !== '' ? 'style="background-color:' + currTax.color + ';"' : '') + '><h4>'+currTax.name+'</h4>';
			if (currTax.has_children) {
				data += '<ul>';
				var currChildren = currTax.children;
				var len = currChildren.length;
				for (var j = 0; j < len; j++)
					data += '<li><a href="' + currChildren[j].link + '"><span>' + currChildren[j].name +'</span><span>'+ (currChildren[j].bus_count > 0 ? ' (' + currChildren[j].bus_count + ')' : '') + '</span></a></li>';
				data += '</ul>';
			}
			if (currTax.view_all) {
				data += '<a href="' + currTax.link + '" class="view-all-cat">'+catSlide.translations.viewAll+' <i class="fa fa-angle-double-right"></i></a>';
			}
				
			data += '</div></div></div>';

			
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
		initOwlCat(nOwl, nav, loop, autoplay, autoplay_timeout, resp,rtl);
	}
});

function initOwlCat(nOwl, nav, loop, autoplay, autoplay_timeout, resp,rtl){

	nOwl.owlCarousel({
		loop: loop,
		nav: nav,
		rtl: rtl,
		autoplay: autoplay,
		autoplayTimeout: autoplay_timeout,
		autoplayHoverPause: autoplay,
		dots: false,
		navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
		margin: 10,
		responsive: resp
	});

	if(autoplay)
		nOwl.trigger('play.owl.autoplay',[autoplay_timeout]);
}