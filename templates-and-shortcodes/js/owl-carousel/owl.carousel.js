window.matchMedia||(window.matchMedia=function(){"use strict";var e=window.styleMedia||window.media;if(!e){var t=document.createElement("style"),i=document.getElementsByTagName("script")[0],n=null;t.type="text/css",t.id="matchmediajs-test",i.parentNode.insertBefore(t,i),n="getComputedStyle"in window&&window.getComputedStyle(t,null)||t.currentStyle,e={matchMedium:function(e){var i="@media "+e+"{ #matchmediajs-test { width: 1px; } }";return t.styleSheet?t.styleSheet.cssText=i:t.textContent=i,"1px"===n.width}}}return function(t){return{matches:e.matchMedium(t||"all"),media:t||"all"}}}());
!function(){if(window.matchMedia&&window.matchMedia("all").addListener)return!1;var e=window.matchMedia,n=e("only all").matches,i=!1,t=0,a=[],r=function(n){clearTimeout(t),t=setTimeout(function(){for(var n=0,i=a.length;i>n;n++){var t=a[n].mql,r=a[n].listeners||[],o=e(t.media).matches;if(o!==t.matches){t.matches=o;for(var c=0,d=r.length;d>c;c++)r[c].call(window,t)}}},30)};window.matchMedia=function(t){var o=e(t),c=[],d=0;return o.addListener=function(e){n&&(i||(i=!0,window.addEventListener("resize",r,!0)),0===d&&(d=a.push({mql:o,listeners:c})),c.push(e))},o.removeListener=function(e){for(var n=0,i=c.length;i>n;n++)c[n]===e&&c.splice(n,1)},o}}();

var isMobile=false;
jQuery(document).ready(function(){
	isMobile=window.matchMedia("only screen and (max-width: 760px)");
});

/*carousel*/
!function(t,e,i,s){function n(e,i){this.settings=null,this.options=t.extend({},n.Defaults,i),this.$element=t(e),this._handlers={},this._plugins={},this._supress={},this._current=null,this._speed=null,this._coordinates=[],this._breakpoint=null,this._width=null,this._items=[],this._clones=[],this._mergers=[],this._widths=[],this._invalidated={},this._pipe=[],this._drag={time:null,target:null,pointer:null,stage:{start:null,current:null},direction:null},this._states={current:{},tags:{initializing:["busy"],animating:["busy"],dragging:["interacting"]}},t.each(["onResize","onThrottledResize"],t.proxy(function(e,i){this._handlers[i]=t.proxy(this[i],this)},this)),t.each(n.Plugins,t.proxy(function(t,e){this._plugins[t.charAt(0).toLowerCase()+t.slice(1)]=new e(this)},this)),t.each(n.Workers,t.proxy(function(e,i){this._pipe.push({filter:i.filter,run:t.proxy(i.run,this)})},this)),this.setup(),this.initialize()}n.Defaults={items:3,loop:!1,center:!1,rewind:!1,checkVisibility:!0,mouseDrag:!0,touchDrag:!0,pullDrag:!0,freeDrag:!1,margin:0,stagePadding:0,merge:!1,mergeFit:!0,autoWidth:!1,startPosition:0,rtl:!1,smartSpeed:250,fluidSpeed:!1,dragEndSpeed:!1,responsive:{},responsiveRefreshRate:200,responsiveBaseElement:e,fallbackEasing:"swing",slideTransition:"",info:!1,nestedItemSelector:!1,itemElement:"div",stageElement:"div",refreshClass:"owl-refresh",loadedClass:"owl-loaded",loadingClass:"owl-loading",rtlClass:"owl-rtl",responsiveClass:"owl-responsive",dragClass:"owl-drag",itemClass:"owl-item",stageClass:"owl-stage",stageOuterClass:"owl-stage-outer",grabClass:"owl-grab"},n.Width={Default:"default",Inner:"inner",Outer:"outer"},n.Type={Event:"event",State:"state"},n.Plugins={},n.Workers=[{filter:["width","settings"],run:function(){this._width=this.$element.width()}},{filter:["width","items","settings"],run:function(t){t.current=this._items&&this._items[this.relative(this._current)]}},{filter:["items","settings"],run:function(){this.$stage.children(".cloned").remove()}},{filter:["width","items","settings"],run:function(t){var e=this.settings.margin||"",i=!this.settings.autoWidth,s=this.settings.rtl,n={width:"auto","margin-left":s?e:"","margin-right":s?"":e};!i&&this.$stage.children().css(n),t.css=n}},{filter:["width","items","settings"],run:function(t){var e=(this.width()/this.settings.items).toFixed(3)-this.settings.margin,i=null,s=this._items.length,n=!this.settings.autoWidth,r=[];for(t.items={merge:!1,width:e};s--;)i=this._mergers[s],i=this.settings.mergeFit&&Math.min(i,this.settings.items)||i,t.items.merge=i>1||t.items.merge,r[s]=n?e*i:this._items[s].width();this._widths=r}},{filter:["items","settings"],run:function(){var e=[],i=this._items,s=this.settings,n=Math.max(2*s.items,4),r=2*Math.ceil(i.length/2),a=s.loop&&i.length?s.rewind?n:Math.max(n,r):0,h="",o="";for(a/=2;a>0;)e.push(this.normalize(e.length/2,!0)),h+=i[e[e.length-1]][0].outerHTML,e.push(this.normalize(i.length-1-(e.length-1)/2,!0)),o=i[e[e.length-1]][0].outerHTML+o,a-=1;this._clones=e,t(h).addClass("cloned").appendTo(this.$stage),t(o).addClass("cloned").prependTo(this.$stage)}},{filter:["width","items","settings"],run:function(){for(var t=this.settings.rtl?1:-1,e=this._clones.length+this._items.length,i=-1,s=0,n=0,r=[];++i<e;)s=r[i-1]||0,n=this._widths[this.relative(i)]+this.settings.margin,r.push(s+n*t);this._coordinates=r}},{filter:["width","items","settings"],run:function(){var t=this.settings.stagePadding,e=this._coordinates,i={width:Math.ceil(Math.abs(e[e.length-1]))+2*t,"padding-left":t||"","padding-right":t||""};this.$stage.css(i)}},{filter:["width","items","settings"],run:function(t){var e=this._coordinates.length,i=!this.settings.autoWidth,s=this.$stage.children();if(i&&t.items.merge)for(;e--;)t.css.width=this._widths[this.relative(e)],s.eq(e).css(t.css);else i&&(t.css.width=t.items.width,s.css(t.css))}},{filter:["items"],run:function(){this._coordinates.length<1&&this.$stage.removeAttr("style")}},{filter:["width","items","settings"],run:function(t){t.current=t.current?this.$stage.children().index(t.current):0,t.current=Math.max(this.minimum(),Math.min(this.maximum(),t.current)),this.reset(t.current)}},{filter:["position"],run:function(){this.animate(this.coordinates(this._current))}},{filter:["width","position","items","settings"],run:function(){var t,e,i,s,n=this.settings.rtl?1:-1,r=2*this.settings.stagePadding,a=this.coordinates(this.current())+r,h=a+this.width()*n,o=[];for(i=0,s=this._coordinates.length;i<s;i++)t=this._coordinates[i-1]||0,e=Math.abs(this._coordinates[i])+r*n,(this.op(t,"<=",a)&&this.op(t,">",h)||this.op(e,"<",a)&&this.op(e,">",h))&&o.push(i);this.$stage.children(".active").removeClass("active"),this.$stage.children(":eq("+o.join("), :eq(")+")").addClass("active"),this.$stage.children(".center").removeClass("center"),this.settings.center&&this.$stage.children().eq(this.current()).addClass("center")}}],n.prototype.initializeStage=function(){this.$stage=this.$element.find("."+this.settings.stageClass),this.$stage.length||(this.$element.addClass(this.options.loadingClass),this.$stage=t("<"+this.settings.stageElement+">",{class:this.settings.stageClass}).wrap(t("<div/>",{class:this.settings.stageOuterClass})),this.$element.append(this.$stage.parent()))},n.prototype.initializeItems=function(){var e=this.$element.find(".owl-item");if(e.length)return this._items=e.get().map(function(e){return t(e)}),this._mergers=this._items.map(function(){return 1}),void this.refresh();this.replace(this.$element.children().not(this.$stage.parent())),this.isVisible()?this.refresh():this.invalidate("width"),this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass)},n.prototype.initialize=function(){var t,e,i;(this.enter("initializing"),this.trigger("initialize"),this.$element.toggleClass(this.settings.rtlClass,this.settings.rtl),this.settings.autoWidth&&!this.is("pre-loading"))&&(t=this.$element.find("img"),e=this.settings.nestedItemSelector?"."+this.settings.nestedItemSelector:s,i=this.$element.children(e).width(),t.length&&i<=0&&this.preloadAutoWidthImages(t));this.initializeStage(),this.initializeItems(),this.registerEventHandlers(),this.leave("initializing"),this.trigger("initialized")},n.prototype.isVisible=function(){return!this.settings.checkVisibility||this.$element.is(":visible")},n.prototype.setup=function(){var e=this.viewport(),i=this.options.responsive,s=-1,n=null;i?(t.each(i,function(t){t<=e&&t>s&&(s=Number(t))}),"function"==typeof(n=t.extend({},this.options,i[s])).stagePadding&&(n.stagePadding=n.stagePadding()),delete n.responsive,n.responsiveClass&&this.$element.attr("class",this.$element.attr("class").replace(new RegExp("("+this.options.responsiveClass+"-)\\S+\\s","g"),"$1"+s))):n=t.extend({},this.options),this.trigger("change",{property:{name:"settings",value:n}}),this._breakpoint=s,this.settings=n,this.invalidate("settings"),this.trigger("changed",{property:{name:"settings",value:this.settings}})},n.prototype.optionsLogic=function(){this.settings.autoWidth&&(this.settings.stagePadding=!1,this.settings.merge=!1)},n.prototype.prepare=function(e){var i=this.trigger("prepare",{content:e});return i.data||(i.data=t("<"+this.settings.itemElement+"/>").addClass(this.options.itemClass).append(e)),this.trigger("prepared",{content:i.data}),i.data},n.prototype.update=function(){for(var e=0,i=this._pipe.length,s=t.proxy(function(t){return this[t]},this._invalidated),n={};e<i;)(this._invalidated.all||t.grep(this._pipe[e].filter,s).length>0)&&this._pipe[e].run(n),e++;this._invalidated={},!this.is("valid")&&this.enter("valid")},n.prototype.width=function(t){switch(t=t||n.Width.Default){case n.Width.Inner:case n.Width.Outer:return this._width;default:return this._width-2*this.settings.stagePadding+this.settings.margin}},n.prototype.refresh=function(){this.enter("refreshing"),this.trigger("refresh"),this.setup(),this.optionsLogic(),this.$element.addClass(this.options.refreshClass),this.update(),this.$element.removeClass(this.options.refreshClass),this.leave("refreshing"),this.trigger("refreshed")},n.prototype.onThrottledResize=function(){e.clearTimeout(this.resizeTimer),this.resizeTimer=e.setTimeout(this._handlers.onResize,this.settings.responsiveRefreshRate)},n.prototype.onResize=function(){return!!this._items.length&&(this._width!==this.$element.width()&&(!!this.isVisible()&&(this.enter("resizing"),this.trigger("resize").isDefaultPrevented()?(this.leave("resizing"),!1):(this.invalidate("width"),this.refresh(),this.leave("resizing"),void this.trigger("resized")))))},n.prototype.registerEventHandlers=function(){t.support.transition&&this.$stage.on(t.support.transition.end+".owl.core",t.proxy(this.onTransitionEnd,this)),!1!==this.settings.responsive&&this.on(e,"resize",this._handlers.onThrottledResize),this.settings.mouseDrag&&(this.$element.addClass(this.options.dragClass),this.$stage.on("mousedown.owl.core",t.proxy(this.onDragStart,this)),this.$stage.on("dragstart.owl.core selectstart.owl.core",function(){return!1})),this.settings.touchDrag&&(this.$stage.on("touchstart.owl.core",t.proxy(this.onDragStart,this)),this.$stage.on("touchcancel.owl.core",t.proxy(this.onDragEnd,this)))},n.prototype.onDragStart=function(e){var s=null;3!==e.which&&(t.support.transform?s={x:(s=this.$stage.css("transform").replace(/.*\(|\)| /g,"").split(","))[16===s.length?12:4],y:s[16===s.length?13:5]}:(s=this.$stage.position(),s={x:this.settings.rtl?s.left+this.$stage.width()-this.width()+this.settings.margin:s.left,y:s.top}),this.is("animating")&&(t.support.transform?this.animate(s.x):this.$stage.stop(),this.invalidate("position")),this.$element.toggleClass(this.options.grabClass,"mousedown"===e.type),this.speed(0),this._drag.time=(new Date).getTime(),this._drag.target=t(e.target),this._drag.stage.start=s,this._drag.stage.current=s,this._drag.pointer=this.pointer(e),t(i).on("mouseup.owl.core touchend.owl.core",t.proxy(this.onDragEnd,this)),t(i).one("mousemove.owl.core touchmove.owl.core",t.proxy(function(e){var s=this.difference(this._drag.pointer,this.pointer(e));t(i).on("mousemove.owl.core touchmove.owl.core",t.proxy(this.onDragMove,this)),Math.abs(s.x)<Math.abs(s.y)&&this.is("valid")||(e.preventDefault(),this.enter("dragging"),this.trigger("drag"))},this)))},n.prototype.onDragMove=function(t){var e=null,i=null,s=null,n=this.difference(this._drag.pointer,this.pointer(t)),r=this.difference(this._drag.stage.start,n);this.is("dragging")&&(t.preventDefault(),this.settings.loop?(e=this.coordinates(this.minimum()),i=this.coordinates(this.maximum()+1)-e,r.x=((r.x-e)%i+i)%i+e):(e=this.settings.rtl?this.coordinates(this.maximum()):this.coordinates(this.minimum()),i=this.settings.rtl?this.coordinates(this.minimum()):this.coordinates(this.maximum()),s=this.settings.pullDrag?-1*n.x/5:0,r.x=Math.max(Math.min(r.x,e+s),i+s)),this._drag.stage.current=r,this.animate(r.x))},n.prototype.onDragEnd=function(e){var s=this.difference(this._drag.pointer,this.pointer(e)),n=this._drag.stage.current,r=s.x>0^this.settings.rtl?"left":"right";t(i).off(".owl.core"),this.$element.removeClass(this.options.grabClass),(0!==s.x&&this.is("dragging")||!this.is("valid"))&&(this.speed(this.settings.dragEndSpeed||this.settings.smartSpeed),this.current(this.closest(n.x,0!==s.x?r:this._drag.direction)),this.invalidate("position"),this.update(),this._drag.direction=r,(Math.abs(s.x)>3||(new Date).getTime()-this._drag.time>300)&&this._drag.target.one("click.owl.core",function(){return!1})),this.is("dragging")&&(this.leave("dragging"),this.trigger("dragged"))},n.prototype.closest=function(e,i){var n=-1,r=this.width(),a=this.coordinates();return this.settings.freeDrag||t.each(a,t.proxy(function(t,h){return"left"===i&&e>h-30&&e<h+30?n=t:"right"===i&&e>h-r-30&&e<h-r+30?n=t+1:this.op(e,"<",h)&&this.op(e,">",a[t+1]!==s?a[t+1]:h-r)&&(n="left"===i?t+1:t),-1===n},this)),this.settings.loop||(this.op(e,">",a[this.minimum()])?n=e=this.minimum():this.op(e,"<",a[this.maximum()])&&(n=e=this.maximum())),n},n.prototype.animate=function(e){var i=this.speed()>0;this.is("animating")&&this.onTransitionEnd(),i&&(this.enter("animating"),this.trigger("translate")),t.support.transform3d&&t.support.transition?this.$stage.css({transform:"translate3d("+e+"px,0px,0px)",transition:this.speed()/1e3+"s"+(this.settings.slideTransition?" "+this.settings.slideTransition:"")}):i?this.$stage.animate({left:e+"px"},this.speed(),this.settings.fallbackEasing,t.proxy(this.onTransitionEnd,this)):this.$stage.css({left:e+"px"})},n.prototype.is=function(t){return this._states.current[t]&&this._states.current[t]>0},n.prototype.current=function(t){if(t===s)return this._current;if(0===this._items.length)return s;if(t=this.normalize(t),this._current!==t){var e=this.trigger("change",{property:{name:"position",value:t}});e.data!==s&&(t=this.normalize(e.data)),this._current=t,this.invalidate("position"),this.trigger("changed",{property:{name:"position",value:this._current}})}return this._current},n.prototype.invalidate=function(e){return"string"===t.type(e)&&(this._invalidated[e]=!0,this.is("valid")&&this.leave("valid")),t.map(this._invalidated,function(t,e){return e})},n.prototype.reset=function(t){(t=this.normalize(t))!==s&&(this._speed=0,this._current=t,this.suppress(["translate","translated"]),this.animate(this.coordinates(t)),this.release(["translate","translated"]))},n.prototype.normalize=function(t,e){var i=this._items.length,n=e?0:this._clones.length;return!this.isNumeric(t)||i<1?t=s:(t<0||t>=i+n)&&(t=((t-n/2)%i+i)%i+n/2),t},n.prototype.relative=function(t){return t-=this._clones.length/2,this.normalize(t,!0)},n.prototype.maximum=function(t){var e,i,s,n=this.settings,r=this._coordinates.length;if(n.loop)r=this._clones.length/2+this._items.length-1;else if(n.autoWidth||n.merge){if(e=this._items.length)for(i=this._items[--e].width(),s=this.$element.width();e--&&!((i+=this._items[e].width()+this.settings.margin)>s););r=e+1}else r=n.center?this._items.length-1:this._items.length-n.items;return t&&(r-=this._clones.length/2),Math.max(r,0)},n.prototype.minimum=function(t){return t?0:this._clones.length/2},n.prototype.items=function(t){return t===s?this._items.slice():(t=this.normalize(t,!0),this._items[t])},n.prototype.mergers=function(t){return t===s?this._mergers.slice():(t=this.normalize(t,!0),this._mergers[t])},n.prototype.clones=function(e){var i=this._clones.length/2,n=i+this._items.length,r=function(t){return t%2==0?n+t/2:i-(t+1)/2};return e===s?t.map(this._clones,function(t,e){return r(e)}):t.map(this._clones,function(t,i){return t===e?r(i):null})},n.prototype.speed=function(t){return t!==s&&(this._speed=t),this._speed},n.prototype.coordinates=function(e){var i,n=1,r=e-1;return e===s?t.map(this._coordinates,t.proxy(function(t,e){return this.coordinates(e)},this)):(this.settings.center?(this.settings.rtl&&(n=-1,r=e+1),i=this._coordinates[e],i+=(this.width()-i+(this._coordinates[r]||0))/2*n):i=this._coordinates[r]||0,i=Math.ceil(i))},n.prototype.duration=function(t,e,i){return 0===i?0:Math.min(Math.max(Math.abs(e-t),1),6)*Math.abs(i||this.settings.smartSpeed)},n.prototype.to=function(t,e){var i=this.current(),s=null,n=t-this.relative(i),r=(n>0)-(n<0),a=this._items.length,h=this.minimum(),o=this.maximum();this.settings.loop?(!this.settings.rewind&&Math.abs(n)>a/2&&(n+=-1*r*a),(s=(((t=i+n)-h)%a+a)%a+h)!==t&&s-n<=o&&s-n>0&&(i=s-n,t=s,this.reset(i))):t=this.settings.rewind?(t%(o+=1)+o)%o:Math.max(h,Math.min(o,t)),this.speed(this.duration(i,t,e)),this.current(t),this.isVisible()&&this.update()},n.prototype.next=function(t){t=t||!1,this.to(this.relative(this.current())+1,t)},n.prototype.prev=function(t){t=t||!1,this.to(this.relative(this.current())-1,t)},n.prototype.onTransitionEnd=function(t){if(t!==s&&(t.stopPropagation(),(t.target||t.srcElement||t.originalTarget)!==this.$stage.get(0)))return!1;this.leave("animating"),this.trigger("translated")},n.prototype.viewport=function(){var s;return this.options.responsiveBaseElement!==e?s=t(this.options.responsiveBaseElement).width():e.innerWidth?s=e.innerWidth:i.documentElement&&i.documentElement.clientWidth?s=i.documentElement.clientWidth:console.warn("Can not detect viewport width."),s},n.prototype.replace=function(e){this.$stage.empty(),this._items=[],e&&(e=e instanceof jQuery?e:t(e)),this.settings.nestedItemSelector&&(e=e.find("."+this.settings.nestedItemSelector)),e.filter(function(){return 1===this.nodeType}).each(t.proxy(function(t,e){e=this.prepare(e),this.$stage.append(e),this._items.push(e),this._mergers.push(1*e.find("[data-merge]").addBack("[data-merge]").attr("data-merge")||1)},this)),this.reset(this.isNumeric(this.settings.startPosition)?this.settings.startPosition:0),this.invalidate("items")},n.prototype.add=function(e,i){var n=this.relative(this._current);i=i===s?this._items.length:this.normalize(i,!0),e=e instanceof jQuery?e:t(e),this.trigger("add",{content:e,position:i}),e=this.prepare(e),0===this._items.length||i===this._items.length?(0===this._items.length&&this.$stage.append(e),0!==this._items.length&&this._items[i-1].after(e),this._items.push(e),this._mergers.push(1*e.find("[data-merge]").addBack("[data-merge]").attr("data-merge")||1)):(this._items[i].before(e),this._items.splice(i,0,e),this._mergers.splice(i,0,1*e.find("[data-merge]").addBack("[data-merge]").attr("data-merge")||1)),this._items[n]&&this.reset(this._items[n].index()),this.invalidate("items"),this.trigger("added",{content:e,position:i})},n.prototype.remove=function(t){(t=this.normalize(t,!0))!==s&&(this.trigger("remove",{content:this._items[t],position:t}),this._items[t].remove(),this._items.splice(t,1),this._mergers.splice(t,1),this.invalidate("items"),this.trigger("removed",{content:null,position:t}))},n.prototype.preloadAutoWidthImages=function(e){e.each(t.proxy(function(e,i){this.enter("pre-loading"),i=t(i),t(new Image).one("load",t.proxy(function(t){i.attr("src",t.target.src),i.css("opacity",1),this.leave("pre-loading"),!this.is("pre-loading")&&!this.is("initializing")&&this.refresh()},this)).attr("src",i.attr("src")||i.attr("data-src")||i.attr("data-src-retina"))},this))},n.prototype.destroy=function(){this.$element.off(".owl.core"),this.$stage.off(".owl.core"),t(i).off(".owl.core"),!1!==this.settings.responsive&&(e.clearTimeout(this.resizeTimer),this.off(e,"resize",this._handlers.onThrottledResize));for(var s in this._plugins)this._plugins[s].destroy();this.$stage.children(".cloned").remove(),this.$stage.unwrap(),this.$stage.children().contents().unwrap(),this.$stage.children().unwrap(),this.$stage.remove(),this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class",this.$element.attr("class").replace(new RegExp(this.options.responsiveClass+"-\\S+\\s","g"),"")).removeData("owl.carousel")},n.prototype.op=function(t,e,i){var s=this.settings.rtl;switch(e){case"<":return s?t>i:t<i;case">":return s?t<i:t>i;case">=":return s?t<=i:t>=i;case"<=":return s?t>=i:t<=i}},n.prototype.on=function(t,e,i,s){t.addEventListener?t.addEventListener(e,i,s):t.attachEvent&&t.attachEvent("on"+e,i)},n.prototype.off=function(t,e,i,s){t.removeEventListener?t.removeEventListener(e,i,s):t.detachEvent&&t.detachEvent("on"+e,i)},n.prototype.trigger=function(e,i,s,r,a){var h={item:{count:this._items.length,index:this.current()}},o=t.camelCase(t.grep(["on",e,s],function(t){return t}).join("-").toLowerCase()),l=t.Event([e,"owl",s||"carousel"].join(".").toLowerCase(),t.extend({relatedTarget:this},h,i));return this._supress[e]||(t.each(this._plugins,function(t,e){e.onTrigger&&e.onTrigger(l)}),this.register({type:n.Type.Event,name:e}),this.$element.trigger(l),this.settings&&"function"==typeof this.settings[o]&&this.settings[o].call(this,l)),l},n.prototype.enter=function(e){t.each([e].concat(this._states.tags[e]||[]),t.proxy(function(t,e){this._states.current[e]===s&&(this._states.current[e]=0),this._states.current[e]++},this))},n.prototype.leave=function(e){t.each([e].concat(this._states.tags[e]||[]),t.proxy(function(t,e){this._states.current[e]--},this))},n.prototype.register=function(e){if(e.type===n.Type.Event){if(t.event.special[e.name]||(t.event.special[e.name]={}),!t.event.special[e.name].owl){var i=t.event.special[e.name]._default;t.event.special[e.name]._default=function(t){return!i||!i.apply||t.namespace&&-1!==t.namespace.indexOf("owl")?t.namespace&&t.namespace.indexOf("owl")>-1:i.apply(this,arguments)},t.event.special[e.name].owl=!0}}else e.type===n.Type.State&&(this._states.tags[e.name]?this._states.tags[e.name]=this._states.tags[e.name].concat(e.tags):this._states.tags[e.name]=e.tags,this._states.tags[e.name]=t.grep(this._states.tags[e.name],t.proxy(function(i,s){return t.inArray(i,this._states.tags[e.name])===s},this)))},n.prototype.suppress=function(e){t.each(e,t.proxy(function(t,e){this._supress[e]=!0},this))},n.prototype.release=function(e){t.each(e,t.proxy(function(t,e){delete this._supress[e]},this))},n.prototype.pointer=function(t){var i={x:null,y:null};return(t=(t=t.originalEvent||t||e.event).touches&&t.touches.length?t.touches[0]:t.changedTouches&&t.changedTouches.length?t.changedTouches[0]:t).pageX?(i.x=t.pageX,i.y=t.pageY):(i.x=t.clientX,i.y=t.clientY),i},n.prototype.isNumeric=function(t){return!isNaN(parseFloat(t))},n.prototype.difference=function(t,e){return{x:t.x-e.x,y:t.y-e.y}},t.fn.owlCarousel=function(e){var i=Array.prototype.slice.call(arguments,1);return this.each(function(){var s=t(this),r=s.data("owl.carousel");r||(r=new n(this,"object"==typeof e&&e),s.data("owl.carousel",r),t.each(["next","prev","to","destroy","refresh","replace","add","remove"],function(e,i){r.register({type:n.Type.Event,name:i}),r.$element.on(i+".owl.carousel.core",t.proxy(function(t){t.namespace&&t.relatedTarget!==this&&(this.suppress([i]),r[i].apply(this,[].slice.call(arguments,1)),this.release([i]))},r))})),"string"==typeof e&&"_"!==e.charAt(0)&&r[e].apply(r,i)})},t.fn.owlCarousel.Constructor=n}(window.Zepto||window.jQuery,window,document);

/*navigations*/
!function(t,e,s,i){"use strict";var o=function(e){this._core=e,this._initialized=!1,this._pages=[],this._controls={},this._templates=[],this.$element=this._core.$element,this._overrides={next:this._core.next,prev:this._core.prev,to:this._core.to},this._handlers={"prepared.owl.carousel":t.proxy(function(e){e.namespace&&this._core.settings.dotsData&&this._templates.push('<div class="'+this._core.settings.dotClass+'">'+t(e.content).find("[data-dot]").addBack("[data-dot]").attr("data-dot")+"</div>")},this),"added.owl.carousel":t.proxy(function(t){t.namespace&&this._core.settings.dotsData&&this._templates.splice(t.position,0,this._templates.pop())},this),"remove.owl.carousel":t.proxy(function(t){t.namespace&&this._core.settings.dotsData&&this._templates.splice(t.position,1)},this),"changed.owl.carousel":t.proxy(function(t){t.namespace&&"position"==t.property.name&&this.draw()},this),"initialized.owl.carousel":t.proxy(function(t){t.namespace&&!this._initialized&&(this._core.trigger("initialize",null,"navigation"),this.initialize(),this.update(),this.draw(),this._initialized=!0,this._core.trigger("initialized",null,"navigation"))},this),"refreshed.owl.carousel":t.proxy(function(t){t.namespace&&this._initialized&&(this._core.trigger("refresh",null,"navigation"),this.update(),this.draw(),this._core.trigger("refreshed",null,"navigation"))},this)},this._core.options=t.extend({},o.Defaults,this._core.options),this.$element.on(this._handlers)};o.Defaults={nav:!1,navText:['<span aria-label="Previous">&#x2039;</span>','<span aria-label="Next">&#x203a;</span>'],navSpeed:!1,navElement:'button type="button" role="presentation"',navContainer:!1,navContainerClass:"owl-nav",navClass:["owl-prev","owl-next"],slideBy:1,dotClass:"owl-dot",dotsClass:"owl-dots",dots:!0,dotsEach:!1,dotsData:!1,dotsSpeed:!1,dotsContainer:!1},o.prototype.initialize=function(){var e,s=this._core.settings;this._controls.$relative=(s.navContainer?t(s.navContainer):t("<div>").addClass(s.navContainerClass).appendTo(this.$element)).addClass("disabled"),this._controls.$previous=t("<"+s.navElement+">").addClass(s.navClass[0]).html(s.navText[0]).prependTo(this._controls.$relative).on("click",t.proxy(function(t){this.prev(s.navSpeed)},this)),this._controls.$next=t("<"+s.navElement+">").addClass(s.navClass[1]).html(s.navText[1]).appendTo(this._controls.$relative).on("click",t.proxy(function(t){this.next(s.navSpeed)},this)),s.dotsData||(this._templates=[t('<button role="button">').addClass(s.dotClass).append(t("<span>")).prop("outerHTML")]),this._controls.$absolute=(s.dotsContainer?t(s.dotsContainer):t("<div>").addClass(s.dotsClass).appendTo(this.$element)).addClass("disabled"),this._controls.$absolute.on("click","button",t.proxy(function(e){var i=t(e.target).parent().is(this._controls.$absolute)?t(e.target).index():t(e.target).parent().index();e.preventDefault(),this.to(i,s.dotsSpeed)},this));for(e in this._overrides)this._core[e]=t.proxy(this[e],this)},o.prototype.destroy=function(){var t,e,s,i,o;o=this._core.settings;for(t in this._handlers)this.$element.off(t,this._handlers[t]);for(e in this._controls)"$relative"===e&&o.navContainer?this._controls[e].html(""):this._controls[e].remove();for(i in this.overides)this._core[i]=this._overrides[i];for(s in Object.getOwnPropertyNames(this))"function"!=typeof this[s]&&(this[s]=null)},o.prototype.update=function(){var t,e,s=this._core.clones().length/2,i=s+this._core.items().length,o=this._core.maximum(!0),n=this._core.settings,a=n.center||n.autoWidth||n.dotsData?1:n.dotsEach||n.items;if("page"!==n.slideBy&&(n.slideBy=Math.min(n.slideBy,n.items)),n.dots||"page"==n.slideBy)for(this._pages=[],t=s,e=0,0;t<i;t++){if(e>=a||0===e){if(this._pages.push({start:Math.min(o,t-s),end:t-s+a-1}),Math.min(o,t-s)===o)break;e=0,0}e+=this._core.mergers(this._core.relative(t))}},o.prototype.draw=function(){var e,s=this._core.settings,i=this._core.items().length<=s.items,o=this._core.relative(this._core.current()),n=s.loop||s.rewind;this._controls.$relative.toggleClass("disabled",!s.nav||i),s.nav&&(this._controls.$previous.toggleClass("disabled",!n&&o<=this._core.minimum(!0)),this._controls.$next.toggleClass("disabled",!n&&o>=this._core.maximum(!0))),this._controls.$absolute.toggleClass("disabled",!s.dots||i),s.dots&&(e=this._pages.length-this._controls.$absolute.children().length,s.dotsData&&0!==e?this._controls.$absolute.html(this._templates.join("")):e>0?this._controls.$absolute.append(new Array(e+1).join(this._templates[0])):e<0&&this._controls.$absolute.children().slice(e).remove(),this._controls.$absolute.find(".active").removeClass("active"),this._controls.$absolute.children().eq(t.inArray(this.current(),this._pages)).addClass("active"))},o.prototype.onTrigger=function(e){var s=this._core.settings;e.page={index:t.inArray(this.current(),this._pages),count:this._pages.length,size:s&&(s.center||s.autoWidth||s.dotsData?1:s.dotsEach||s.items)}},o.prototype.current=function(){var e=this._core.relative(this._core.current());return t.grep(this._pages,t.proxy(function(t,s){return t.start<=e&&t.end>=e},this)).pop()},o.prototype.getPosition=function(e){var s,i,o=this._core.settings;return"page"==o.slideBy?(s=t.inArray(this.current(),this._pages),i=this._pages.length,e?++s:--s,s=this._pages[(s%i+i)%i].start):(s=this._core.relative(this._core.current()),i=this._core.items().length,e?s+=o.slideBy:s-=o.slideBy),s},o.prototype.next=function(e){t.proxy(this._overrides.to,this._core)(this.getPosition(!0),e)},o.prototype.prev=function(e){t.proxy(this._overrides.to,this._core)(this.getPosition(!1),e)},o.prototype.to=function(e,s,i){var o;!i&&this._pages.length?(o=this._pages.length,t.proxy(this._overrides.to,this._core)(this._pages[(e%o+o)%o].start,s)):t.proxy(this._overrides.to,this._core)(e,s)},t.fn.owlCarousel.Constructor.Plugins.Navigation=o}(window.Zepto||window.jQuery,window,document);

/*hash*/
!function(t,e,s,i){"use strict";var n=function(s){this._core=s,this._hashes={},this.$element=this._core.$element,this._handlers={"initialized.owl.carousel":t.proxy(function(s){s.namespace&&"URLHash"===this._core.settings.startPosition&&t(e).trigger("hashchange.owl.navigation")},this),"prepared.owl.carousel":t.proxy(function(e){if(e.namespace){var s=t(e.content).find("[data-hash]").addBack("[data-hash]").attr("data-hash");if(!s)return;this._hashes[s]=e.content}},this),"changed.owl.carousel":t.proxy(function(s){if(s.namespace&&"position"===s.property.name){var i=this._core.items(this._core.relative(this._core.current())),n=t.map(this._hashes,function(t,e){return t===i?e:null}).join();if(!n||e.location.hash.slice(1)===n)return;e.location.hash=n}},this)},this._core.options=t.extend({},n.Defaults,this._core.options),this.$element.on(this._handlers),t(e).on("hashchange.owl.navigation",t.proxy(function(t){var s=e.location.hash.substring(1),i=this._core.$stage.children(),n=this._hashes[s]&&i.index(this._hashes[s]);void 0!==n&&n!==this._core.current()&&this._core.to(this._core.relative(n),!1,!0)},this))};n.Defaults={URLhashListener:!1},n.prototype.destroy=function(){var s,i;t(e).off("hashchange.owl.navigation");for(s in this._handlers)this._core.$element.off(s,this._handlers[s]);for(i in Object.getOwnPropertyNames(this))"function"!=typeof this[i]&&(this[i]=null)},t.fn.owlCarousel.Constructor.Plugins.Hash=n}(window.Zepto||window.jQuery,window,document);

/*auto refresh*/
!function(t,e,i,s){var o=function(e){this._core=e,this._interval=null,this._visible=null,this._handlers={"initialized.owl.carousel":t.proxy(function(t){t.namespace&&this._core.settings.autoRefresh&&this.watch()},this)},this._core.options=t.extend({},o.Defaults,this._core.options),this._core.$element.on(this._handlers)};o.Defaults={autoRefresh:!0,autoRefreshInterval:500},o.prototype.watch=function(){this._interval||(this._visible=this._core.isVisible(),this._interval=e.setInterval(t.proxy(this.refresh,this),this._core.settings.autoRefreshInterval))},o.prototype.refresh=function(){this._core.isVisible()!==this._visible&&(this._visible=!this._visible,this._core.$element.toggleClass("owl-hidden",!this._visible),this._visible&&this._core.invalidate("width")&&this._core.refresh())},o.prototype.destroy=function(){var t,i;e.clearInterval(this._interval);for(t in this._handlers)this._core.$element.off(t,this._handlers[t]);for(i in Object.getOwnPropertyNames(this))"function"!=typeof this[i]&&(this[i]=null)},t.fn.owlCarousel.Constructor.Plugins.AutoRefresh=o}(window.Zepto||window.jQuery,window,document);

/*auto play*/
!function(t,i,e,s){var o=function(i){this._core=i,this._call=null,this._time=0,this._timeout=0,this._paused=!0,this._handlers={"changed.owl.carousel":t.proxy(function(t){t.namespace&&"settings"===t.property.name?this._core.settings.autoplay?this.play():this.stop():t.namespace&&"position"===t.property.name&&this._paused&&(this._time=0)},this),"initialized.owl.carousel":t.proxy(function(t){t.namespace&&this._core.settings.autoplay&&this.play()},this),"play.owl.autoplay":t.proxy(function(t,i,e){t.namespace&&this.play(i,e)},this),"stop.owl.autoplay":t.proxy(function(t){t.namespace&&this.stop()},this),"mouseover.owl.autoplay":t.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.pause()},this),"mouseleave.owl.autoplay":t.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.play()},this),"touchstart.owl.core":t.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.pause()},this),"touchend.owl.core":t.proxy(function(){this._core.settings.autoplayHoverPause&&this.play()},this)},this._core.$element.on(this._handlers),this._core.options=t.extend({},o.Defaults,this._core.options)};o.Defaults={autoplay:!1,autoplayTimeout:5e3,autoplayHoverPause:!1,autoplaySpeed:!1},o.prototype._next=function(s){this._call=i.setTimeout(t.proxy(this._next,this,s),this._timeout*(Math.round(this.read()/this._timeout)+1)-this.read()),this._core.is("interacting")||e.hidden||this._core.next(s||this._core.settings.autoplaySpeed)},o.prototype.read=function(){return(new Date).getTime()-this._time},o.prototype.play=function(e,s){var o;this._core.is("rotating")||this._core.enter("rotating"),e=e||this._core.settings.autoplayTimeout,o=Math.min(this._time%(this._timeout||e),e),this._paused?(this._time=this.read(),this._paused=!1):i.clearTimeout(this._call),this._time+=this.read()%e-o,this._timeout=e,this._call=i.setTimeout(t.proxy(this._next,this,s),e-o)},o.prototype.stop=function(){this._core.is("rotating")&&(this._time=0,this._paused=!0,i.clearTimeout(this._call),this._core.leave("rotating"))},o.prototype.pause=function(){this._core.is("rotating")&&!this._paused&&(this._time=this.read(),this._paused=!0,i.clearTimeout(this._call))},o.prototype.destroy=function(){var t,i;this.stop();for(t in this._handlers)this._core.$element.off(t,this._handlers[t]);for(i in Object.getOwnPropertyNames(this))"function"!=typeof this[i]&&(this[i]=null)},t.fn.owlCarousel.Constructor.Plugins.autoplay=o}(window.Zepto||window.jQuery,window,document);

/*auto height*/
!function(t,e,i,s){var o=function(i){this._core=i,this._previousHeight=null,this._handlers={"initialized.owl.carousel refreshed.owl.carousel":t.proxy(function(t){t.namespace&&this._core.settings.autoHeight&&this.update()},this),"changed.owl.carousel":t.proxy(function(t){t.namespace&&this._core.settings.autoHeight&&"position"===t.property.name&&this.update()},this),"loaded.owl.lazy":t.proxy(function(t){t.namespace&&this._core.settings.autoHeight&&t.element.closest("."+this._core.settings.itemClass).index()===this._core.current()&&this.update()},this)},this._core.options=t.extend({},o.Defaults,this._core.options),this._core.$element.on(this._handlers),this._intervalId=null;var s=this;t(e).on("load",function(){s._core.settings.autoHeight&&s.update()}),t(e).resize(function(){s._core.settings.autoHeight&&(null!=s._intervalId&&clearTimeout(s._intervalId),s._intervalId=setTimeout(function(){s.update()},250))})};o.Defaults={autoHeight:!1,autoHeightClass:"owl-height"},o.prototype.update=function(){var e=this._core._current,i=e+this._core.settings.items,s=this._core.settings.lazyLoad,o=this._core.$stage.children().toArray().slice(e,i),n=[],r=0;t.each(o,function(e,i){n.push(t(i).height())}),(r=Math.max.apply(null,n))<=1&&s&&this._previousHeight&&(r=this._previousHeight),this._previousHeight=r,this._core.$stage.parent().height(r).addClass(this._core.settings.autoHeightClass)},o.prototype.destroy=function(){var t,e;for(t in this._handlers)this._core.$element.off(t,this._handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.owlCarousel.Constructor.Plugins.AutoHeight=o}(window.Zepto||window.jQuery,window,document);

/*animate*/
!function(t,e,s,i){var o=function(e){this.core=e,this.core.options=t.extend({},o.Defaults,this.core.options),this.swapping=!0,this.previous=i,this.next=i,this.handlers={"change.owl.carousel":t.proxy(function(t){t.namespace&&"position"==t.property.name&&(this.previous=this.core.current(),this.next=t.property.value)},this),"drag.owl.carousel dragged.owl.carousel translated.owl.carousel":t.proxy(function(t){t.namespace&&(this.swapping="translated"==t.type)},this),"translate.owl.carousel":t.proxy(function(t){t.namespace&&this.swapping&&(this.core.options.animateOut||this.core.options.animateIn)&&this.swap()},this)},this.core.$element.on(this.handlers)};o.Defaults={animateOut:!1,animateIn:!1},o.prototype.swap=function(){if(1===this.core.settings.items&&t.support.animation&&t.support.transition){this.core.speed(0);var e,s=t.proxy(this.clear,this),i=this.core.$stage.children().eq(this.previous),o=this.core.$stage.children().eq(this.next),n=this.core.settings.animateIn,a=this.core.settings.animateOut;this.core.current()!==this.previous&&(a&&(e=this.core.coordinates(this.previous)-this.core.coordinates(this.next),i.one(t.support.animation.end,s).css({left:e+"px"}).addClass("animated owl-animated-out").addClass(a)),n&&o.one(t.support.animation.end,s).addClass("animated owl-animated-in").addClass(n))}},o.prototype.clear=function(e){t(e.target).css({left:""}).removeClass("animated owl-animated-out owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut),this.core.onTransitionEnd()},o.prototype.destroy=function(){var t,e;for(t in this.handlers)this.core.$element.off(t,this.handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.owlCarousel.Constructor.Plugins.Animate=o}(window.Zepto||window.jQuery,window,document);
