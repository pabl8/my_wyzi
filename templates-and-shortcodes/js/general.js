//toastr
!function(e){e(["jquery"],function(e){return function(){function t(e,t,n){return g({type:O.error,iconClass:m().iconClasses.error,message:e,optionsOverride:n,title:t})}function n(t,n){return t||(t=m()),v=e("#"+t.containerId),v.length?v:(n&&(v=d(t)),v)}function o(e,t,n){return g({type:O.info,iconClass:m().iconClasses.info,message:e,optionsOverride:n,title:t})}function s(e){C=e}function i(e,t,n){return g({type:O.success,iconClass:m().iconClasses.success,message:e,optionsOverride:n,title:t})}function a(e,t,n){return g({type:O.warning,iconClass:m().iconClasses.warning,message:e,optionsOverride:n,title:t})}function r(e,t){var o=m();v||n(o),u(e,o,t)||l(o)}function c(t){var o=m();return v||n(o),t&&0===e(":focus",t).length?void h(t):void(v.children().length&&v.remove())}function l(t){for(var n=v.children(),o=n.length-1;o>=0;o--)u(e(n[o]),t)}function u(t,n,o){var s=!(!o||!o.force)&&o.force;return!(!t||!s&&0!==e(":focus",t).length)&&(t[n.hideMethod]({duration:n.hideDuration,easing:n.hideEasing,complete:function(){h(t)}}),!0)}function d(t){return v=e("<div/>").attr("id",t.containerId).addClass(t.positionClass),v.appendTo(e(t.target)),v}function p(){return{tapToDismiss:!0,toastClass:"toast",containerId:"toast-container",debug:!1,showMethod:"fadeIn",showDuration:300,showEasing:"swing",onShown:void 0,hideMethod:"fadeOut",hideDuration:1e3,hideEasing:"swing",onHidden:void 0,closeMethod:!1,closeDuration:!1,closeEasing:!1,closeOnHover:!0,extendedTimeOut:1e3,iconClasses:{error:"toast-error",info:"toast-info",success:"toast-success",warning:"toast-warning"},iconClass:"toast-info",positionClass:"toast-top-right",timeOut:5e3,titleClass:"toast-title",messageClass:"toast-message",escapeHtml:!1,target:"body",closeHtml:'<button type="button">&times;</button>',closeClass:"toast-close-button",newestOnTop:!0,preventDuplicates:!1,progressBar:!1,progressClass:"toast-progress",rtl:!1}}function f(e){C&&C(e)}function g(t){function o(e){return null==e&&(e=""),e.replace(/&/g,"&amp;").replace(/"/g,"&quot;").replace(/'/g,"&#39;").replace(/</g,"&lt;").replace(/>/g,"&gt;")}function s(){c(),u(),d(),p(),g(),C(),l(),i()}function i(){var e="";switch(t.iconClass){case"toast-success":case"toast-info":e="polite";break;default:e="assertive"}I.attr("aria-live",e)}function a(){E.closeOnHover&&I.hover(H,D),!E.onclick&&E.tapToDismiss&&I.click(b),E.closeButton&&j&&j.click(function(e){e.stopPropagation?e.stopPropagation():void 0!==e.cancelBubble&&e.cancelBubble!==!0&&(e.cancelBubble=!0),E.onCloseClick&&E.onCloseClick(e),b(!0)}),E.onclick&&I.click(function(e){E.onclick(e),b()})}function r(){I.hide(),I[E.showMethod]({duration:E.showDuration,easing:E.showEasing,complete:E.onShown}),E.timeOut>0&&(k=setTimeout(b,E.timeOut),F.maxHideTime=parseFloat(E.timeOut),F.hideEta=(new Date).getTime()+F.maxHideTime,E.progressBar&&(F.intervalId=setInterval(x,10)))}function c(){t.iconClass&&I.addClass(E.toastClass).addClass(y)}function l(){E.newestOnTop?v.prepend(I):v.append(I)}function u(){if(t.title){var e=t.title;E.escapeHtml&&(e=o(t.title)),M.append(e).addClass(E.titleClass),I.append(M)}}function d(){if(t.message){var e=t.message;E.escapeHtml&&(e=o(t.message)),B.append(e).addClass(E.messageClass),I.append(B)}}function p(){E.closeButton&&(j.addClass(E.closeClass).attr("role","button"),I.prepend(j))}function g(){E.progressBar&&(q.addClass(E.progressClass),I.prepend(q))}function C(){E.rtl&&I.addClass("rtl")}function O(e,t){if(e.preventDuplicates){if(t.message===w)return!0;w=t.message}return!1}function b(t){var n=t&&E.closeMethod!==!1?E.closeMethod:E.hideMethod,o=t&&E.closeDuration!==!1?E.closeDuration:E.hideDuration,s=t&&E.closeEasing!==!1?E.closeEasing:E.hideEasing;if(!e(":focus",I).length||t)return clearTimeout(F.intervalId),I[n]({duration:o,easing:s,complete:function(){h(I),clearTimeout(k),E.onHidden&&"hidden"!==P.state&&E.onHidden(),P.state="hidden",P.endTime=new Date,f(P)}})}function D(){(E.timeOut>0||E.extendedTimeOut>0)&&(k=setTimeout(b,E.extendedTimeOut),F.maxHideTime=parseFloat(E.extendedTimeOut),F.hideEta=(new Date).getTime()+F.maxHideTime)}function H(){clearTimeout(k),F.hideEta=0,I.stop(!0,!0)[E.showMethod]({duration:E.showDuration,easing:E.showEasing})}function x(){var e=(F.hideEta-(new Date).getTime())/F.maxHideTime*100;q.width(e+"%")}var E=m(),y=t.iconClass||E.iconClass;if("undefined"!=typeof t.optionsOverride&&(E=e.extend(E,t.optionsOverride),y=t.optionsOverride.iconClass||y),!O(E,t)){T++,v=n(E,!0);var k=null,I=e("<div/>"),M=e("<div/>"),B=e("<div/>"),q=e("<div/>"),j=e(E.closeHtml),F={intervalId:null,hideEta:null,maxHideTime:null},P={toastId:T,state:"visible",startTime:new Date,options:E,map:t};return s(),r(),a(),f(P),E.debug&&console&&console.log(P),I}}function m(){return e.extend({},p(),b.options)}function h(e){v||(v=n()),e.is(":visible")||(e.remove(),e=null,0===v.children().length&&(v.remove(),w=void 0))}var v,C,w,T=0,O={error:"error",info:"info",success:"success",warning:"warning"},b={clear:r,remove:c,error:t,getContainer:n,info:o,options:{},subscribe:s,success:i,version:"2.1.3",warning:a};return b}()})}("function"==typeof define&&define.amd?define:function(e,t){"undefined"!=typeof module&&module.exports?module.exports=t(require("jquery")):window.toastr=t(window.jQuery)});
"use strict";

/*! lazysizes - v4.1.5 */
!function(a,b){var c=b(a,a.document);a.lazySizes=c,"object"==typeof module&&module.exports&&(module.exports=c)}(window,function(a,b){"use strict";if(b.getElementsByClassName){var c,d,e=b.documentElement,f=a.Date,g=a.HTMLPictureElement,h="addEventListener",i="getAttribute",j=a[h],k=a.setTimeout,l=a.requestAnimationFrame||k,m=a.requestIdleCallback,n=/^picture$/i,o=["load","error","lazyincluded","_lazyloaded"],p={},q=Array.prototype.forEach,r=function(a,b){return p[b]||(p[b]=new RegExp("(\\s|^)"+b+"(\\s|$)")),p[b].test(a[i]("class")||"")&&p[b]},s=function(a,b){r(a,b)||a.setAttribute("class",(a[i]("class")||"").trim()+" "+b)},t=function(a,b){var c;(c=r(a,b))&&a.setAttribute("class",(a[i]("class")||"").replace(c," "))},u=function(a,b,c){var d=c?h:"removeEventListener";c&&u(a,b),o.forEach(function(c){a[d](c,b)})},v=function(a,d,e,f,g){var h=b.createEvent("Event");return e||(e={}),e.instance=c,h.initEvent(d,!f,!g),h.detail=e,a.dispatchEvent(h),h},w=function(b,c){var e;!g&&(e=a.picturefill||d.pf)?(c&&c.src&&!b[i]("srcset")&&b.setAttribute("srcset",c.src),e({reevaluate:!0,elements:[b]})):c&&c.src&&(b.src=c.src)},x=function(a,b){return(getComputedStyle(a,null)||{})[b]},y=function(a,b,c){for(c=c||a.offsetWidth;c<d.minSize&&b&&!a._lazysizesWidth;)c=b.offsetWidth,b=b.parentNode;return c},z=function(){var a,c,d=[],e=[],f=d,g=function(){var b=f;for(f=d.length?e:d,a=!0,c=!1;b.length;)b.shift()();a=!1},h=function(d,e){a&&!e?d.apply(this,arguments):(f.push(d),c||(c=!0,(b.hidden?k:l)(g)))};return h._lsFlush=g,h}(),A=function(a,b){return b?function(){z(a)}:function(){var b=this,c=arguments;z(function(){a.apply(b,c)})}},B=function(a){var b,c=0,e=d.throttleDelay,g=d.ricTimeout,h=function(){b=!1,c=f.now(),a()},i=m&&g>49?function(){m(h,{timeout:g}),g!==d.ricTimeout&&(g=d.ricTimeout)}:A(function(){k(h)},!0);return function(a){var d;(a=!0===a)&&(g=33),b||(b=!0,d=e-(f.now()-c),d<0&&(d=0),a||d<9?i():k(i,d))}},C=function(a){var b,c,d=99,e=function(){b=null,a()},g=function(){var a=f.now()-c;a<d?k(g,d-a):(m||e)(e)};return function(){c=f.now(),b||(b=k(g,d))}};!function(){var b,c={lazyClass:"lazyload",loadedClass:"lazyloaded",loadingClass:"lazyloading",preloadClass:"lazypreload",errorClass:"lazyerror",autosizesClass:"lazyautosizes",srcAttr:"data-src",srcsetAttr:"data-srcset",sizesAttr:"data-sizes",minSize:40,customMedia:{},init:!0,expFactor:1.5,hFac:.8,loadMode:2,loadHidden:!0,ricTimeout:0,throttleDelay:125};d=a.lazySizesConfig||a.lazysizesConfig||{};for(b in c)b in d||(d[b]=c[b]);a.lazySizesConfig=d,k(function(){d.init&&F()})}();var D=function(){var g,l,m,o,p,y,D,F,G,H,I,J=/^img$/i,K=/^iframe$/i,L="onscroll"in a&&!/(gle|ing)bot/.test(navigator.userAgent),M=0,N=0,O=0,P=-1,Q=function(a){O--,a&&a.target&&u(a.target,Q),(!a||O<0||!a.target)&&(O=0)},R=function(a,c){var d,f=a,g="hidden"==x(b.body,"visibility")||"hidden"!=x(a.parentNode,"visibility")&&"hidden"!=x(a,"visibility");for(F-=c,I+=c,G-=c,H+=c;g&&(f=f.offsetParent)&&f!=b.body&&f!=e;)(g=(x(f,"opacity")||1)>0)&&"visible"!=x(f,"overflow")&&(d=f.getBoundingClientRect(),g=H>d.left&&G<d.right&&I>d.top-1&&F<d.bottom+1);return g},S=function(){var a,f,h,j,k,m,n,p,q,r,s,t,u=c.elements;if((o=d.loadMode)&&O<8&&(a=u.length)){for(f=0,P++,r=!d.expand||d.expand<1?e.clientHeight>500&&e.clientWidth>500?500:370:d.expand,s=r*d.expFactor,t=d.hFac,N<s&&O<1&&P>2&&o>2&&!b.hidden?(N=s,P=0):N=o>1&&P>1&&O<6?r:M;f<a;f++)if(u[f]&&!u[f]._lazyRace)if(L)if((p=u[f][i]("data-expand"))&&(m=1*p)||(m=N),q!==m&&(y=innerWidth+m*t,D=innerHeight+m,n=-1*m,q=m),h=u[f].getBoundingClientRect(),(I=h.bottom)>=n&&(F=h.top)<=D&&(H=h.right)>=n*t&&(G=h.left)<=y&&(I||H||G||F)&&(d.loadHidden||"hidden"!=x(u[f],"visibility"))&&(l&&O<3&&!p&&(o<3||P<4)||R(u[f],m))){if($(u[f]),k=!0,O>9)break}else!k&&l&&!j&&O<4&&P<4&&o>2&&(g[0]||d.preloadAfterLoad)&&(g[0]||!p&&(I||H||G||F||"auto"!=u[f][i](d.sizesAttr)))&&(j=g[0]||u[f]);else $(u[f]);j&&!k&&$(j)}},T=B(S),U=function(a){s(a.target,d.loadedClass),t(a.target,d.loadingClass),u(a.target,W),v(a.target,"lazyloaded")},V=A(U),W=function(a){V({target:a.target})},X=function(a,b){try{a.contentWindow.location.replace(b)}catch(c){a.src=b}},Y=function(a){var b,c=a[i](d.srcsetAttr);(b=d.customMedia[a[i]("data-media")||a[i]("media")])&&a.setAttribute("media",b),c&&a.setAttribute("srcset",c)},Z=A(function(a,b,c,e,f){var g,h,j,l,o,p;(o=v(a,"lazybeforeunveil",b)).defaultPrevented||(e&&(c?s(a,d.autosizesClass):a.setAttribute("sizes",e)),h=a[i](d.srcsetAttr),g=a[i](d.srcAttr),f&&(j=a.parentNode,l=j&&n.test(j.nodeName||"")),p=b.firesLoad||"src"in a&&(h||g||l),o={target:a},p&&(u(a,Q,!0),clearTimeout(m),m=k(Q,2500),s(a,d.loadingClass),u(a,W,!0)),l&&q.call(j.getElementsByTagName("source"),Y),h?a.setAttribute("srcset",h):g&&!l&&(K.test(a.nodeName)?X(a,g):a.src=g),f&&(h||l)&&w(a,{src:g})),a._lazyRace&&delete a._lazyRace,t(a,d.lazyClass),z(function(){(!p||a.complete&&a.naturalWidth>1)&&(p?Q(o):O--,U(o))},!0)}),$=function(a){var b,c=J.test(a.nodeName),e=c&&(a[i](d.sizesAttr)||a[i]("sizes")),f="auto"==e;(!f&&l||!c||!a[i]("src")&&!a.srcset||a.complete||r(a,d.errorClass)||!r(a,d.lazyClass))&&(b=v(a,"lazyunveilread").detail,f&&E.updateElem(a,!0,a.offsetWidth),a._lazyRace=!0,O++,Z(a,b,f,e,c))},_=function(){if(!l){if(f.now()-p<999)return void k(_,999);var a=C(function(){d.loadMode=3,T()});l=!0,d.loadMode=3,T(),j("scroll",function(){3==d.loadMode&&(d.loadMode=2),a()},!0)}};return{_:function(){p=f.now(),c.elements=b.getElementsByClassName(d.lazyClass),g=b.getElementsByClassName(d.lazyClass+" "+d.preloadClass),j("scroll",T,!0),j("resize",T,!0),a.MutationObserver?new MutationObserver(T).observe(e,{childList:!0,subtree:!0,attributes:!0}):(e[h]("DOMNodeInserted",T,!0),e[h]("DOMAttrModified",T,!0),setInterval(T,999)),j("hashchange",T,!0),["focus","mouseover","click","load","transitionend","animationend","webkitAnimationEnd"].forEach(function(a){b[h](a,T,!0)}),/d$|^c/.test(b.readyState)?_():(j("load",_),b[h]("DOMContentLoaded",T),k(_,2e4)),c.elements.length?(S(),z._lsFlush()):T()},checkElems:T,unveil:$}}(),E=function(){var a,c=A(function(a,b,c,d){var e,f,g;if(a._lazysizesWidth=d,d+="px",a.setAttribute("sizes",d),n.test(b.nodeName||""))for(e=b.getElementsByTagName("source"),f=0,g=e.length;f<g;f++)e[f].setAttribute("sizes",d);c.detail.dataAttr||w(a,c.detail)}),e=function(a,b,d){var e,f=a.parentNode;f&&(d=y(a,f,d),e=v(a,"lazybeforesizes",{width:d,dataAttr:!!b}),e.defaultPrevented||(d=e.detail.width)&&d!==a._lazysizesWidth&&c(a,f,e,d))},f=function(){var b,c=a.length;if(c)for(b=0;b<c;b++)e(a[b])},g=C(f);return{_:function(){a=b.getElementsByClassName(d.autosizesClass),j("resize",g)},checkElems:g,updateElem:e}}(),F=function(){F.i||(F.i=!0,E._(),D._())};return c={cfg:d,autoSizer:E,loader:D,init:F,uP:w,aC:s,rC:t,hC:r,fire:v,gW:y,rAF:z}}});


/*set location (latitude and longitude) cookies*/
function wyzSaveLocationCookies(position) {
    wyzDeleteCookie("user_lat");
     wyzDeleteCookie("user_long");
    
    var lat = position.coords.latitude;
    var long = position.coords.longitude;
    
    wyzCreateCookie("user_lat",lat,1);
    wyzCreateCookie("user_long",long,1);
    
}

function wyzLocationCookiesError(err) {
    wyzCreateCookie("user_lat","NA",1);
    wyzCreateCookie("user_long","NA",1);
}

function wyzCreateCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function wyzDeleteCookie( name ) {
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

jQuery(document).ready(function() {
	//show wyzi js elements.
	//for browsers with no js.
	jQuery('.map-container').show();

	jQuery('.category-search-area').show();
	jQuery('.location-search-area').show();
	jQuery('.our-offer-area').show();
	jQuery('.recently-added-area').show();
	jQuery('.featured-area').show();

	var LikeDropdown = {
	    getDropdown: function(a, b, c) {
	        return this.x = a, this.y = b, this.dropdown = '<div class="login-dropdown list-group above'+(c?' post-comm-dropdown':'')+'" style="display:block; position: absolute; '+(c?'right':'left')+': ' + a + "px; top: " + b + 'px;" >' + (!c?'<span>'+general.likePostq+'</span>':'')+'<p>'+general.loginLike+'</p><a class="wyz-button wyz-primary-color  wyz-prim-color icon action-btn btn-bg-blue" style="padding: 0 18px;" href="' + general.loginPermalink + '" title="Sign in"> Sign up<i class="fa fa-angle-right" aria-hidden="true"></i></a></div>', jQuery.parseHTML(this.dropdown);
	    }
	};


	var useDimmer = 1 == wyz_template_type;
	
	//business category dropdown filter
	if (jQuery('.bus-filter-category .wyz-select').length) {
		jQuery('.bus-filter-category .wyz-select').selectator({
			labels: {
				search: general.searchText
			},
			useDimmer: useDimmer
		});
	}
	
	
	    setTimeout(function(){
	        try{
	    jQuery('#wyz_registration_form').tooltip();
	        }catch(error){}
	    },500);
	

	//locations dropdown filter
	if (jQuery('#wyz-loc-filter').length) {
		jQuery('#wyz-loc-filter').selectator({
			labels: {
				search: general.searchText
			},
			useDimmer: useDimmer
		});
	}

	//locations dropdown filter
	if (jQuery('.bus-filter-locations-dropdown').length) {
		jQuery('.bus-filter-locations-dropdown').each(function(){
			jQuery(this).selectator({
				labels: {
					search: general.searchText
				},
				useDimmer: useDimmer
			});
		});
	}

	if( jQuery('.wyz-selectize-days-filter').length){
		jQuery('.wyz-selectize-days-filter').each(function(){
			jQuery(this).selectize({
				create: false,
				plugins: ['remove_button']
			});
		});
	}

	if(jQuery('#header-image-content').length){
		var marg = (jQuery('#page-header-image').height()-jQuery('#header-image-content').height())/2;
		jQuery('#header-image-content').css('padding-top',marg+'px' );
	}


	var shropn = false;
	jQuery(document).on('click', '.busi-post-share-btn', function (event) {
			event.preventDefault();
			shropn=!shropn;
			if(shropn){
				jQuery(this).parent().parent().parent().find('.view-offer').css('margin-bottom', "15px");
			} else {
				jQuery(this).parent().parent().parent().find('.view-offer').css('margin-bottom', "0px");
			}
			jQuery(this).next(".business-post-share-cont").toggle();
	});

	jQuery('.file-upload').live('change',function () {
		jQuery(this).parent().find('p').text(this.files[0].name);
	});
	

	// Ipad fix
	jQuery(document).ready(function() {

		// Detect ios 11_x_x affected  
		// NEED TO BE UPDATED if new versions are affected
		var ua = navigator.userAgent,
		iOS = /iPad|iPhone|iPod/.test(ua),
		iOS11 = /OS 11_0|OS 11_1|OS 11_2/.test(ua);

		// ios 11 bug caret position
		if ( iOS && iOS11 ) {

		    // Add CSS class to body
		    jQuery("body").addClass("iosBugFixCaret");

		}

	});

	jQuery('.single-wyz_business_post .sin-blog .blog-image').bind('click', function(){
		var h = jQuery('img',this).height()
		jQuery(this).unbind().animate({maxHeight:h+'px'},500);
		jQuery('img', this).unbind().fadeTo(50,1);
	});
	jQuery('.single-wyz_business_post .sin-blog .blog-image>img').hover(function(){
		jQuery(this).fadeTo(50,0.6);
	},function(){
		jQuery(this).fadeTo(50,1);
	});

	var haveSliders = ( jQuery('.category-search-area').length || jQuery('.location-search-area').length || jQuery('.our-offer-area').length || jQuery('.recently-added-area').length || jQuery('.featured-area').length );
	var is_single_business = jQuery('.business-offers-area').length;

	if(haveSliders){
		var resizeId;
		var catExists = jQuery('.category-search-area').length;
		var locExists = jQuery('.location-search-area').length;
		var offExists = jQuery('.our-offer-area').length;
		var busOffExists = jQuery('.business-offers-area').length;
		var recExists = jQuery('.recently-added-area').length;
		var featExists = jQuery('.featured-area').length;

		refreshSlidersWidth(catExists,locExists,offExists,recExists,featExists,busOffExists);
	}

	if(is_single_business)
		refreshSlidersWidth(false,false,false,false,false,true);

	//Like functionality
	jQuery(".like-button").live("click", function(a) {
		var currentLiked = jQuery(this);
        a.preventDefault(), currentLiked.removeClass("like-button"), currentLiked.addClass("liked");
        var iElement = currentLiked.find('i');
        if(iElement.hasClass('fa-heart-o')){
        	iElement.removeClass('fa-heart-o');
        	iElement.addClass('fa-heart');
        }
        var b = "#pl_" + currentLiked.data("postid");

        currentLiked.attr('disabled',true);
        var currentLikes = currentLiked.data("likes");
        currentLiked.data("likes", currentLikes+ 1);
        jQuery(b).html(currentLiked.data("likes")), jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: "action=buslike&nonce=" + ajaxnonce + "&act=like&post-id=" + currentLiked.data("postid"),
            success: function() {currentLiked.attr('disabled',false);}
        })
    });
    jQuery(".liked").live("click", function(a) {
		var currentDisliked = jQuery(this);
        a.preventDefault(), currentDisliked.removeClass("liked"), currentDisliked.addClass("like-button");
        var iElement = currentDisliked.find('i');
        if(iElement.hasClass('fa-heart')){
        	iElement.removeClass('fa-heart');
        	iElement.addClass('fa-heart-o');
        }
        var b = "#pl_" + currentDisliked.data("postid");

        currentDisliked.attr('disabled',true);

        var currentLikes = currentDisliked.data("likes");
        currentDisliked.data("likes", currentLikes- 1);
        jQuery(b).html(currentDisliked.data("likes")), jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: "action=buslike&nonce=" + ajaxnonce + "&act=dislike&post-id=" + currentDisliked.data("postid"),
            success: function() {currentDisliked.attr('disabled',false);}
        })
    });

    jQuery(document).on('update_checkout updated_wc_div',function(){
    	if ( ! jQuery('.wcmenucart-contents').length)return;
    	jQuery('.wcmenucart-contents').css('opacity',0.5);
    	jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: "action=cart_content_count&nonce=" + ajaxnonce,
			success: function(result) {
				jQuery('.wcmenucart-contents .count').html(result);
				jQuery('.wcmenucart-contents').css('opacity',1);
			}
		});
    });


    var DropDn, dpdnopn = !1;

    //handle liking when user is not logged in
	jQuery(document).on("click", ".like-btn-no-log", function(event) {
    	if(dpdnopn){DropDn.slideUp("fast")}
    	//var selector = jQuery(this).closest('.our-offer-slider').length ? '.our-offer-slider' : '.wyz_offers.type-wyz_offers.sin-offer-item';
        event.preventDefault();
        var isPost = jQuery(this).parent().parent('.wall-post').length;
        if(isPost) {
        	var x = jQuery(this).position().left -11, y = 30;
        } else {
        	var x = jQuery(this).position().left -215, y = -200;
        }
        dpdnopn = true, DropDn = LikeDropdown.getDropdown(x, y, false), jQuery(this).parent().append(DropDn);
        DropDn = jQuery(this).parent().find(".login-dropdown");
        if(!isPost&&jQuery(window).width()<480){
        	DropDn.css({'width':'200px','height':'auto','top':'-220px','left': '-117px'});
        }
    	DropDn.slideDown("slow");
    });

	jQuery("body").click(function() {
        dpdnopn && DropDn!=undefined&&(DropDn.slideUp("fast"), DropDn.remove(), dpdnopn = !1);
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

	function refreshSlidersWidth(catExists,locExists,offExists,recExists,featExists,busOffExists){
		var wid;
		if(catExists){
			var cat = jQuery('.category-search-area');
			wid = cat.width();
			if(wid<480){
				cat.addClass('category-search-area-sml');
			}
			else if(wid<768){
				cat.addClass('category-search-area-med');
			}
			else if(wid<992){
				cat.addClass('category-search-area-lrg');
			}
		}
		if(locExists){
			var loc = jQuery('.location-search-area');
			wid = loc.width();
			if(wid<480){
				loc.addClass('location-search-area-sml');
			}
			else if(wid<768){
				loc.addClass('location-search-area-med');
			}
			else if(wid<992){
				loc.addClass('location-search-area-lrg');
			}
		}
		if(offExists){
			
			jQuery('.our-offer-area').each(function(){
				wid = jQuery(this).width();
				if(wid<480){
				jQuery(this).addClass('our-offer-area-sml');
				}
				else if(wid<768){
					jQuery(this).addClass('our-offer-area-med');
				}
				else {
					jQuery(this).addClass('our-offer-area-lrg');
				}
			});
		}
		if(recExists){
			var cat = jQuery('.recently-added-area');
			wid = cat.width();
			if(wid<480){
				cat.addClass('recently-added-area-sml');
			}
			else if(wid<768){
				cat.addClass('recently-added-area-med');
			}
			else if(wid<992){
				cat.addClass('recently-added-area-lrg');
			}
		}
		if(recExists){
			var feat = jQuery('.featured-area');
			wid = feat.width();
			if(wid<480){
				feat.addClass('featured-area-sml');
			}
			else if(wid<768){
				feat.addClass('featured-area-med');
			}
			else if(wid<992){
				feat.addClass('featured-area-lrg');
			}
		}
		var busOffAreaName = ( 1 == wyz_template_type ? '.business-offers-area' : '.our-offer-area' );
		if(busOffExists){
			jQuery(busOffAreaName).each(function(){
				wid = jQuery(this).width();
				if(wid<480){
				jQuery(this).addClass('our-offer-area-sml');
				}
				else if(wid<768){
					jQuery(this).addClass('our-offer-area-med');
				}
				else {
					jQuery(this).addClass('our-offer-area-lrg');
				}
			});
		}
	}

	function showFavDrpDn(target) {
    	if(dpdnopn){DropDn.slideUp("fast")}
        dpdnopn = true;
        DropDn = LikeDropdown.getDropdown(target.position().left +35, 500, false);
        target.parents('.map-info-links').append(DropDn);
    	DropDn = target.parents('.map-info-links').find(".login-dropdown");
    	DropDn.slideDown("slow");
    }

	jQuery('.fav-bus').live('click',favoriteBus);
	function favoriteBus(event){
		event.preventDefault();
		if(jQuery(this).hasClass('fav-no-log')){
			if(jQuery(this).parents('.map-info-links').length)
				showFavDrpDn(jQuery(this));
			return;

		}
		var bus_id = jQuery(this).data('busid');
		if( '' == bus_id || undefined == bus_id ) return;
		var isFav = jQuery(this).data('action');
		jQuery(this).parent().addClass('fade-loading');
		jQuery(this).unbind('favoriteBus');
		var favType = isFav == 0 ? 'unfav' : 'fav';
		var target = jQuery(this);

		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: "action=business_favorite&nonce=" + ajaxnonce + "&business_id=" + bus_id + "&fav_type=" + favType,
			success: function(result) {
				target.parent().removeClass('fade-loading');
				if(favType=='fav'){
					target.find('i').removeClass('fa-heart-o');
					target.find('i').addClass('fa-heart');
					target.data('action',0 );
				} else {
					target.find('i').removeClass('fa-heart');
					target.find('i').addClass('fa-heart-o');
					target.data('action',1 );
				}
			}
		});
	}

	jQuery( document.body ).on( 'added_to_cart', function(resp){
	    if( 'added_to_cart' == resp.type ) {
	        jQuery('.wcmenucart-contents span.count').each(function(){
	            jQuery(this).text(parseInt(jQuery(this).html())+1); 
            });
            jQuery('.wcmenucart').each(function(){
            	jQuery(this).removeClass('hdn');
            	jQuery(this).find('.wcmenucart-contents').attr('href',general.cartUrl);
            });
	    }
	});

	jQuery( document.body ).on( 'removed_from_cart', function(resp){
	    if( 'removed_from_cart' == resp.type ) {
	        jQuery('.wcmenucart-contents span.count').each(function(){
	        	var val = parseInt(jQuery(this).html());
	        	if ( isNaN(val) ) val = 0;
	        	val--;
	        	if ( general.hideEmptyCart && val < 1 ) {
	        		jQuery('.wcmenucart').each(function(){
	        			addClass('hidden');
	        		});
	        	} else if( !general.hideEmptyCart && val < 1 ){
	        		jQuery('.wcmenucart').each(function(){
	        			removeClass('hidden');
	        			jQuery(this).find('.wcmenucart-contents').attr('href',general.shopUrl);
	        		});
					jQuery(this).text(val); 
	        	}
            });
            jQuery('.wcmenucart').each(function(){
            	removeClass('hidden');
            });
	    }
	});

	

	var cont = jQuery('#acount-btn-content');
	var ddo = false;
	if(1==wyz_template_type && user_logged_in && cont.length) {

		var myAccBtn = jQuery('#my-account-btn');

		jQuery.fn.rotate = function(degrees) {
		    jQuery(this).css({'-webkit-transform' : 'rotate('+ degrees +'deg)',
		                 '-moz-transform' : 'rotate('+ degrees +'deg)',
		                 '-ms-transform' : 'rotate('+ degrees +'deg)',
		                 'transform' : 'rotate('+ degrees +'deg)'});
		    return jQuery(this);
		};


		myAccBtn.on('click', function(e){
			e.preventDefault();
			if (!ddo) {
				ddo = true;
				cont.slideDown("fast");
				myAccBtn.find('i').rotate(90);
				setTimeout(function(){jQuery("body").click(hideAccBtnDropdown);}, 100);
			}
		});
	}

	function hideAccBtnDropdown(e) {
		jQuery("body").unbind( 'click', hideAccBtnDropdown);
		ddo = false;
		myAccBtn.find('i').rotate(0);
		jQuery(cont).slideUp("fast");
	}



	// Lazy load
	window.lazySizesConfig = window.lazySizesConfig || {};
	//page is optimized for fast onload event
	lazySizesConfig.loadMode = 2;
	lazySizesConfig.expFactor = 4;
});

function WyzAjax (target) {
	target.appendChild('<div class="loading-spin"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></div>');

	this.done = function(){
		target.removeChild(target.getElementsByClassName('loading-spin'));
	}
}
