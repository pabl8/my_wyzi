var wyz_map_loaded = false;
var selectizeExceptions = undefined;
jQuery(document).ready(function(){
	/*
	 * Range slider.
	 */
	!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var t;t="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,t.rangesliderJs=e()}}(function(){return function e(t,n,i){function s(o,a){if(!n[o]){if(!t[o]){var l="function"==typeof require&&require;if(!a&&l)return l(o,!0);if(r)return r(o,!0);var h=new Error("Cannot find module '"+o+"'");throw h.code="MODULE_NOT_FOUND",h}var u=n[o]={exports:{}};t[o][0].call(u.exports,function(e){var n=t[o][1][e];return s(n?n:e)},u,u.exports,e,t,n,i)}return n[o].exports}for(var r="function"==typeof require&&require,o=0;o<i.length;o++)s(i[o]);return s}({1:[function(e,t,n){function i(e,t,n){return n>t?t>e?t:e>n?n:e:n>e?n:e>t?t:e}t.exports=i},{}],2:[function(e,t,n){"use strict";function i(e,t,n){var i=e.getElementById(t);if(i)n(i);else{var s=e.getElementsByTagName("head")[0];i=e.createElement("style"),null!=t&&(i.id=t),n(i),s.appendChild(i)}return i}t.exports=function(e,t,n){var s=t||document;if(s.createStyleSheet){var r=s.createStyleSheet();return r.cssText=e,r.ownerNode}return i(s,n,function(t){t.styleSheet?t.styleSheet.cssText=e:t.innerHTML=e})},t.exports.byUrl=function(e){if(document.createStyleSheet)return document.createStyleSheet(e).ownerNode;var t=document.getElementsByTagName("head")[0],n=document.createElement("link");return n.rel="stylesheet",n.href=e,t.appendChild(n),n}},{}],3:[function(e,t,n){(function(e){function n(){try{var e=new i("cat",{detail:{foo:"bar"}});return"cat"===e.type&&"bar"===e.detail.foo}catch(e){}return!1}var i=e.CustomEvent;t.exports=n()?i:"function"==typeof document.createEvent?function(e,t){var n=document.createEvent("CustomEvent");return t?n.initCustomEvent(e,t.bubbles,t.cancelable,t.detail):n.initCustomEvent(e,!1,!1,void 0),n}:function(e,t){var n=document.createEventObject();return n.type=e,t?(n.bubbles=Boolean(t.bubbles),n.cancelable=Boolean(t.cancelable),n.detail=t.detail):(n.bubbles=!1,n.cancelable=!1,n.detail=void 0),n}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],4:[function(e,t,n){var i=e("date-now");t.exports=function(e,t,n){function s(){var u=i()-l;t>u&&u>0?r=setTimeout(s,t-u):(r=null,n||(h=e.apply(a,o),r||(a=o=null)))}var r,o,a,l,h;return null==t&&(t=100),function(){a=this,o=arguments,l=i();var u=n&&!r;return r||(r=setTimeout(s,t)),u&&(h=e.apply(a,o),a=o=null),h}}},{"date-now":5}],5:[function(e,t,n){function i(){return(new Date).getTime()}t.exports=Date.now||i},{}],6:[function(e,t,n){"use strict";var i=function(e){return"number"==typeof e&&!isNaN(e)},s=function(e,t){t=t||t.currentTarget;var n=t.getBoundingClientRect(),s=e.originalEvent||e,r=e.touches&&e.touches.length,o=0,a=0;return r?i(e.touches[0].pageX)&&i(e.touches[0].pageY)?(o=e.touches[0].pageX,a=e.touches[0].pageY):i(e.touches[0].clientX)&&i(e.touches[0].clientY)&&(o=s.touches[0].clientX,a=s.touches[0].clientY):i(e.pageX)&&i(e.pageY)?(o=e.pageX,a=e.pageY):e.currentPoint&&i(e.currentPoint.x)&&i(e.currentPoint.y)&&(o=e.currentPoint.x,a=e.currentPoint.y),{x:o-n.left,y:a-n.top}};t.exports=s},{}],7:[function(e,t,n){"use strict";var i=e("number-is-nan");t.exports=Number.isFinite||function(e){return!("number"!=typeof e||i(e)||e===1/0||e===-(1/0))}},{"number-is-nan":8}],8:[function(e,t,n){"use strict";t.exports=Number.isNaN||function(e){return e!==e}},{}],9:[function(e,t,n){function i(e,t){t=t||{},this.element=e,this.options=t,this.onSlideEventsCount=-1,this.isInteracting=!1,this.needTriggerEvents=!1,this.identifier="js-"+l.PLUGIN_NAME+"-"+h++,this.min=a.getFirstNumberLike(t.min,parseFloat(e.getAttribute("min")),0),this.max=a.getFirstNumberLike(t.max,parseFloat(e.getAttribute("max")),l.MAX_SET_BY_DEFAULT),this.value=a.getFirstNumberLike(t.value,parseFloat(e.value),this.min+(this.max-this.min)/2),this.step=a.getFirstNumberLike(t.step,parseFloat(e.getAttribute("step")),l.STEP_SET_BY_DEFAULT),this.percent=null,this._updatePercentFromValue(),this.toFixed=d(this.step),this.range=u(l.RANGE_CLASS),this.range.id=this.identifier,this.fillBg=u(l.FILL_BG_CLASS),this.fill=u(l.FILL_CLASS),this.handle=u(l.HANDLE_CLASS),["fillBg","fill","handle"].forEach(function(e){this.range.appendChild(this[e])},this),["min","max","step"].forEach(function(t){e.setAttribute(t,""+this[t])},this),this._setValue(this.value),a.insertAfter(e,this.range),e.style.position="absolute",e.style.width="1px",e.style.height="1px",e.style.overflow="hidden",e.style.opacity="0",["_update","_handleDown","_handleMove","_handleEnd","_startEventListener","_changeEventListener"].forEach(function(e){this[e]=this[e].bind(this)},this),this._init(),window.addEventListener("resize",r(this._update,l.HANDLE_RESIZE_DEBOUNCE)),l.START_EVENTS.forEach(function(e){this.range.addEventListener(e,this._startEventListener)},this),e.addEventListener("change",this._changeEventListener)}e("./styles/base.css");var s=e("clamp"),r=e("debounce"),o=e("ev-pos"),a=e("./utils"),l={MAX_SET_BY_DEFAULT:100,HANDLE_RESIZE_DEBOUNCE:100,RANGE_CLASS:"rangeslider",FILL_CLASS:"range_fill",FILL_BG_CLASS:"range_fill_bg",HANDLE_CLASS:"range_handle",DISABLED_CLASS:"range-disabled",STEP_SET_BY_DEFAULT:1,START_EVENTS:["mousedown","touchstart","pointerdown"],MOVE_EVENTS:["mousemove","touchmove","pointermove"],END_EVENTS:["mouseup","touchend","pointerup"],PLUGIN_NAME:"rangeslider-js"},h=0,u=function(e){var t=document.createElement("div");return t.classList.add(e),t},d=function(e){return(e+"").replace(".","").length-1};i.prototype.constructor=i,i.prototype._init=function(){this.options.onInit&&this.options.onInit(),this._update()},i.prototype._updatePercentFromValue=function(){this.percent=(this.value-this.min)/(this.max-this.min)},i.prototype._startEventListener=function(e,t){var n=e.target,i=!1,s=this.identifier;a.forEachAncestorsAndSelf(n,function(e){return i=e.id===s&&!e.classList.contains(l.DISABLED_CLASS)}),i&&this._handleDown(e,t)},i.prototype._changeEventListener=function(e,t){t&&t.origin===this.identifier||this._setPosition(this._getPositionFromValue(e.target.value))},i.prototype._update=function(){this.handleWidth=a.getDimension(this.handle,"offsetWidth"),this.rangeWidth=a.getDimension(this.range,"offsetWidth"),this.maxHandleX=this.rangeWidth-this.handleWidth,this.grabX=this.handleWidth/2,this.position=this._getPositionFromValue(this.value),this.range.classList[this.element.disabled?"add":"remove"](l.DISABLED_CLASS),this._setPosition(this.position),this._updatePercentFromValue(),a.emit(this.element,"change")},i.prototype._listen=function(e){var t=(e?"add":"remove")+"EventListener";l.MOVE_EVENTS.forEach(function(e){document[t](e,this._handleMove)},this),l.END_EVENTS.forEach(function(e){document[t](e,this._handleEnd),this.range[t](e,this._handleEnd)},this)},i.prototype._handleDown=function(e){if(e.preventDefault(),this.isInteracting=!0,this._listen(!0),!e.target.classList.contains(l.HANDLE_CLASS)){var t=o(e,this.range).x,n=this.range.getBoundingClientRect().left,i=this.handle.getBoundingClientRect().left-n;this._setPosition(t-this.grabX),t>=i&&t<i+this.handleWidth&&(this.grabX=t-i),this._updatePercentFromValue()}},i.prototype._handleMove=function(e){this.isInteracting=!0,e.preventDefault();var t=o(e,this.range).x;this._setPosition(t-this.grabX)},i.prototype._handleEnd=function(e){e.preventDefault(),this._listen(!1),a.emit(this.element,"change",{origin:this.identifier}),(this.isInteracting||this.needTriggerEvents)&&this.options.onSlideEnd&&this.options.onSlideEnd(this.value,this.percent,this.position),this.onSlideEventsCount=0,this.isInteracting=!1},i.prototype._setPosition=function(e){var t=this._getValueFromPosition(s(e,0,this.maxHandleX)),n=this._getPositionFromValue(t);this.fill.style.width=n+this.grabX+"px",this.handle.style.webkitTransform=this.handle.style.transform="translate("+n+"px, 0px)",this._setValue(t),this.position=n,this.value=t,this._updatePercentFromValue(),(this.isInteracting||this.needTriggerEvents)&&(this.options.onSlideStart&&0===this.onSlideEventsCount&&this.options.onSlideStart(this.value,this.percent,this.position),this.options.onSlide&&this.options.onSlide(this.value,this.percent,this.position)),this.onSlideEventsCount++},i.prototype._getPositionFromValue=function(e){var t=(e-this.min)/(this.max-this.min);return t*this.maxHandleX},i.prototype._getValueFromPosition=function(e){var t=e/(this.maxHandleX||1),n=this.step*Math.round(t*(this.max-this.min)/this.step)+this.min;return Number(n.toFixed(this.toFixed))},i.prototype._setValue=function(e){e===this.value&&e===this.element.value||(this.value=this.element.value=e,a.emit(this.element,"input",{origin:this.identifier}))},i.prototype.update=function(e,t){return e=e||{},this.needTriggerEvents=!!t,a.isFiniteNumber(e.min)&&(this.element.setAttribute("min",""+e.min),this.min=e.min),a.isFiniteNumber(e.max)&&(this.element.setAttribute("max",""+e.max),this.max=e.max),a.isFiniteNumber(e.step)&&(this.element.setAttribute("step",""+e.step),this.step=e.step,this.toFixed=d(e.step)),a.isFiniteNumber(e.value)&&this._setValue(e.value),this._update(),this.onSlideEventsCount=0,this.needTriggerEvents=!1,this},i.prototype.destroy=function(){window.removeEventListener("resize",this._update,!1),l.START_EVENTS.forEach(function(e){this.range.removeEventListener(e,this._startEventListener)},this),this.element.removeEventListener("change",this._changeEventListener),this.element.style.cssText="",delete this.element[l.PLUGIN_NAME],this.range.parentNode.removeChild(this.range)},i.create=function(e,t){function n(e){e[l.PLUGIN_NAME]=e[l.PLUGIN_NAME]||new i(e,t)}e.length?Array.prototype.slice.call(e).forEach(function(e){n(e)}):n(e)},t.exports=i},{"./styles/base.css":10,"./utils":11,clamp:1,debounce:4,"ev-pos":6}],10:[function(e,t,n){var i=e("./../../node_modules/cssify"),s=".rangeslider {\n    position: relative;\n    cursor: pointer;\n   width: 100%;\n}\n.rangeslider,\n.rangeslider__fill,\n.rangeslider__fill__bg {\n    display: block;\n}\n.rangeslider__fill,\n.rangeslider__fill__bg,\n.rangeslider__handle {\n    position: absolute;\n}\n.rangeslider__fill,\n.rangeslider__fill__bg {\n    top: calc(50% - 6px);\n    height: 12px;\n    z-index: 2;\n    background: #29e;\n    border-radius: 10px;\n    will-change: width;\n}\n.rangeslider__handle {\n    display: inline-block;\n    top: calc(50% - 15px);\n    background: #29e;\n    width: 30px;\n    height: 30px;\n    z-index: 3;\n    cursor: pointer;\n    border: solid 2px #ffffff;\n    border-radius: 50%;\n}\n.rangeslider__handle:active {\n    background: #107ecd;\n}\n.rangeslider__fill__bg {\n    background: #ccc;\n    width: 100%;\n}\n.rangeslider--disabled {\n    opacity: 0.4;\n}\n.rangeslider--slim .rangeslider {\n    height: 25px;\n}\n.rangeslider--slim .rangeslider:active .rangeslider__handle {\n    width: 21px;\n    height: 21px;\n    top: calc(50% - 10px);\n    background: #29e;\n}\n.rangeslider--slim .rangeslider__fill,\n.rangeslider--slim .rangeslider__fill__bg {\n    top: calc(50% - 1px);\n    height: 2px;\n}\n.rangeslider--slim .rangeslider__handle {\n    will-change: width, height, top;\n    -webkit-transition: width 0.1s ease-in-out, height 0.1s ease-in-out, top 0.1s ease-in-out;\n    transition: width 0.1s ease-in-out, height 0.1s ease-in-out, top 0.1s ease-in-out;\n    width: 14px;\n    height: 14px;\n    top: calc(50% - 7px);\n}\n";i(s,void 0,"_1fcddbb"),t.exports=s},{"./../../node_modules/cssify":2}],11:[function(e,t,n){function i(e){return!(0!==e.offsetWidth&&0!==e.offsetHeight&&e.open!==!1)}function s(e){return d(parseFloat(e))||d(e)}function r(){if(!arguments.length)return null;for(var e=0,t=arguments.length;t>e;e++)if(s(arguments[e]))return arguments[e]}function o(e){for(var t=[],n=e.parentNode;n&&i(n);)t.push(n),n=n.parentNode;return t}function a(e,t){function n(e){"undefined"!=typeof e.open&&(e.open=!e.open)}var i,s=o(e),r=s.length,a=e[t],l=[],h=0;if(r){for(h=0;r>h;h++)i=s[h].style,l[h]=i.display,i.display="block",i.height="0",i.overflow="hidden",i.visibility="hidden",n(s[h]);for(a=e[t],h=0;r>h;h++)i=s[h].style,n(s[h]),i.display=l[h],i.height="",i.overflow="",i.visibility=""}return a}function l(e,t){for(t(e);e.parentNode&&!t(e);)e=e.parentNode;return e}function h(e,t){e.parentNode.insertBefore(t,e.nextSibling)}var u=e("custom-event"),d=e("is-finite");t.exports={emit:function(e,t,n){e.dispatchEvent(new u(t,n))},isFiniteNumber:d,getFirstNumberLike:r,getDimension:a,insertAfter:h,forEachAncestorsAndSelf:l}},{"custom-event":3,"is-finite":7}]},{},[9])(9)});


	//booking filter
	if(jQuery('.booking-filter-date').length){
		jQuery('.booking-filter-date').datepicker({
			dateFormat : 'mm_dd_yy'
		});
	}
	jQuery(document).trigger('wyzSelectizeInit');

	if( jQuery('.wyz-selectize-filter').length){
		jQuery('.wyz-selectize-filter').each(function(index){

			if(undefined!=selectizeExceptions && selectizeExceptions.indexOf(jQuery(this).attr('name')) > -1    )
				return;
			jQuery(this).selectize({
				create: false,
				plugins: ['remove_button']
			});
		});
	}
	if(jQuery('.bus-filter>select').length) {
		jQuery('.bus-filter>select').each(function(){
			var $this = jQuery(this);
			if (!$this.val()) {
				$this.addClass('busfilternovalue');
			} else {
				$this.removeClass('busfilternovalue');
			}
		});
		jQuery('.bus-filter>select').on('change', function(){
			var $this = jQuery(this);

			if (!$this.val()) {
				$this.addClass('busfilternovalue');
			} else {
				$this.removeClass('busfilternovalue');
			}
		});
	}

	if(jQuery('.number-filter-range').length) {
		jQuery('.number-filter-range').each(function(){
			rangesliderJs.create(jQuery(this),{});
			jQuery(this).on('input', function() {
				jQuery(this).parent().find('.slider-val').html( jQuery(this).val() );
			});
			jQuery(this).trigger('input');
		});
	}
});

function updateRanges(){
	jQuery('.number-filter-range').each(function(){
		jQuery(this).rangeslider('update', true);
	});
}



var wyz_filter_dom_loaded = false;
var wyz_filter_map_loaded = false;
document.addEventListener('DOMContentLoaded', function() {
	wyz_filter_dom_loaded = true;
	wyz_init_load_filter_text_field();
}, false);

function wyz_init_load_filter_text_field() {
	if(wyz_filter_map_loaded)return;
	if (typeof google === 'object' && typeof google.maps === 'object' && wyz_filter_dom_loaded ) {
		wyz_filter_map_loaded = true;
		wyzLocationFilterFieldInit();
	}
}

function wyzLocationFilterFieldInit() {
	wyz_filter_map_loaded = true;
	if ( ! jQuery('.bus-filter-locations-text').length) return;
	var filter = document.getElementsByClassName("bus-filter-locations-text");
	var currentFilterTarget;
	for(var i=0;i<filter.length;i++){
		jQuery(filter[i]).click(function(){currentFilterTarget = jQuery(this)});
		var autocomplete = new google.maps.places.Autocomplete(filter[i]);
		google.maps.event.addListener(autocomplete, 'place_changed', function () {
			var place = autocomplete.getPlace();
			currentFilterTarget.siblings('.loc-filter-txt').val(place.name);
			currentFilterTarget.siblings('.loc-filter-lat').val(place.geometry.location.lat());
			currentFilterTarget.siblings('.loc-filter-lon').val(place.geometry.location.lng());

		});
	}
}


