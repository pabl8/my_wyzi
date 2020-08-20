/*
function MarkerClusterer(t,e,r){this.extend(MarkerClusterer,google.maps.OverlayView),this.map_=t,this.markers_=[],this.clusters_=[],this.sizes=[53,56,66,78,90],this.styles_=[],this.ready_=!1;var s=r||{};this.gridSize_=s.gridSize||60,this.minClusterSize_=s.minimumClusterSize||2,this.maxZoom_=s.maxZoom||null,this.styles_=s.styles||[],this.imagePath_=s.imagePath||this.MARKER_CLUSTER_IMAGE_PATH_,this.imageExtension_=s.imageExtension||this.MARKER_CLUSTER_IMAGE_EXTENSION_,this.zoomOnClick_=!0,void 0!=s.zoomOnClick&&(this.zoomOnClick_=s.zoomOnClick),this.averageCenter_=!1,void 0!=s.averageCenter&&(this.averageCenter_=s.averageCenter),this.setupStyles_(),this.setMap(t),this.prevZoom_=this.map_.getZoom();var o=this;google.maps.event.addListener(this.map_,"zoom_changed",function(){var t=o.map_.getZoom(),e=o.map_.minZoom||0,r=Math.min(o.map_.maxZoom||100,o.map_.mapTypes[o.map_.getMapTypeId()].maxZoom);t=Math.min(Math.max(t,e),r),o.prevZoom_!=t&&(o.prevZoom_=t,o.resetViewport())}),google.maps.event.addListener(this.map_,"idle",function(){o.redraw()}),e&&(e.length||Object.keys(e).length)&&this.addMarkers(e,!1)}function Cluster(t){this.markerClusterer_=t,this.map_=t.getMap(),this.gridSize_=t.getGridSize(),this.minClusterSize_=t.getMinClusterSize(),this.averageCenter_=t.isAverageCenter(),this.center_=null,this.markers_=[],this.bounds_=null,this.clusterIcon_=new ClusterIcon(this,t.getStyles(),t.getGridSize())}function ClusterIcon(t,e,r){t.getMarkerClusterer().extend(ClusterIcon,google.maps.OverlayView),this.styles_=e,this.padding_=r||0,this.cluster_=t,this.center_=null,this.map_=t.getMap(),this.div_=null,this.sums_=null,this.visible_=!1,this.setMap(this.map_)}MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_PATH_="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/images/m",MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_EXTENSION_="png",MarkerClusterer.prototype.extend=function(t,e){return function(t){for(var e in t.prototype)this.prototype[e]=t.prototype[e];return this}.apply(t,[e])},MarkerClusterer.prototype.onAdd=function(){this.setReady_(!0)},MarkerClusterer.prototype.draw=function(){},MarkerClusterer.prototype.setupStyles_=function(){if(!this.styles_.length)for(var t,e=0;t=this.sizes[e];e++)this.styles_.push({url:this.imagePath_+(e+1)+"."+this.imageExtension_,height:t,width:t})},MarkerClusterer.prototype.fitMapToMarkers=function(){for(var t,e=this.getMarkers(),r=new google.maps.LatLngBounds,s=0;t=e[s];s++)r.extend(t.getPosition());this.map_.fitBounds(r)},MarkerClusterer.prototype.setStyles=function(t){this.styles_=t},MarkerClusterer.prototype.getStyles=function(){return this.styles_},MarkerClusterer.prototype.isZoomOnClick=function(){return this.zoomOnClick_},MarkerClusterer.prototype.isAverageCenter=function(){return this.averageCenter_},MarkerClusterer.prototype.getMarkers=function(){return this.markers_},MarkerClusterer.prototype.getTotalMarkers=function(){return this.markers_.length},MarkerClusterer.prototype.setMaxZoom=function(t){this.maxZoom_=t},MarkerClusterer.prototype.getMaxZoom=function(){return this.maxZoom_},MarkerClusterer.prototype.calculator_=function(t,e){for(var r=0,s=t.length,o=s;0!==o;)o=parseInt(o/10,10),r++;return r=Math.min(r,e),{text:s,index:r}},MarkerClusterer.prototype.setCalculator=function(t){this.calculator_=t},MarkerClusterer.prototype.getCalculator=function(){return this.calculator_},MarkerClusterer.prototype.addMarkers=function(t,e){if(t.length)for(var r,s=0;r=t[s];s++)this.pushMarkerTo_(r);else if(Object.keys(t).length)for(var r in t)this.pushMarkerTo_(t[r]);e||this.redraw()},MarkerClusterer.prototype.pushMarkerTo_=function(t){if(t.isAdded=!1,t.draggable){var e=this;google.maps.event.addListener(t,"dragend",function(){t.isAdded=!1,e.repaint()})}this.markers_.push(t)},MarkerClusterer.prototype.addMarker=function(t,e){this.pushMarkerTo_(t),e||this.redraw()},MarkerClusterer.prototype.removeMarker_=function(t){var e=-1;if(this.markers_.indexOf)e=this.markers_.indexOf(t);else for(var r,s=0;r=this.markers_[s];s++)if(r==t){e=s;break}return-1==e?!1:(t.setMap(null),this.markers_.splice(e,1),!0)},MarkerClusterer.prototype.removeMarker=function(t,e){var r=this.removeMarker_(t);return!e&&r?(this.resetViewport(),this.redraw(),!0):!1},MarkerClusterer.prototype.removeMarkers=function(t,e){for(var r,s=!1,o=0;r=t[o];o++){var i=this.removeMarker_(r);s=s||i}return!e&&s?(this.resetViewport(),this.redraw(),!0):void 0},MarkerClusterer.prototype.setReady_=function(t){this.ready_||(this.ready_=t,this.createClusters_())},MarkerClusterer.prototype.getTotalClusters=function(){return this.clusters_.length},MarkerClusterer.prototype.getMap=function(){return this.map_},MarkerClusterer.prototype.setMap=function(t){this.map_=t},MarkerClusterer.prototype.getGridSize=function(){return this.gridSize_},MarkerClusterer.prototype.setGridSize=function(t){this.gridSize_=t},MarkerClusterer.prototype.getMinClusterSize=function(){return this.minClusterSize_},MarkerClusterer.prototype.setMinClusterSize=function(t){this.minClusterSize_=t},MarkerClusterer.prototype.getExtendedBounds=function(t){var e=this.getProjection(),r=new google.maps.LatLng(t.getNorthEast().lat(),t.getNorthEast().lng()),s=new google.maps.LatLng(t.getSouthWest().lat(),t.getSouthWest().lng()),o=e.fromLatLngToDivPixel(r);o.x+=this.gridSize_,o.y-=this.gridSize_;var i=e.fromLatLngToDivPixel(s);i.x-=this.gridSize_,i.y+=this.gridSize_;var a=e.fromDivPixelToLatLng(o),n=e.fromDivPixelToLatLng(i);return t.extend(a),t.extend(n),t},MarkerClusterer.prototype.isMarkerInBounds_=function(t,e){return e.contains(t.getPosition())},MarkerClusterer.prototype.clearMarkers=function(){this.resetViewport(!0),this.markers_=[]},MarkerClusterer.prototype.resetViewport=function(t){for(var e,r=0;e=this.clusters_[r];r++)e.remove();for(var s,r=0;s=this.markers_[r];r++)s.isAdded=!1,t&&s.setMap(null);this.clusters_=[]},MarkerClusterer.prototype.repaint=function(){var t=this.clusters_.slice();this.clusters_.length=0,this.resetViewport(),this.redraw(),window.setTimeout(function(){for(var e,r=0;e=t[r];r++)e.remove()},0)},MarkerClusterer.prototype.redraw=function(){this.createClusters_()},MarkerClusterer.prototype.distanceBetweenPoints_=function(t,e){if(!t||!e)return 0;var r=6371,s=(e.lat()-t.lat())*Math.PI/180,o=(e.lng()-t.lng())*Math.PI/180,i=Math.sin(s/2)*Math.sin(s/2)+Math.cos(t.lat()*Math.PI/180)*Math.cos(e.lat()*Math.PI/180)*Math.sin(o/2)*Math.sin(o/2),a=2*Math.atan2(Math.sqrt(i),Math.sqrt(1-i)),n=r*a;return n},MarkerClusterer.prototype.addToClosestCluster_=function(t){for(var e,r=4e4,s=null,o=(t.getPosition(),0);e=this.clusters_[o];o++){var i=e.getCenter();if(i){var a=this.distanceBetweenPoints_(i,t.getPosition());r>a&&(r=a,s=e)}}if(s&&s.isMarkerInClusterBounds(t))s.addMarker(t);else{var e=new Cluster(this);e.addMarker(t),this.clusters_.push(e)}},MarkerClusterer.prototype.createClusters_=function(){if(this.ready_)for(var t,e=new google.maps.LatLngBounds(this.map_.getBounds().getSouthWest(),this.map_.getBounds().getNorthEast()),r=this.getExtendedBounds(e),s=0;t=this.markers_[s];s++)!t.isAdded&&this.isMarkerInBounds_(t,r)&&this.addToClosestCluster_(t)},Cluster.prototype.isMarkerAlreadyAdded=function(t){if(this.markers_.indexOf)return-1!=this.markers_.indexOf(t);for(var e,r=0;e=this.markers_[r];r++)if(e==t)return!0;return!1},Cluster.prototype.addMarker=function(t){if(this.isMarkerAlreadyAdded(t))return!1;if(this.center_){if(this.averageCenter_){var e=this.markers_.length+1,r=(this.center_.lat()*(e-1)+t.getPosition().lat())/e,s=(this.center_.lng()*(e-1)+t.getPosition().lng())/e;this.center_=new google.maps.LatLng(r,s),this.calculateBounds_()}}else this.center_=t.getPosition(),this.calculateBounds_();t.isAdded=!0,this.markers_.push(t);var o=this.markers_.length;if(o<this.minClusterSize_&&t.getMap()!=this.map_&&t.setMap(this.map_),o==this.minClusterSize_)for(var i=0;o>i;i++)this.markers_[i].setMap(null);return o>=this.minClusterSize_&&t.setMap(null),this.updateIcon(),!0},Cluster.prototype.getMarkerClusterer=function(){return this.markerClusterer_},Cluster.prototype.getBounds=function(){for(var t,e=new google.maps.LatLngBounds(this.center_,this.center_),r=this.getMarkers(),s=0;t=r[s];s++)e.extend(t.getPosition());return e},Cluster.prototype.remove=function(){this.clusterIcon_.remove(),this.markers_.length=0,delete this.markers_},Cluster.prototype.getSize=function(){return this.markers_.length},Cluster.prototype.getMarkers=function(){return this.markers_},Cluster.prototype.getCenter=function(){return this.center_},Cluster.prototype.calculateBounds_=function(){var t=new google.maps.LatLngBounds(this.center_,this.center_);this.bounds_=this.markerClusterer_.getExtendedBounds(t)},Cluster.prototype.isMarkerInClusterBounds=function(t){return this.bounds_.contains(t.getPosition())},Cluster.prototype.getMap=function(){return this.map_},Cluster.prototype.updateIcon=function(){var t=this.map_.getZoom(),e=this.markerClusterer_.getMaxZoom();if(e&&t>e)for(var r,s=0;r=this.markers_[s];s++)r.setMap(this.map_);else{if(this.markers_.length<this.minClusterSize_)return void this.clusterIcon_.hide();var o=this.markerClusterer_.getStyles().length,i=this.markerClusterer_.getCalculator()(this.markers_,o);this.clusterIcon_.setCenter(this.center_),this.clusterIcon_.setSums(i),this.clusterIcon_.show()}},ClusterIcon.prototype.triggerClusterClick=function(){var t=this.cluster_.getMarkerClusterer();google.maps.event.trigger(t,"clusterclick",this.cluster_),t.isZoomOnClick()&&this.map_.fitBounds(this.cluster_.getBounds())},ClusterIcon.prototype.onAdd=function(){if(this.div_=document.createElement("DIV"),this.visible_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(t),this.div_.innerHTML=this.sums_.text}var e=this.getPanes();e.overlayMouseTarget.appendChild(this.div_);var r=this;google.maps.event.addDomListener(this.div_,"click",function(){r.triggerClusterClick()})},ClusterIcon.prototype.getPosFromLatLng_=function(t){var e=this.getProjection().fromLatLngToDivPixel(t);return e.x-=parseInt(this.width_/2,10),e.y-=parseInt(this.height_/2,10),e},ClusterIcon.prototype.draw=function(){if(this.visible_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.top=t.y+"px",this.div_.style.left=t.x+"px"}},ClusterIcon.prototype.hide=function(){this.div_&&(this.div_.style.display="none"),this.visible_=!1},ClusterIcon.prototype.show=function(){if(this.div_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(t),this.div_.style.display=""}this.visible_=!0},ClusterIcon.prototype.remove=function(){this.setMap(null)},ClusterIcon.prototype.onRemove=function(){this.div_&&this.div_.parentNode&&(this.hide(),this.div_.parentNode.removeChild(this.div_),this.div_=null)},ClusterIcon.prototype.setSums=function(t){this.sums_=t,this.text_=t.text,this.index_=t.index,this.div_&&(this.div_.innerHTML=t.text),this.useStyle()},ClusterIcon.prototype.useStyle=function(){var t=Math.max(0,this.sums_.index-1);t=Math.min(this.styles_.length-1,t);var e=this.styles_[t];this.url_=e.url,this.height_=e.height,this.width_=e.width,this.textColor_=e.textColor,this.anchor_=e.anchor,this.textSize_=e.textSize,this.backgroundPosition_=e.backgroundPosition},ClusterIcon.prototype.setCenter=function(t){this.center_=t},ClusterIcon.prototype.createCss=function(t){var e=[];e.push("background-image:url("+this.url_+");");var r=this.backgroundPosition_?this.backgroundPosition_:"0 0";e.push("background-position:"+r+";"),e.push("background-size: contain;"),"object"==typeof this.anchor_?("number"==typeof this.anchor_[0]&&this.anchor_[0]>0&&this.anchor_[0]<this.height_?e.push("height:"+(this.height_-this.anchor_[0])+"px; padding-top:"+this.anchor_[0]+"px;"):e.push("height:"+this.height_+"px; line-height:"+this.height_+"px;"),"number"==typeof this.anchor_[1]&&this.anchor_[1]>0&&this.anchor_[1]<this.width_?e.push("width:"+(this.width_-this.anchor_[1])+"px; padding-left:"+this.anchor_[1]+"px;"):e.push("width:"+this.width_+"px; text-align:center;")):e.push("height:"+this.height_+"px; line-height:"+this.height_+"px; width:"+this.width_+"px; text-align:center;");var s=this.textColor_?this.textColor_:"black",o=this.textSize_?this.textSize_:11;return e.push("cursor:pointer; top:"+t.y+"px; left:"+t.x+"px; color:"+s+"; position:absolute; font-size:"+o+"px; font-family:Arial,sans-serif; font-weight:bold"),e.join("")},window.MarkerClusterer=MarkerClusterer,MarkerClusterer.prototype.addMarker=MarkerClusterer.prototype.addMarker,MarkerClusterer.prototype.addMarkers=MarkerClusterer.prototype.addMarkers,MarkerClusterer.prototype.clearMarkers=MarkerClusterer.prototype.clearMarkers,MarkerClusterer.prototype.fitMapToMarkers=MarkerClusterer.prototype.fitMapToMarkers,MarkerClusterer.prototype.getCalculator=MarkerClusterer.prototype.getCalculator,MarkerClusterer.prototype.getGridSize=MarkerClusterer.prototype.getGridSize,MarkerClusterer.prototype.getExtendedBounds=MarkerClusterer.prototype.getExtendedBounds,MarkerClusterer.prototype.getMap=MarkerClusterer.prototype.getMap,MarkerClusterer.prototype.getMarkers=MarkerClusterer.prototype.getMarkers,MarkerClusterer.prototype.getMaxZoom=MarkerClusterer.prototype.getMaxZoom,MarkerClusterer.prototype.getStyles=MarkerClusterer.prototype.getStyles,MarkerClusterer.prototype.getTotalClusters=MarkerClusterer.prototype.getTotalClusters,MarkerClusterer.prototype.getTotalMarkers=MarkerClusterer.prototype.getTotalMarkers,MarkerClusterer.prototype.redraw=MarkerClusterer.prototype.redraw,MarkerClusterer.prototype.removeMarker=MarkerClusterer.prototype.removeMarker,MarkerClusterer.prototype.removeMarkers=MarkerClusterer.prototype.removeMarkers,MarkerClusterer.prototype.resetViewport=MarkerClusterer.prototype.resetViewport,MarkerClusterer.prototype.repaint=MarkerClusterer.prototype.repaint,MarkerClusterer.prototype.setCalculator=MarkerClusterer.prototype.setCalculator,MarkerClusterer.prototype.setGridSize=MarkerClusterer.prototype.setGridSize,MarkerClusterer.prototype.setMaxZoom=MarkerClusterer.prototype.setMaxZoom,MarkerClusterer.prototype.onAdd=MarkerClusterer.prototype.onAdd,MarkerClusterer.prototype.draw=MarkerClusterer.prototype.draw,Cluster.prototype.getCenter=Cluster.prototype.getCenter,Cluster.prototype.getSize=Cluster.prototype.getSize,Cluster.prototype.getMarkers=Cluster.prototype.getMarkers,ClusterIcon.prototype.onAdd=ClusterIcon.prototype.onAdd,ClusterIcon.prototype.draw=ClusterIcon.prototype.draw,ClusterIcon.prototype.onRemove=ClusterIcon.prototype.onRemove,Object.keys=Object.keys||function(t){var e=[];for(var r in t)t.hasOwnProperty(r)&&e.push(r);return e};


(function(){var m,t,w,y,u,z={}.hasOwnProperty,A=[].slice;this.OverlappingMarkerSpiderfier=function(){function r(a,d){var b,f,e;this.map=a;null==d&&(d={});null==this.constructor.N&&(this.constructor.N=!0,h=google.maps,l=h.event,p=h.MapTypeId,c.keepSpiderfied=!1,c.ignoreMapClick=!1,c.markersWontHide=!1,c.markersWontMove=!1,c.basicFormatEvents=!1,c.nearbyDistance=20,c.circleSpiralSwitchover=9,c.circleFootSeparation=23,c.circleStartAngle=x/12,c.spiralFootSeparation=26,c.spiralLengthStart=11,c.spiralLengthFactor=
4,c.spiderfiedZIndex=h.Marker.MAX_ZINDEX+2E4,c.highlightedLegZIndex=h.Marker.MAX_ZINDEX+1E4,c.usualLegZIndex=h.Marker.MAX_ZINDEX+1,c.legWeight=1.5,c.legColors={usual:{},highlighted:{}},e=c.legColors.usual,f=c.legColors.highlighted,e[p.HYBRID]=e[p.SATELLITE]="#fff",f[p.HYBRID]=f[p.SATELLITE]="#f00",e[p.TERRAIN]=e[p.ROADMAP]="#444",f[p.TERRAIN]=f[p.ROADMAP]="#f00",this.constructor.j=function(a){return this.setMap(a)},this.constructor.j.prototype=new h.OverlayView,this.constructor.j.prototype.draw=function(){});
for(b in d)z.call(d,b)&&(f=d[b],this[b]=f);this.g=new this.constructor.j(this.map);this.C();this.c={};this.B=this.l=null;this.addListener("click",function(a,b){return l.trigger(a,"spider_click",b)});this.addListener("format",function(a,b){return l.trigger(a,"spider_format",b)});this.ignoreMapClick||l.addListener(this.map,"click",function(a){return function(){return a.unspiderfy()}}(this));l.addListener(this.map,"maptypeid_changed",function(a){return function(){return a.unspiderfy()}}(this));l.addListener(this.map,
"zoom_changed",function(a){return function(){a.unspiderfy();if(!a.basicFormatEvents)return a.h()}}(this))}var l,h,m,v,p,c,t,x,u;c=r.prototype;t=[r,c];m=0;for(v=t.length;m<v;m++)u=t[m],u.VERSION="1.0.3";x=2*Math.PI;h=l=p=null;r.markerStatus={SPIDERFIED:"SPIDERFIED",SPIDERFIABLE:"SPIDERFIABLE",UNSPIDERFIABLE:"UNSPIDERFIABLE",UNSPIDERFIED:"UNSPIDERFIED"};c.C=function(){this.a=[];this.s=[]};c.addMarker=function(a,d){a.setMap(this.map);return this.trackMarker(a,d)};c.trackMarker=function(a,d){var b;if(null!=
a._oms)return this;a._oms=!0;b=[l.addListener(a,"click",function(b){return function(d){return b.V(a,d)}}(this))];this.markersWontHide||b.push(l.addListener(a,"visible_changed",function(b){return function(){return b.D(a,!1)}}(this)));this.markersWontMove||b.push(l.addListener(a,"position_changed",function(b){return function(){return b.D(a,!0)}}(this)));null!=d&&b.push(l.addListener(a,"spider_click",d));this.s.push(b);this.a.push(a);this.basicFormatEvents?this.trigger("format",a,this.constructor.markerStatus.UNSPIDERFIED):
(this.trigger("format",a,this.constructor.markerStatus.UNSPIDERFIABLE),this.h());return this};c.D=function(a,d){if(!this.J&&!this.K)return null==a._omsData||!d&&a.getVisible()||this.unspiderfy(d?a:null),this.h()};c.getMarkers=function(){return this.a.slice(0)};c.removeMarker=function(a){this.forgetMarker(a);return a.setMap(null)};c.forgetMarker=function(a){var d,b,f,e,g;null!=a._omsData&&this.unspiderfy();d=this.A(this.a,a);if(0>d)return this;g=this.s.splice(d,1)[0];b=0;for(f=g.length;b<f;b++)e=g[b],
l.removeListener(e);delete a._oms;this.a.splice(d,1);this.h();return this};c.removeAllMarkers=c.clearMarkers=function(){var a,d,b,f;f=this.getMarkers();this.forgetAllMarkers();a=0;for(d=f.length;a<d;a++)b=f[a],b.setMap(null);return this};c.forgetAllMarkers=function(){var a,d,b,f,e,g,c,q;this.unspiderfy();q=this.a;a=d=0;for(b=q.length;d<b;a=++d){g=q[a];e=this.s[a];c=0;for(a=e.length;c<a;c++)f=e[c],l.removeListener(f);delete g._oms}this.C();return this};c.addListener=function(a,d){var b;(null!=(b=this.c)[a]?
b[a]:b[a]=[]).push(d);return this};c.removeListener=function(a,d){var b;b=this.A(this.c[a],d);0>b||this.c[a].splice(b,1);return this};c.clearListeners=function(a){this.c[a]=[];return this};c.trigger=function(){var a,d,b,f,e,g;d=arguments[0];a=2<=arguments.length?A.call(arguments,1):[];d=null!=(b=this.c[d])?b:[];g=[];f=0;for(e=d.length;f<e;f++)b=d[f],g.push(b.apply(null,a));return g};c.L=function(a,d){var b,f,e,g,c;g=this.circleFootSeparation*(2+a)/x;f=x/a;c=[];for(b=e=0;0<=a?e<a:e>a;b=0<=a?++e:--e)b=
this.circleStartAngle+b*f,c.push(new h.Point(d.x+g*Math.cos(b),d.y+g*Math.sin(b)));return c};c.M=function(a,d){var b,f,e,c,k;c=this.spiralLengthStart;b=0;k=[];for(f=e=0;0<=a?e<a:e>a;f=0<=a?++e:--e)b+=this.spiralFootSeparation/c+5E-4*f,f=new h.Point(d.x+c*Math.cos(b),d.y+c*Math.sin(b)),c+=x*this.spiralLengthFactor/b,k.push(f);return k};c.V=function(a,d){var b,f,e,c,k,q,n,l,h;(q=null!=a._omsData)&&this.keepSpiderfied||this.unspiderfy();if(q||this.map.getStreetView().getVisible()||"GoogleEarthAPI"===
this.map.getMapTypeId())return this.trigger("click",a,d);q=[];n=[];b=this.nearbyDistance;l=b*b;k=this.f(a.position);h=this.a;b=0;for(f=h.length;b<f;b++)e=h[b],null!=e.map&&e.getVisible()&&(c=this.f(e.position),this.i(c,k)<l?q.push({R:e,G:c}):n.push(e));return 1===q.length?this.trigger("click",a,d):this.W(q,n)};c.markersNearMarker=function(a,d){var b,f,e,c,k,q,n,l,h,m;null==d&&(d=!1);if(null==this.g.getProjection())throw"Must wait for 'idle' event on map before calling markersNearMarker";b=this.nearbyDistance;
n=b*b;k=this.f(a.position);q=[];l=this.a;b=0;for(f=l.length;b<f&&!(e=l[b],e!==a&&null!=e.map&&e.getVisible()&&(c=this.f(null!=(h=null!=(m=e._omsData)?m.v:void 0)?h:e.position),this.i(c,k)<n&&(q.push(e),d)));b++);return q};c.F=function(){var a,d,b,f,e,c,k,l,n,h,m;if(null==this.g.getProjection())throw"Must wait for 'idle' event on map before calling markersNearAnyOtherMarker";n=this.nearbyDistance;n*=n;var p;e=this.a;p=[];h=0;for(d=e.length;h<d;h++)f=e[h],p.push({H:this.f(null!=(a=null!=(b=f._omsData)?
b.v:void 0)?a:f.position),b:!1});h=this.a;a=b=0;for(f=h.length;b<f;a=++b)if(d=h[a],null!=d.getMap()&&d.getVisible()&&(c=p[a],!c.b))for(m=this.a,d=l=0,e=m.length;l<e;d=++l)if(k=m[d],d!==a&&null!=k.getMap()&&k.getVisible()&&(k=p[d],(!(d<a)||k.b)&&this.i(c.H,k.H)<n)){c.b=k.b=!0;break}return p};c.markersNearAnyOtherMarker=function(){var a,d,b,c,e,g,k;e=this.F();g=this.a;k=[];a=d=0;for(b=g.length;d<b;a=++d)c=g[a],e[a].b&&k.push(c);return k};c.setImmediate=function(a){return window.setTimeout(a,0)};c.h=
function(){if(!this.basicFormatEvents&&null==this.l)return this.l=this.setImmediate(function(a){return function(){a.l=null;return null!=a.g.getProjection()?a.w():null!=a.B?void 0:a.B=l.addListenerOnce(a.map,"idle",function(){return a.w()})}}(this))};c.w=function(){var a,d,b,c,e,g,k;if(this.basicFormatEvents){e=[];d=0;for(b=markers.length;d<b;d++)c=markers[d],a=null!=c._omsData?"SPIDERFIED":"UNSPIDERFIED",e.push(this.trigger("format",c,this.constructor.markerStatus[a]));return e}e=this.F();g=this.a;
k=[];a=b=0;for(d=g.length;b<d;a=++b)c=g[a],a=null!=c._omsData?"SPIDERFIED":e[a].b?"SPIDERFIABLE":"UNSPIDERFIABLE",k.push(this.trigger("format",c,this.constructor.markerStatus[a]));return k};c.P=function(a){return{m:function(d){return function(){return a._omsData.o.setOptions({strokeColor:d.legColors.highlighted[d.map.mapTypeId],zIndex:d.highlightedLegZIndex})}}(this),u:function(d){return function(){return a._omsData.o.setOptions({strokeColor:d.legColors.usual[d.map.mapTypeId],zIndex:d.usualLegZIndex})}}(this)}};
c.W=function(a,d){var b,c,e,g,k,q,n,m,p,r;this.J=!0;r=a.length;b=this.T(function(){var b,d,c;c=[];b=0;for(d=a.length;b<d;b++)m=a[b],c.push(m.G);return c}());g=r>=this.circleSpiralSwitchover?this.M(r,b).reverse():this.L(r,b);b=function(){var b,d,f;f=[];b=0;for(d=g.length;b<d;b++)e=g[b],c=this.U(e),p=this.S(a,function(a){return function(b){return a.i(b.G,e)}}(this)),n=p.R,q=new h.Polyline({map:this.map,path:[n.position,c],strokeColor:this.legColors.usual[this.map.mapTypeId],strokeWeight:this.legWeight,
zIndex:this.usualLegZIndex}),n._omsData={v:n.getPosition(),X:n.getZIndex(),o:q},this.legColors.highlighted[this.map.mapTypeId]!==this.legColors.usual[this.map.mapTypeId]&&(k=this.P(n),n._omsData.O={m:l.addListener(n,"mouseover",k.m),u:l.addListener(n,"mouseout",k.u)}),this.trigger("format",n,this.constructor.markerStatus.SPIDERFIED),n.setPosition(c),n.setZIndex(Math.round(this.spiderfiedZIndex+e.y)),f.push(n);return f}.call(this);delete this.J;this.I=!0;return this.trigger("spiderfy",b,d)};c.unspiderfy=
function(a){var d,b,c,e,g,k,h;null==a&&(a=null);if(null==this.I)return this;this.K=!0;h=[];g=[];k=this.a;d=0;for(b=k.length;d<b;d++)e=k[d],null!=e._omsData?(e._omsData.o.setMap(null),e!==a&&e.setPosition(e._omsData.v),e.setZIndex(e._omsData.X),c=e._omsData.O,null!=c&&(l.removeListener(c.m),l.removeListener(c.u)),delete e._omsData,e!==a&&(c=this.basicFormatEvents?"UNSPIDERFIED":"SPIDERFIABLE",this.trigger("format",e,this.constructor.markerStatus[c])),h.push(e)):g.push(e);delete this.K;delete this.I;
this.trigger("unspiderfy",h,g);return this};c.i=function(a,d){var b,c;b=a.x-d.x;c=a.y-d.y;return b*b+c*c};c.T=function(a){var c,b,f,e,g;c=e=g=0;for(b=a.length;c<b;c++)f=a[c],e+=f.x,g+=f.y;a=a.length;return new h.Point(e/a,g/a)};c.f=function(a){return this.g.getProjection().fromLatLngToDivPixel(a)};c.U=function(a){return this.g.getProjection().fromDivPixelToLatLng(a)};c.S=function(a,c){var b,d,e,g,k,h;e=k=0;for(h=a.length;k<h;e=++k)if(g=a[e],g=c(g),"undefined"===typeof b||null===b||g<d)d=g,b=e;return a.splice(b,
1)[0]};c.A=function(a,c){var b,d,e,g;if(null!=a.indexOf)return a.indexOf(c);b=d=0;for(e=a.length;d<e;b=++d)if(g=a[b],g===c)return b;return-1};return r}();t=/(\?.*(&|&amp;)|\?)spiderfier_callback=(\w+)/;m=document.currentScript;null==m&&(m=function(){var m,l,h,w,v;h=document.getElementsByTagName("script");v=[];m=0;for(l=h.length;m<l;m++)u=h[m],null!=(w=u.getAttribute("src"))&&w.match(t)&&v.push(u);return v}()[0]);if(null!=m&&(m=null!=(w=m.getAttribute("src"))?null!=(y=w.match(t))?y[3]:void 0:void 0)&&
"function"===typeof window[m])window[m]();"function"===typeof window.spiderfier_callback&&window.spiderfier_callback()}).call(this);



(function(factory){'use strict';if(typeof define==='function'&&define.amd){define(['jquery'],factory)}else if(typeof exports==='object'){module.exports=factory(require('jquery'))}else{factory(jQuery)}}(function($){'use strict';Number.isNaN=Number.isNaN||function(value){return typeof value==='number'&&value!==value};function supportsRange(){var input=document.createElement('input');input.setAttribute('type','range');return input.type!=='text'}
var pluginName='rangeslider',pluginIdentifier=0,hasInputRangeSupport=supportsRange(),defaults={polyfill:!0,orientation:'horizontal',rangeClass:'rangeslider',disabledClass:'rangeslider--disabled',activeClass:'rangeslider--active',horizontalClass:'rangeslider--horizontal',verticalClass:'rangeslider--vertical',fillClass:'rangeslider__fill',handleClass:'rangeslider__handle',startEvent:['mousedown','touchstart','pointerdown'],moveEvent:['mousemove','touchmove','pointermove'],endEvent:['mouseup','touchend','pointerup']},constants={orientation:{horizontal:{dimension:'width',direction:'left',directionStyle:'left',coordinate:'x'},vertical:{dimension:'height',direction:'top',directionStyle:'bottom',coordinate:'y'}}};function delay(fn,wait){var args=Array.prototype.slice.call(arguments,2);return setTimeout(function(){return fn.apply(null,args)},wait)}
function debounce(fn,debounceDuration){debounceDuration=debounceDuration||100;return function(){if(!fn.debouncing){var args=Array.prototype.slice.apply(arguments);fn.lastReturnVal=fn.apply(window,args);fn.debouncing=!0}
clearTimeout(fn.debounceTimeout);fn.debounceTimeout=setTimeout(function(){fn.debouncing=!1},debounceDuration);return fn.lastReturnVal}}
function isHidden(element){return(element&&(element.offsetWidth===0||element.offsetHeight===0||element.open===!1))}
function getHiddenParentNodes(element){var parents=[],node=element.parentNode;while(isHidden(node)){parents.push(node);node=node.parentNode}
return parents}
function getDimension(element,key){var hiddenParentNodes=getHiddenParentNodes(element),hiddenParentNodesLength=hiddenParentNodes.length,inlineStyle=[],dimension=element[key];function toggleOpenProperty(element){if(typeof element.open!=='undefined'){element.open=(element.open)?!1:!0}}
if(hiddenParentNodesLength){for(var i=0;i<hiddenParentNodesLength;i++){inlineStyle[i]=hiddenParentNodes[i].style.cssText;if(hiddenParentNodes[i].style.setProperty){hiddenParentNodes[i].style.setProperty('display','block','important')}else{hiddenParentNodes[i].style.cssText+=';display: block !important'}
hiddenParentNodes[i].style.height='0';hiddenParentNodes[i].style.overflow='hidden';hiddenParentNodes[i].style.visibility='hidden';toggleOpenProperty(hiddenParentNodes[i])}
dimension=element[key];for(var j=0;j<hiddenParentNodesLength;j++){hiddenParentNodes[j].style.cssText=inlineStyle[j];toggleOpenProperty(hiddenParentNodes[j])}}
return dimension}
function tryParseFloat(str,defaultValue){var value=parseFloat(str);return Number.isNaN(value)?defaultValue:value}
function ucfirst(str){return str.charAt(0).toUpperCase()+str.substr(1)}
function Plugin(element,options){this.$window=$(window);this.$document=$(document);this.$element=$(element);this.options=$.extend({},defaults,options);this.polyfill=this.options.polyfill;this.orientation=this.$element[0].getAttribute('data-orientation')||this.options.orientation;this.onInit=this.options.onInit;this.onSlide=this.options.onSlide;this.onSlideEnd=this.options.onSlideEnd;this.DIMENSION=constants.orientation[this.orientation].dimension;this.DIRECTION=this.options.direction||constants.orientation[this.orientation].direction;this.DIRECTION_STYLE=this.options.direction||constants.orientation[this.orientation].directionStyle;this.COORDINATE=constants.orientation[this.orientation].coordinate;if(this.polyfill){if(hasInputRangeSupport){return!1}}
this.identifier='js-'+pluginName+'-'+(pluginIdentifier++);this.startEvent=this.options.startEvent.join('.'+this.identifier+' ')+'.'+this.identifier;this.moveEvent=this.options.moveEvent.join('.'+this.identifier+' ')+'.'+this.identifier;this.endEvent=this.options.endEvent.join('.'+this.identifier+' ')+'.'+this.identifier;this.toFixed=(this.step+'').replace('.','').length-1;this.$fill=$('<div class="'+this.options.fillClass+'" />').css(this.DIRECTION,0);this.$handle=$('<div class="'+this.options.handleClass+'" />');this.$range=$('<div class="'+this.options.rangeClass+' '+this.options[this.orientation+'Class']+'" id="'+this.identifier+'" />').insertAfter(this.$element).prepend(this.$fill,this.$handle);this.$element.css({'position':'absolute','width':'1px','height':'1px','overflow':'hidden','opacity':'0'});this.handleDown=$.proxy(this.handleDown,this);this.handleMove=$.proxy(this.handleMove,this);this.handleEnd=$.proxy(this.handleEnd,this);this.init();var _this=this;this.$window.on('resize.'+this.identifier,debounce(function(){delay(function(){_this.update(!1,!1)},300)},20));this.$document.on(this.startEvent,'#'+this.identifier+':not(.'+this.options.disabledClass+')',this.handleDown);this.$element.on('change.'+this.identifier,function(e,data){if(data&&data.origin===_this.identifier){return}
var value=e.target.value,pos=_this.getPositionFromValue(value);_this.setPosition(pos)})}
Plugin.prototype.init=function(){this.update(!0,!1);if(this.onInit&&typeof this.onInit==='function'){this.onInit()}};Plugin.prototype.update=function(updateAttributes,triggerSlide){updateAttributes=updateAttributes||!1;if(updateAttributes){this.min=tryParseFloat(this.$element[0].getAttribute('min'),0);this.max=tryParseFloat(this.$element[0].getAttribute('max'),100);this.value=tryParseFloat(this.$element[0].value,Math.round(this.min+(this.max-this.min)/2));this.step=tryParseFloat(this.$element[0].getAttribute('step'),1)}
this.handleDimension=getDimension(this.$handle[0],'offset'+ucfirst(this.DIMENSION));this.rangeDimension=getDimension(this.$range[0],'offset'+ucfirst(this.DIMENSION));this.maxHandlePos=this.rangeDimension-this.handleDimension;this.grabPos=this.handleDimension/2;this.position=this.getPositionFromValue(this.value);if(this.$element[0].disabled){this.$range.addClass(this.options.disabledClass)}else{this.$range.removeClass(this.options.disabledClass)}
this.setPosition(this.position,triggerSlide)};Plugin.prototype.handleDown=function(e){e.preventDefault();this.$document.on(this.moveEvent,this.handleMove);this.$document.on(this.endEvent,this.handleEnd);this.$range.addClass(this.options.activeClass);if((' '+e.target.className+' ').replace(/[\n\t]/g,' ').indexOf(this.options.handleClass)>-1){return}
var pos=this.getRelativePosition(e),rangePos=this.$range[0].getBoundingClientRect()[this.DIRECTION],handlePos=this.getPositionFromNode(this.$handle[0])-rangePos,setPos=(this.orientation==='vertical')?(this.maxHandlePos-(pos-this.grabPos)):(pos-this.grabPos);this.setPosition(setPos);if(pos>=handlePos&&pos<handlePos+this.handleDimension){this.grabPos=pos-handlePos}};Plugin.prototype.handleMove=function(e){e.preventDefault();var pos=this.getRelativePosition(e);var setPos=(this.orientation==='vertical')?(this.maxHandlePos-(pos-this.grabPos)):(pos-this.grabPos);this.setPosition(setPos)};Plugin.prototype.handleEnd=function(e){e.preventDefault();this.$document.off(this.moveEvent,this.handleMove);this.$document.off(this.endEvent,this.handleEnd);this.$range.removeClass(this.options.activeClass);this.$element.trigger('change',{origin:this.identifier});if(this.onSlideEnd&&typeof this.onSlideEnd==='function'){this.onSlideEnd(this.position,this.value)}};Plugin.prototype.cap=function(pos,min,max){if(pos<min){return min}
if(pos>max){return max}
return pos};Plugin.prototype.setPosition=function(pos,triggerSlide){var value,newPos;if(triggerSlide===undefined){triggerSlide=!0}
value=this.getValueFromPosition(this.cap(pos,0,this.maxHandlePos));newPos=this.getPositionFromValue(value);this.$fill[0].style[this.DIMENSION]=(newPos+this.grabPos)+'px';this.$handle[0].style[this.DIRECTION_STYLE]=newPos+'px';this.setValue(value);this.position=newPos;this.value=value;if(triggerSlide&&this.onSlide&&typeof this.onSlide==='function'){this.onSlide(newPos,value)}};Plugin.prototype.getPositionFromNode=function(node){var i=0;while(node!==null){i+=node.offsetLeft;node=node.offsetParent}
return i};Plugin.prototype.getRelativePosition=function(e){var ucCoordinate=ucfirst(this.COORDINATE),rangePos=this.$range[0].getBoundingClientRect()[this.DIRECTION],pageCoordinate=0;if(typeof e.originalEvent['client'+ucCoordinate]!=='undefined'){pageCoordinate=e.originalEvent['client'+ucCoordinate]}
else if(e.originalEvent.touches&&e.originalEvent.touches[0]&&typeof e.originalEvent.touches[0]['client'+ucCoordinate]!=='undefined'){pageCoordinate=e.originalEvent.touches[0]['client'+ucCoordinate]}
else if(e.currentPoint&&typeof e.currentPoint[this.COORDINATE]!=='undefined'){pageCoordinate=e.currentPoint[this.COORDINATE]}
return('right'===this.DIRECTION)?rangePos-pageCoordinate:pageCoordinate-rangePos};Plugin.prototype.getPositionFromValue=function(value){var percentage,pos;percentage=(value-this.min)/(this.max-this.min);pos=(!Number.isNaN(percentage))?percentage*this.maxHandlePos:0;return pos};Plugin.prototype.getValueFromPosition=function(pos){var percentage,value;percentage=((pos)/(this.maxHandlePos||1));value=this.step*Math.round(percentage*(this.max-this.min)/this.step)+this.min;return Number((value).toFixed(this.toFixed))};Plugin.prototype.setValue=function(value){if(value===this.value&&this.$element[0].value!==''){return}
this.$element.val(value).trigger('input',{origin:this.identifier})};Plugin.prototype.destroy=function(){this.$document.off('.'+this.identifier);this.$window.off('.'+this.identifier);this.$element.off('.'+this.identifier).removeAttr('style').removeData('plugin_'+pluginName);if(this.$range&&this.$range.length){this.$range[0].parentNode.removeChild(this.$range[0])}};$.fn[pluginName]=function(options){var args=Array.prototype.slice.call(arguments,1);return this.each(function(){var $this=$(this),data=$this.data('plugin_'+pluginName);if(!data){$this.data('plugin_'+pluginName,(data=new Plugin(this,options)))}
if(typeof options==='string'){data[options].apply(data,args)}})};return'rangeslider.js is available in jQuery context e.g $(selector).rangeslider(options);'}))*/



var wyz_map_loaded_sh = false;
var wyz_dom_loaded_sh = false;

document.addEventListener('DOMContentLoaded', function() {
	wyz_dom_loaded_sh = true;
	wyz_init_load_map_shortcode();
}, false);

function wyz_init_load_map_shortcode() {
	if(wyz_map_loaded_sh)return;
	if (typeof google === 'object' && typeof google.maps === 'object' && wyz_dom_loaded_sh ) {
		wyz_map_loaded_sh = true;
		wyz_load_map_shortcode();
	}
}

function wyz_load_map_shortcode(){
	(function( $ ) {
 
		$.fn.wyziGlobalMap = function( element, options ) {

			var defaults = {
				radius:{
					radiusUnit: 'km',
					radiusDef: 0,
					radiusMax: 500,
					radiusStep: 10,
				},
				markerType: 1,
				sidebar: false,
				GPSLocations: {},
				gpsLen: 0,
				defCoor: {
					latitude: 0,
					longitude: 0,
					zoom: 12,
				},
				markersWithIcons: {},
				range_radius:{},
				businessNames: {},
				businessLogoes: {},
				businessPermalinks: {},
				businessCategories: {},
				businessCategoriesColors: {},
				myLocationMarker: '',
				spiderfyMarker: '',
				locLocationMarker: '',
				geolocation: true,
				taxonomies: '',
				businessList: '',
				favEnabled: false,
				businessIds: {},
				defLoc: -1,
				defCat: -1,
				autoFit: true,
				defLogo: '',
				templateType: 1,
				favorites: {},
				filterType: 'dropdown',
				onLoadLocReq: false,
				translations: {
					searchText: 'Search here...',
					viewDetails: 'View Details',
					viewAll: 'View All',
					noBusinessesFound: 'No Businesses match your search',
					notValidRad: 'Not a valid radius',
					geoFail: 'The Geolocation service failed. You can still search by location radius, but not by your location.',
					geoBrowserFail: 'Error: Your browser doesn\'t support geolocation.',
				},
				tabs: {},
				mapSkin: 1,
				mapId:1
			};

			var settings = $.extend(defaults, options );

			var self = this;

			self.sourceElement = $(element);

			self.latLng = null;
			self.map = null;
			self.markerSpiderfier = null;
			self.searching = false;
			self.geoEnabled = false;
			self.myLat = 0;
			self.myLon = 0;
			self.myTrueLat = 0;
			self.myTrueLon = 0;
			self.radVal = 0;
			self.fRadVal = 0;
			self.offset = 1;
			self.append = '';
			self.slided = false;
			self.appendTop = '';
			self.locationFirstRun = true;
			self.mapFirstLoad = true;
			self.pageFirstLoad = true;
			self.sidebarWidth =0;

			self.mapCntr = 0;
			self.markers = [];
			self.infowindow;
			self.bounds;
			self.content;
			self.lastIndex = 0;
			self.searchMarker = null;
			self.spiderfyMarker = null;
			self.markerAnchorX = null;
			self.markerAnchorY = null;
			self.markerWidthX = null;
			self.markerWidthY = null;
			self.myoverlay = null;
			self.markerCluster = null;

			self.submitBtn = self.sourceElement.find('.map-search-submit');
			self.locText={};
			self.locText.locText= self.sourceElement.find('#wyz-loc-filter-txt');
			self.locText.text= self.sourceElement.find('#loc-filter-txt');
			self.locText.lat = self.sourceElement.find("#loc-filter-lat");
			self.locText.lon = self.sourceElement.find("#loc-filter-lon");
			self.locText.filter = self.sourceElement.find("#wyz-loc-filter");
			self.catFilter = self.sourceElement.find("#wyz-cat-filter");
			self.radiusFilter = self.sourceElement.find(".loc-radius");
			self.searchNames = self.sourceElement.find(".map-names");
			self.mapMask = self.sourceElement.find('.map-mask');
			self.mapLoading = self.sourceElement.find('.map-loading');
			self.sidebar = self.sourceElement.find('.slidable-map-sidebar');
			self.galleryContainer = self.sourceElement.find('.page-map-right-content .map-info-gallery');


			var page = 0;
			var active;

			var path = wyz_plg_ref + "templates-and-shortcodes\/images\/";

			var clusterStyles = [{
				textColor: 'grey',
				url: path + "mrkr-clstr-sml.png",
				height: 50,
				width: 50
			}, {
				textColor: 'grey',
				url: path + "mrkr-clstr-mdm.png",
				height: 50,
				width: 50
			}, {
				textColor: 'grey',
				url: path + "mrkr-clstr-lrg.png",
				height: 50,
				width: 50
			}];
			var spiderConfig = {
				keepSpiderfied: true,
				event: 'mouseover',
			};


			google.maps.event.addDomListener(window, 'load', function(){
				self.init();
				init_listeners();
				mapSearch();
				if( settings.onLoadLocReq &&settings.geolocation && navigator.geolocation && ('' == settings.radius.radiusDef ||0<=settings.radius.radiusDef)) {
					self.radiusFilter.trigger('input');
					navigator.geolocation.getCurrentPosition(function(position) {
						wyzSaveLocationCookies(position);
						self.myTrueLat = position.coords.latitude;
						self.myTrueLon = position.coords.longitude;
						
						mapSearch();
					}, function(error) {
						wyzLocationCookiesError(error);
						handleLocationError(1);
					});
				}
				
			});


			var init_listeners = function() {
				self.submitBtn.on('click', mapSearch);
				/*self.submitBtn.click(function(){
					ajax_search();
				});*/

				self.sidebarWidth = jQuery(window).width();

				if( jQuery('html').attr('dir') == 'rtl')
					self.sidebar.css({'right':self.sidebarWidth*2});
				else
					self.sidebar.css({'right':-self.sidebarWidth*2});

				self.sourceElement.find(".map-share-btn").live({
					click: function (e) {
						e.preventDefault();
						jQuery(this).parent().nextAll(".business-post-share-cont").first().toggle();
					}
				});


				self.sourceElement.find('.search-wrapper .close-button').click(function(event){
					event.preventDefault();
					if(self.slided){
						if( jQuery('html').attr('dir') == 'rtl')
							self.sidebar.animate({right:self.sidebarWidth}, {queue: false, duration: 500});
						else
							self.sidebar.animate({right:-self.sidebarWidth}, {queue: false, duration: 500});
						if(settings.templateType==1)
							self.sourceElement.find('.home-map').animate({ marginBottom: '0px' }, 500);
						
						self.slided = false;
					}
				});

				settings.templateType = parseInt(settings.templateType);

				switch ( settings.templateType ) {
					case 1:
						self.markerAnchorX = 20;
						self.markerAnchorY = 55;
						self.markerWidthX = 40;
						self.markerWidthY = 55;
					break;
					case 2:
						self.markerAnchorX = 0;
						self.markerAnchorY = 60;
						self.markerWidthX = 60;
						self.markerWidthY = 60;
					break;
				}

				var useDimmer = 1 == settings.templateType;

				//pretty select box
				self.catFilter.selectator({
					labels: {
						search: settings.translations.searchText
					},
					useDimmer: useDimmer
				});

				self.locText.filter.selectator({
					labels: {
						search: settings.translations.searchText
					},
					useDimmer: useDimmer
				});

				//add km or miles to the map radius slider
				if(settings.radiusUnit=='mile')
					self.sourceElement.find('.location-search .input-range p span').addClass('distance-miles');
				else
					self.sourceElement.find('.location-search .input-range p span').addClass('distance-km');

				var range = self.radiusFilter;
				var radius = self.radiusFilter.attr('value')
				self.radiusFilter.siblings('p').find('span').html( radius );

				var rtl = jQuery('html[dir="rtl"]').length ? 'right' : 'left';
				range.rangeslider({
					polyfill: false,
					direction: rtl,
					fillClass: 'range_fill',
					handleClass: 'range_handle',
					onSlideEnd: function(pos, value) {

						if (self.locationFirstRun) {
							self.locationFirstRun = false;

							//geolocation activation
							if (settings.geolocation && navigator.geolocation) {
								self.mapMask.fadeIn('"slow"') ;
								navigator.geolocation.getCurrentPosition(function(position) {
									wyzSaveLocationCookies(position);
									self.mapMask.fadeOut('"fast"') ;
									self.myTrueLat = position.coords.latitude;
									self.myTrueLon = position.coords.longitude;

								}, function(error) {
									self.mapMask.fadeOut('"fast"') ;
									wyzLocationCookiesError(error);
									handleLocationError(1);
								});
							} else {
								if (settings.geolocation)
									handleLocationError(3);
								else
									handleLocationError(2);
							}
						} 
					}
				});
			}


			self.init = function(){
				if(self.searching && jQuery.isEmptyObject(settings.GPSLocations)){
					toastr.info( settings.translations.noBusinessesFound );
				}

				self.latLng = new google.maps.LatLng(parseFloat(settings.defCoor.latitude), parseFloat(settings.defCoor.longitude));
				var options = {
					zoom: parseInt(settings.defCoor.zoom),
					center: self.latLng,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
				};
			
				self.map = new google.maps.Map(document.getElementById(self.sourceElement.find('.home-map').attr('id')), options);

		        self.markerSpiderfier = new OverlappingMarkerSpiderfier(self.map, spiderConfig);


				if ( '' != settings.mapSkin ) {
					self.map.setOptions({styles: settings.mapSkin});
				}

				self.myoverlay = new google.maps.OverlayView();
			    self.myoverlay.draw = function () {
			        this.getPanes().markerLayer.id='markerLayer';
			    };
				self.myoverlay.setMap(self.map);


				self.mapCntr = 0;
				self.markers = [];
				self.infowindow = new google.maps.InfoWindow();
				self.bounds = new google.maps.LatLngBounds();
				
				settings.gpsLen = settings.GPSLocations.length;
				self.lastIndex = 0;

				self.markerCluster = new MarkerClusterer(self.map, self.markers, { maxZoom: 12, averageCenter: true, styles: clusterStyles });
			}

			var mapSearch = function() {
				if ('dropdown' != settings.filterType){
					if ( '' == self.locText.locText.val()){
						self.locText.lat.val('');
						self.locText.lon.val('');
						self.locText.filter.val('');
					}
				}


				self.geoEnabled = settings.geolocation && navigator.geolocation && 0 < self.radVal;

				if ( self.geoEnabled && (isNaN(self.radVal) || 0 > self.radVal ) )
					toastr.warning( settings.translations.notValidRad);
				else {
					if(self.mapFirstLoad&&settings.radius.radiusDef>0 && !isNaN(settings.radius.radiusDef)){
						self.radVal = settings.radius.radiusDef;
					}
					var catId = self.catFilter.val();
					if ( self.mapFirstLoad )
						catId = self.defCat;
					var busName = self.searchNames.val();

					self.mapMask.fadeIn('"slow"');
					self.mapLoading.fadeIn('"fast"');
					
					var locData = self.locText.filter.val();
					if ( self.mapFirstLoad )
						locData = settings.defLoc;
					var locId = '';

					if ( 'dropdown' == settings.filterType ) {
						if( -1 != locData && '' != locData){
							locData = JSON.parse(locData);
							self.myLat = locData.lat;
							self.myLon = locData.lon;
							self.searchMarker = settings.locLocationMarker;
						}else if(self.geoEnabled){
							self.myLat = self.myTrueLat;
							self.myLon = self.myTrueLon;
						}else{
							self.searchMarker = settings.myLocationMarker;
						}
						
						locId = locData.id;

						if( undefined == locId )
							locId = '';
					} else {
						if ( '' != self.locText.lat.val()){
							
							self.myLat = self.locText.lat.val();
							self.myLon = self.locText.lon.val();
							locId = '';
							if ( self.radVal<1)self.radVal=500;
						} else if(self.radVal>0) {
							self.myLat = self.myTrueLat;
							self.myLon = self.myTrueLon;
						}
						self.searchMarker = settings.myLocationMarker;
					}
					page = 0;

					if(jQuery.active>0&&undefined!=active)
						active.abort();

					if(self.slided){
						if( jQuery('html').attr('dir') == 'rtl')
							self.sidebar.animate({right:self.sidebarWidth}, {queue: false, duration: 500});
						else
							self.sidebar.animate({right:-self.sidebarWidth}, {queue: false, duration: 500});
						if(settings.templateType==1)
							self.sourceElement.find('.home-map').animate({ marginBottom: '0px' }, 500);
						self.slided = false;
					}

					self.fRadVal = self.radVal;

					if (-1 == catId && '' === busName && "" == locId && 'text' != settings.filterType )
						ajax_search('', '', '');
					else
						ajax_search(catId, busName, locId);

					if(self.mapFirstLoad)
						self.mapFirstLoad = false;
					else
						self.searching = true;
				}
			}


			var ajax_search = function(catId, busName, locId) {
				active = jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: "action=global_map_search&nonce=" + ajaxnonce + "&page=" + page + "&bus-name=" + busName + "&loc-id=" + locId + "&is-listing=false&is-grid=false&cat-id=" + catId + ( ( self.geoEnabled || 'text' == settings.filterType ) ? "&rad=" +  self.fRadVal + "&lat=" + self.myLat + "&lon=" + self.myLon : '') + '&posts-per-page=-1',
					success: function(result) {

						result = JSON.parse(result);

						if(0==page){
							resetGlobalData(result);
							self.init();
							self.mapMask.fadeOut('fast');
						}
						else{
							updateGlobalData(result);
						}

						if(0==parseInt(result.postsCount)){
							self.searching=false;
							self.mapLoading.fadeOut('"fast"');
							return;
						}
						
						updateMap();

						page+=parseInt(result.postsCount);
						ajax_search(catId, busName, locId);
					}
				});
			}


			var updateMap = function(){
				var marker;
				settings.gpsLen = settings.GPSLocations.length;
				for (var ii = self.lastIndex; ii<settings.gpsLen; ii++){
					if(undefined!=settings.GPSLocations[ii]&&''!=settings.GPSLocations[ii].latitude&&''!=settings.GPSLocations[ii].longitude && ! isNaN(parseFloat(settings.GPSLocations[ii].latitude)) && !isNaN(parseFloat(settings.GPSLocations[ii].longitude) ) ){
						var latlng = new google.maps.LatLng(parseFloat(settings.GPSLocations[ii].latitude), parseFloat(settings.GPSLocations[ii].longitude));

						self.content = '<div id="content">'+
							'<div style="display:none;">' + settings.businessNames[ii] + '</div>' +
							'<div id="siteNotice">'+
							'</div>'+
							'<div id="mapBodyContent">'+
							('' != settings.businessLogoes[ii] ? settings.businessLogoes[ii] : '<img class="business-logo-marker wp-post-image" src="'+settings.defLogo+'"/>' )
							+
							'<h4>'+settings.businessNames[ii]+'</h4>'+	
							( null != settings.afterBusinessNames[ii] ? ( '<div>' + settings.afterBusinessNames[ii] + '</div>' ) : '' ) +
							'<a href="'+settings.businessPermalinks[ii]+'"' + ( 2 == settings.templateType ? '' : ' class="wyz-button" style="background-color:' + settings.businessCategoriesColors[ii] + ';"' ) + '>'+settings.translations.viewDetails+'</a>'+		
							'</div>'+
							'</div>';

						if ('' !== settings.markersWithIcons[ii]) {
							marker = new google.maps.Marker({
								position: latlng,
								counter: ii,
								icon: {
									url: settings.markersWithIcons[ii],
									size: new google.maps.Size(self.markerWidthX,self.markerWidthY),
									origin: new google.maps.Point(0, 0),
									anchor: new google.maps.Point(self.markerAnchorX, self.markerAnchorY),
								},
								info: self.content,
								shadow: settings.myLocationMarker,
								optimized: false,
								category: parseInt(settings.businessCategories[ii]),
								busName: settings.businessNames[ii],
								busId: settings.businessIds[ii],
								busPermalink:settings.businessPermalinks[ii],
								favorite:settings.favorites[ii],
								galleryLoaded: false,
								gallery: [],
							});

							var circle2 = new google.maps.Circle({
								map: self.map,
								radius: parseInt(settings.range_radius[ii]),
								fillColor: settings.radFillColor,
								strokeColor: settings.radStrokeColor,
								strokeWeight: 1
							});

							circle2.bindTo('center', marker , 'position');

						} else{
							marker = new google.maps.Marker({
								busName: settings.businessNames[ii],
								counter: ii,
								info: self.content,
								busId: settings.businessIds[ii],
								busPermalink:settings.businessPermalinks[ii],
								position: latlng,
								galleryLoaded: false,
								favorite:settings.favorites[ii],
								gallery: [],
							});

							var circle2 = new google.maps.Circle({
								map: self.map,
								radius: parseInt(settings.range_radius[ii]),
								fillColor: settings.radFillColor,
								strokeColor: settings.radStrokeColor,
								strokeWeight: 1
							});

							circle2.bindTo('center', marker , 'position');
						}
						if(2 != settings.templateType ){
							marker.setAnimation(google.maps.Animation.DROP);
						}

						if(self.searching || 'on' == settings.autoFit) {
							self.bounds.extend(marker.position);
							self.map.fitBounds(self.bounds);
						}


						google.maps.event.addListener(marker, 'click', function() {

							jQuery('.map-container').trigger('wyz_marker_click', [this.busId] );

							if (! settings.sidebar){
								self.infowindow.setContent(this.info);
								self.infowindow.open(self.map, this);
							}

							
							this.setAnimation(google.maps.Animation.oo);
							if ( settings.sidebar){
							self.sourceElement.find('.map-company-info .company-logo').attr( 'href',this.busPermalink );
							self.sourceElement.find('.map-company-info .map-company-info-name>a').attr( 'href',this.busPermalink ).html(this.busName);
							self.sourceElement.find('.page-map-right-content .rate-bus').attr('href',this.busPermalink +'#'+settings.tabs['rating'] );

							self.sourceElement.find('.map-company-info .map-company-info-slogan').html('');
							self.sourceElement.find('.page-map-right-content .map-company-info .company-logo img').attr('src','');
							self.sourceElement.find('.map-company-info .map-company-info-rating').html('');
							if(self.sourceElement.find('.map-company-info .map-company-info-name .verified-icon').length)
								self.sourceElement.find('.map-company-info .map-company-info-name .verified-icon').remove();

							if(settings.favEnabled){
								var favBus = self.sourceElement.find('.page-map-right-content .fav-bus');
								favBus.data("busid",this.busId );

								if ( this.favorite){
									favBus.find('i').removeClass('fa-heart-o');
									favBus.find('i').addClass('fa-heart');
									favBus.data('fav',1 );

								} else {
									favBus.find('i').removeClass('fa-heart');
									favBus.find('i').addClass('fa-heart-o');
									favBus.data('fav',0 );
								}
							}

							if(!self.slided){
								self.sidebar.animate({right:'0'}, {queue: false, duration: 500});
								if(settings.templateType==1){
									var margin = self.sourceElement.find('.home-map').position().top + self.sourceElement.find('.home-map').height() - self.sourceElement.find('.location-search-float').position().top;
									self.sourceElement.find('.home-map').animate({ marginBottom: margin +'px' }, 500);
								}
								self.slided = true;
							}

							self.galleryContainer.html('');


							if(!this.galleryLoaded){
								var This = this;
								
								self.sourceElement.find('.page-map-right-content .search-wrapper .map-sidebar-loading').addClass('loading-spinner');
								self.sourceElement.find('.page-map-right-content .search-wrapper').css('background-image','');

								jQuery.ajax({
									type: "POST",
									url: ajaxurl,
									data: "action=business_map_sidebar_data&nonce=" + ajaxnonce + "&bus_id=" + this.busId ,
									success: function(result) {

										result = JSON.parse(result);

										This.galleryLoaded = true;
										This.gallery = result;

										self.sourceElement.find('.map-container').trigger('business_map_sidebar_data_loaded', [result] );

										self.sourceElement.find('.map-company-info .map-company-info-slogan').html(result.slogan);

										self.sourceElement.find('.map-company-info .map-company-info-name>a').before(result.verified);

										self.sourceElement.find('.page-map-right-content .map-company-info .company-logo img').attr('src',result.logo);

										self.sourceElement.find('.page-map-right-content .search-wrapper .map-sidebar-loading').removeClass('loading-spinner');

										for(var i=0;i<result.gallery.length;i++){
											self.galleryContainer.append( '<li><img src="'+result.gallery.thumb[i]+'" alt=""></li>' );
										}
										if ( result.gallery.length > 0)
											self.sourceElement.find('.page-map-right-content .map-info-gallery li:last-child').append('<a class="gal-link" href="'+This.busPermalink+'#'+settings.tabs['photo']+'">'+settings.translations.viewAll+'</a>');
										self.sourceElement.find('.map-company-info .map-company-info-desc').html(result.slogan );
										self.sourceElement.find('.map-company-info .map-company-info-rating').html(result.ratings );
										self.sourceElement.find('.page-map-right-content .search-wrapper').css('background-image','url('+result.banner_image+')');
										self.sourceElement.find('.map-info-links').append(result.share);
										if ( result.canBooking) {
											self.sourceElement.find('.page-map-right-content .book-bus').attr('href',This.busPermalink +'#'+settings.tabs['booking'] );
											self.sourceElement.find('.page-map-right-content .book-bus').parent().css('display','block');
											self.sourceElement.find('.page-map-right-content .map-info-links li').each(function(){
												jQuery(this).removeClass('three-way-width');
											});
										} else {
											self.sourceElement.find('.page-map-right-content .book-bus').attr('href','');
											self.sourceElement.find('.page-map-right-content .book-bus').parent().css('display','none');
											self.sourceElement.find('.page-map-right-content .map-info-links li').each(function(){
												jQuery(this).addClass('three-way-width');
											});
										}
										self.sourceElement.find('.page-map-right-content .map-info-gallery li .gal-link').css('line-height',self.sourceElement.find('.page-map-right-content .map-info-gallery').width()/4+'px');
									}
								});
							} else {
								self.sourceElement.find('.page-map-right-content .search-wrapper .map-sidebar-loading').removeClass('loading-spinner');
								for(var i=0;i<this.gallery.gallery.length;i++){
									self.galleryContainer.append( '<li><img src="'+this.gallery.gallery.thumb[i]+'" alt=""></li>' );
								}

								self.sourceElement.find('.page-map-right-content .map-company-info .company-logo img').attr('src',this.gallery.logo);
								self.sourceElement.find('.map-company-info .map-company-info-slogan').html(this.gallery.slogan);
								self.sourceElement.find('.map-company-info .map-company-info-name>a').before(this.gallery.verified);

								if(this.gallery.gallery.length)
									self.sourceElement.find('.page-map-right-content .map-info-gallery li:last-child').append('<a class="gal-link" href="'+this.busPermalink+'#'+settings.tabs['photo']+'">'+settings.translations.viewAll+'</a>');
								self.sourceElement.find('.map-company-info .map-company-info-desc').html(this.gallery.slogan );
								self.sourceElement.find('.map-company-info .map-company-info-rating').html(this.gallery.ratings );
								self.sourceElement.find('.page-map-right-content .search-wrapper').css('background-image','url('+this.gallery.banner_image+')');
								self.sourceElement.find('.map-info-links').append(this.gallery.share);
								if ( this.gallery.canBooking) {
									self.sourceElement.find('.page-map-right-content .book-bus').attr('href',this.busPermalink +'#'+settings.tabs['booking'] );
									self.sourceElement.find('.page-map-right-content .book-bus').parent().css('display','block');
									self.sourceElement.find('.page-map-right-content .map-info-links li').each(function(){
										jQuery(this).css('width','25%');
									});
								} else {
									self.sourceElement.find('.page-map-right-content .book-bus').attr('href','');
									self.sourceElement.find('.page-map-right-content .book-bus').parent().css('display','none');
									self.sourceElement.find('.page-map-right-content .map-info-links li').each(function(){
										jQuery(this).css('width','33%');
									});
								}
								self.sourceElement.find('.page-map-right-content .map-info-gallery li .gal-link').css('line-height',self.sourceElement.find('.page-map-right-content .map-info-gallery').width()/4+'px');

								self.sourceElement.find('.map-container').trigger('business_map_sidebar_data_loaded', [this.gallery] );
							}
							}

							self.sourceElement.find('.map-container').trigger('wyz_marker_click', [this.busId] );
						});

						

						self.markers.push(marker);
						self.markerSpiderfier.addMarker(marker);
						if( 0 >= self.radVal && ( self.searching || settings.autoFit )&& marker != undefined ) {
							self.bounds.extend(marker.position);
							self.map.fitBounds(self.bounds);
						}
					}
					
					self.mapCntr++;
				}
				if( self.pageFirstLoad && settings.onLoadLocReq && settings.geolocation && navigator.geolocation && 1>settings.radius.radiusDef) {
					self.mapMask.fadeIn('"slow"');
					var la,lo;
					navigator.geolocation.getCurrentPosition(function(position) {
						wyzSaveLocationCookies(position);
						la = position.coords.latitude;
						lo = position.coords.longitude;

						self.mapMask.fadeOut('"fast"');
						marker = new google.maps.Marker({
							position: { lat: parseFloat(la), lng: parseFloat(lo) },
							icon: {
								url: self.searchMarker,
								size: new google.maps.Size(40,55),
								origin: new google.maps.Point(0, 0),
								anchor: new google.maps.Point(20, 55),
							},
							map: self.map
						});
						if(2 != settings.templateType )
							marker.setAnimation(google.maps.Animation.DROP);
						self.markers.push(marker);
						self.markerSpiderfier.addMarker(marker);
						self.map.setCenter({lat:la, lng:lo});
					}, function(error) {
						wyzLocationCookiesError(error);
						handleLocationError(1);
					});
				} else {
					if (settings.geolocation && self.pageFirstLoad && settings.onLoadLocReq && 1>settings.radius.radiusDef){
						handleLocationError(3);
					}
				}

				self.pageFirstLoad=false;
				if ((self.geoEnabled||'dropdown' != settings.filterType) && (self.searching || (settings.radius.radiusDef>0 && settings.onLoadLocReq)) && (0!=self.myLat||0!=self.myLon)) {

					marker = new google.maps.Marker({
						position: { lat: parseFloat(self.myLat), lng: parseFloat(self.myLon) },
						icon: {
							url: self.searchMarker,
							size: new google.maps.Size(40,55),
							origin: new google.maps.Point(0, 0),
							anchor: new google.maps.Point(20, 55),
						},
						map: self.map
					});
					if(2 != settings.templateType )
						marker.setAnimation(google.maps.Animation.DROP);

					//setup radius multiplier in miles or km
					var radMult = ('km'==settings.radiusUnit ? 1000 : 1609.34);
					// Add circle overlay and bind to marker
					var circle = new google.maps.Circle({
						map: self.map,
						radius: self.radVal * radMult,
						fillColor: '#42c2ff',
						strokeColor: '#00aeff',
						strokeWeight: 1
					});
					circle.bindTo('center', marker, 'position');

					self.bounds.extend(marker.position);
						
					self.map.fitBounds(self.bounds);

					var sz = 0;
					sz = (self.radVal<20? 11 : ( self.radVal<50 ? 10 : ( self.radVal<80?9:(self.radVal < 101 ? 8 : (self.radVal < 201 ? 7 : (self.radVal < 401 ? 6 : self.radVal < 501 ? 5 : 0))))));
					if (0 !== sz)
						self.map.setZoom(sz);
				}/* else {
					var sz = self.map.getZoom();
					if(!isNaN(sz)){
						if(sz>3)
							sz--;
						self.map.setZoom(sz);
					}
				}*/

				

		        self.markerSpiderfier = new OverlappingMarkerSpiderfier(self.map, spiderConfig);

				// all markers set and added to map, update marker cluster

				self.markerCluster = new MarkerClusterer(self.map, self.markers, { maxZoom: 12, averageCenter: true, styles: clusterStyles });
				
		        self.markerCluster.setMaxZoom(15);
				self.lastIndex = settings.gpsLen;
			}

			var resetGlobalData = function(result){
				var markerType = settings.markerType;
				var sidebar = settings.sidebar;
				var tempMark = settings.myLocationMarker;
				var spiderfyMarker = settings.spiderfyMarker;
				var tempLocMark = settings.locLocationMarker;
				var tempGeolocation = settings.geolocation;
				var defCoor = settings.defCoor;
				var defLogo = settings.defLogo;
				var radUnit = settings.radiusUnit;
				var radius = settings.radius;
				var translations = settings.translations;
				var tmpFilterType = settings.filterType;
				var tmpTemplateType = settings.templateType;
				var tmpTabs = settings.tabs;
				var tmpTrueLat = settings.myTrueLat;
				var tmpTrueLon = settings.myTrueLon;
				var tmpFavEn = settings.favEnabled;
				var tmpSkin = settings.mapSkin;
				var autoFit = settings.autoFit;
				var onLoadLocReq = settings.onLoadLocReq;

				var radFillColor = settings.radFillColor;
				var radStrokeColor = settings.radStrokeColor;

				settings = null;
				google.maps.event.trigger(self.map, 'resize');
				settings = result;


				settings.radFillColor = radFillColor;
				settings.radStrokeColor = radStrokeColor;
				settings.markerType = markerType;
				settings.sidebar = sidebar;
				settings.myLocationMarker = tempMark;
				settings.spiderfyMarker = spiderfyMarker;
				settings.locLocationMarker = tempLocMark;
				settings.geolocation = tempGeolocation;
				settings.defCoor = defCoor;
				settings.defLogo = defLogo;
				settings.radiusUnit = radUnit;
				settings.radius = radius;
				settings.translations = translations;
				settings.filterType = tmpFilterType;
				settings.templateType = tmpTemplateType;
				settings.tabs = tmpTabs;
				settings.myTrueLat = tmpTrueLat;
				settings.myTrueLon = tmpTrueLon;
				settings.favEnabled = tmpFavEn;
				settings.mapSkin = tmpSkin;
				settings.autoFit = autoFit;
				settings.onLoadLocReq = onLoadLocReq;

				settings.businessList = result.businessList;
				settings.postsPerPage = result.postsPerPage;
				settings.businessIds = result.businessIds;
			}



			var input_interval;

			var intialize = function() {
				self.infowindow = new google.maps.InfoWindow();
				self.bounds = new google.maps.LatLngBounds();
				input_interval = setTimeout(input_autocomplete, 500);
			}

			var input_autocomplete = function() {
				if (!(typeof google.maps.places === 'object') )
					return;
				clearInterval(input_interval);
				autocomplete = new google.maps.places.Autocomplete(document.getElementById( self.locText.locText.attr('id')));
				google.maps.event.addListener(autocomplete, 'place_changed', function () {
					var place = autocomplete.getPlace();
					self.locText.text.val(place.name);
					self.locText.lat.val(place.geometry.location.lat());
					self.locText.lon.val(place.geometry.location.lng());

				});
			}

			if('text'==settings.filterType){
				google.maps.event.addDomListener(window, 'load', intialize);
			}


			var updateGlobalData = function(result){
				for(var i=0;i<result.postsCount;i++){
					settings.GPSLocations.push(result.GPSLocations[i]);
					settings.range_radius.push(result.range_radius[i]);

					settings.markersWithIcons.push(result.markersWithIcons[i]);
					settings.businessNames.push(result.businessNames[i]);

					settings.businessLogoes.push(result.businessLogoes[i]);
					settings.businessPermalinks.push(result.businessPermalinks[i]);
					settings.businessCategories.push(result.businessCategories[i]);
					settings.businessCategoriesColors.push(result.businessCategoriesColors[i]);
					
				}
				
			}

			var handleLocationError = function(browserHasGeolocation) {
				switch (browserHasGeolocation) {
					case 1:
						toastr.error(settings.translations.geoFail);
						break;
					case 2:
						break;
					case 3:
						toastr.warning(settings.geolocation.geoBrowserFail);
				}
			}


			self.sourceElement.find('.fav-bus').click(favoriteBus);

			var favoriteBus = function(event){
				event.preventDefault();
				var bus_id = jQuery(this).data('busid');
				if( '' == bus_id || undefined == bus_id ) return;
				var isFav = jQuery(this).data('fav');
				jQuery(this).parent().addClass('fade-loading');
				jQuery(this).unbind('favoriteBus');
				var favType = isFav == 1 ? 'unfav' : 'fav';
				var target = jQuery(this);

				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: "action=business_favorite&nonce=" + ajaxnonce + "&business_id=" + bus_id + "&fav_type=" + favType,
					success: function(result) {
						target.parent().removeClass('fade-loading');
						var i;
						for(i=0;i<settings.businessIds.length;i++){
							if(settings.businessIds[i] == bus_id)
								break;
						}
						if(favType=='fav'){
							if(i<settings.favorites.length)
								settings.favorites[i]=true;
							target.find('i').removeClass('fa-heart-o');
							target.find('i').addClass('fa-heart');
							target.data('fav',1 );
						} else {
							if(i<settings.favorites.length)
								settings.favorites[i]=false;
							target.find('i').removeClass('fa-heart');
							target.find('i').addClass('fa-heart-o');
							target.data('fav',0 );
						}
					}
				});
			}

			self.radiusFilter.on('input', function() {
				jQuery(this).siblings('p').find('span').html( jQuery(this).val() );
				self.radVal = jQuery(this).val();
			});

			self.searchNames.keypress(function(e) {
				if(e.which == 13) {
					self.submitBtn.trigger('click');
				}
			});


		};

	}( jQuery ));
	
	jQuery('.map-container').each(function(){
		var id = jQuery(this).attr('id');
		if(undefined!=id&&''!=id){
			id = id.split('__-_');
			if(typeof id[1] != 'undefined') {
				id = id[1];
				jQuery(this).wyziGlobalMap(jQuery(this),globalOptions[id]);
			}
		}
	});
}