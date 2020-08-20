var wyz_map_loaded = false;
var wyz_dom_loaded = false;

document.addEventListener('DOMContentLoaded', function() {
	wyz_dom_loaded = true;
	wyz_init_load_map();
}, false);

function wyz_init_load_map() {
	if(wyz_map_loaded)return;
	if (typeof google === 'object' && typeof google.maps === 'object' && wyz_dom_loaded ) {
		wyz_map_loaded = true;
		wyz_load_map();
	}
}

function wyz_load_map(){
	var latLng;
	var map;
	var markerSpiderfier;
	var searching = false;
	var geoEnabled = false;
	var myLat = 0;
	var myLon = 0;
	var myTrueLat = 0;
	var myTrueLon = 0;
	var radVal = 0;
	var fRadVal = 0;
	var offset = 1;
	var append = '';
	var appendTop = '';
	var appendBottom = '';
	var locationFirstRun = true;
	var mapFirstLoad = true;
	var pageFirstLoad = true;
	var sidebarWidth =0;

	var mapCntr = 0;
	var markers = [];
	var infowindow;
	var bounds;
	var content;
	var gpsLen = globalMap.GPSLocations.length;
	var lastIndex = 0;
	var searchMarker = globalMap.myLocationMarker;
	var spiderfyMarker = globalMap.spiderfyMarker;
	var markerAnchorX;
	var markerAnchorY;
	var markerWidthX;
	var markerWidthY;
	var myoverlay;
	var myPosition;

	var page = 0;


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
	var markerCluster;
	var spiderConfig = {
		keepSpiderfied: true,
		event: 'mouseover',
	};

	function initMap() {
		if(searching && jQuery.isEmptyObject(globalMap.GPSLocations)){
			toastr.info( globalMap.translations.noBusinessesFound );
		}

		// Hide Business list under map
		if (!globalMap.defCoor || globalMap.defCoor.latitude === '' || undefined === globalMap.defCoor.latitude){
			latLng = new google.maps.LatLng(0, 0);
			globalMap.defCoor = new Object;
			globalMap.defCoor.latitude = 0;
			globalMap.defCoor.longitude = 0;
			globalMap.defCoor.zoom = 11;
		}
		else latLng = new google.maps.LatLng(parseFloat(globalMap.defCoor.latitude), parseFloat(globalMap.defCoor.longitude));
		var scrollwheel = 'on' == mapScrollZoom ? true : false;
		var options = {
			zoom: parseInt(globalMap.defCoor.zoom),
			scrollwheel : scrollwheel,
			center: latLng,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
		};

		map = new google.maps.Map(document.getElementById('home-map'), options);


        markerSpiderfier = new OverlappingMarkerSpiderfier(map, spiderConfig);


		if ( '' != globalMap.mapSkin ) {
			map.setOptions({styles: globalMap.mapSkin});
		}

		myoverlay = new google.maps.OverlayView();
	    myoverlay.draw = function () {
	        this.getPanes().markerLayer.id='markerLayer';
	    };
		myoverlay.setMap(map);


		mapCntr = 0;
		markers = [];
		infowindow = new google.maps.InfoWindow();
		bounds = new google.maps.LatLngBounds();
		
		gpsLen = globalMap.GPSLocations.length;
		lastIndex = 0;


		markerCluster = new MarkerClusterer(map, markers, { maxZoom: 12, averageCenter: true, styles: clusterStyles });

		if (!geoEnabled || !searching) {

		}
	}

	var slided = false;
	var hasMapSidebar = jQuery('#slidable-map-sidebar').length;

	function updateMap(){
		var marker;
		gpsLen = globalMap.GPSLocations.length;
		for (var ii = lastIndex; ii<gpsLen; ii++){
			if(undefined!=globalMap.GPSLocations[ii]&&''!=globalMap.GPSLocations[ii].latitude&&''!=globalMap.GPSLocations[ii].longitude && ! isNaN(parseFloat(globalMap.GPSLocations[ii].latitude)) && !isNaN(parseFloat(globalMap.GPSLocations[ii].longitude) ) ){
				var latlng = new google.maps.LatLng(parseFloat(globalMap.GPSLocations[ii].latitude), parseFloat(globalMap.GPSLocations[ii].longitude));

				content = '<div id="content">'+
					'<div style="display:none;">' + globalMap.businessNames[ii] + '</div>' +
					'<div id="siteNotice">'+
					'</div>'+
					'<div id="mapBodyContent">'+
					('' != globalMap.businessLogoes[ii] ? globalMap.businessLogoes[ii] : '<img class="business-logo-marker wp-post-image" src="'+globalMap.defLogo+'"/>' )
					+
					'<h4>'+globalMap.businessNames[ii]+'</h4>'+	
					( null != globalMap.afterBusinessNames[ii] ? ( '<div>' + globalMap.afterBusinessNames[ii] + '</div>' ) : '' ) +
					'<a href="'+globalMap.businessPermalinks[ii]+'"' + ( 2 == globalMap.templateType ? '' : ' class="wyz-button" style="background-color:' + globalMap.businessCategoriesColors[ii] + ';"' ) + '>'+globalMap.translations.viewDetails+'</a>'+		
					'</div>'+
					'</div>';

				if ('' !== globalMap.markersWithIcons[ii]) {
					marker = new google.maps.Marker({
						position: latlng,
						counter: ii,
						icon: {
							url: globalMap.markersWithIcons[ii],
							size: new google.maps.Size(markerWidthX,markerWidthY),
							origin: new google.maps.Point(0, 0),
							anchor: new google.maps.Point(markerAnchorX, markerAnchorY),
						},
						info: content,
						shadow: globalMap.myLocationMarker,
						optimized: false,
						category: parseInt(globalMap.businessCategories[ii]),
						busName: globalMap.businessNames[ii],
						busId: globalMap.businessIds[ii],
						busPermalink:globalMap.businessPermalinks[ii],
						favorite:globalMap.favorites[ii],
						galleryLoaded: false,
						gallery: [],
					});

					var circle2 = new google.maps.Circle({
							map: map,
							radius: parseInt(globalMap.range_radius[ii]),
							fillColor: globalMap.radFillColor,
							strokeColor: globalMap.radStrokeColor,
							strokeWeight: 1
						});

					circle2.bindTo('center', marker , 'position');

				} else{
					marker = new google.maps.Marker({
						busName: globalMap.businessNames[ii],
						counter: ii,
						info: content,
						busId: globalMap.businessIds[ii],
						busPermalink:globalMap.businessPermalinks[ii],
						position: latlng,
						galleryLoaded: false,
						favorite:globalMap.favorites[ii],
						gallery: [],
					});

					var circle2 = new google.maps.Circle({
							map: map,
							radius: parseInt(globalMap.range_radius[ii]),
							fillColor: globalMap.radFillColor,
							strokeColor: globalMap.radStrokeColor,
							strokeWeight: 1
						});

					circle2.bindTo('center', marker , 'position');
				}
				if(2 != globalMap.templateType ){
					marker.setAnimation(google.maps.Animation.DROP);
				}

				if(searching || 'on' == mapAutoZoom) {
					bounds.extend(marker.position);
					map.fitBounds(bounds);
				}
				
				var galleryContainer = jQuery('.page-map-right-content .map-info-gallery');



				google.maps.event.addListener(marker, 'click', function() {

					jQuery('.map-container').trigger('wyz_marker_click', [this.busId] );

					if ( globalMap.templateType == 1){
						infowindow.setContent(this.info);
						infowindow.open(map, this);
					}

					
					this.setAnimation(google.maps.Animation.oo);
					jQuery('.map-company-info .company-logo').attr( 'href',this.busPermalink );
					jQuery('.map-company-info #map-company-info-name>a').attr( 'href',this.busPermalink ).html(this.busName);
					jQuery('.page-map-right-content #rate-bus').attr('href',this.busPermalink +'#'+globalMap.tabs['rating'] );

					jQuery('.map-company-info #map-company-info-slogan').html('');
					jQuery('.page-map-right-content .map-company-info .company-logo img').attr('src','');
					jQuery('.map-company-info #map-company-info-rating').html('');
					if(jQuery('.map-company-info #map-company-info-name .verified-icon').length)
						jQuery('.map-company-info #map-company-info-name .verified-icon').remove();

					if(globalMap.favEnabled){
						var favBus = jQuery('.page-map-right-content .fav-bus');
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

					if(hasMapSidebar && !slided){
						jQuery('#slidable-map-sidebar').animate({right:'0'}, {queue: false, duration: 500});
						if(jQuery('.map-container .location-search-float').length){
							jQuery('.map-container .location-search-float').css({'margin-top': '0'});
						}
						slided = true;
					}

					galleryContainer.html('');


					if(!this.galleryLoaded){
						var This = this;
						
						jQuery('.page-map-right-content .search-wrapper #map-sidebar-loading').addClass('loading-spinner');
						jQuery('.page-map-right-content .search-wrapper').css('background-image','');

						jQuery.ajax({
							type: "POST",
							url: ajaxurl,
							data: "action=business_map_sidebar_data&nonce=" + ajaxnonce + "&bus_id=" + this.busId ,
							success: function(result) {

								result = JSON.parse(result);

								This.galleryLoaded = true;
								This.gallery = result;

								jQuery('.map-container').trigger('business_map_sidebar_data_loaded', [result] );

								jQuery('.map-company-info #map-company-info-slogan').html(result.slogan);

								jQuery('.map-company-info #map-company-info-name>a').before(result.verified);

								jQuery('.page-map-right-content .map-company-info .company-logo img').attr('src',result.logo);

								jQuery('.page-map-right-content .search-wrapper #map-sidebar-loading').removeClass('loading-spinner');

								for(var i=0;i<result.gallery.length;i++){
									galleryContainer.append( '<li><img src="'+result.gallery.thumb[i]+'" alt=""></li>' );
								}
								if ( result.gallery.length > 0)
									jQuery('.page-map-right-content .map-info-gallery li:last-child').append('<a class="gal-link" href="'+This.busPermalink+'#'+globalMap.tabs['photo']+'">'+globalMap.translations.viewAll+'</a>');
								jQuery('.map-company-info #map-company-info-desc').html(result.slogan );
								jQuery('.map-company-info #map-company-info-rating').html(result.ratings );
								jQuery('.page-map-right-content .search-wrapper').css('background-image','url('+result.banner_image+')');
								jQuery('.map-info-links').append(result.share);
								if ( result.canBooking) {
									jQuery('.page-map-right-content #book-bus').attr('href',This.busPermalink +'#'+globalMap.tabs['booking'] );
									jQuery('.page-map-right-content #book-bus').parent().css('display','block');
									jQuery('.page-map-right-content .map-info-links li').each(function(){
										jQuery(this).removeClass('three-way-width');
									});
								} else {
									jQuery('.page-map-right-content #book-bus').attr('href','');
									jQuery('.page-map-right-content #book-bus').parent().css('display','none');
									jQuery('.page-map-right-content .map-info-links li').each(function(){
										jQuery(this).addClass('three-way-width');
									});
								}
								jQuery('.page-map-right-content .map-info-gallery li .gal-link').css('line-height',jQuery('.page-map-right-content .map-info-gallery').width()/4+'px');
							}
						});
					} else {
						jQuery('.page-map-right-content .search-wrapper #map-sidebar-loading').removeClass('loading-spinner');
						for(var i=0;i<this.gallery.gallery.length;i++){
							galleryContainer.append( '<li><img src="'+this.gallery.gallery.thumb[i]+'" alt=""></li>' );
						}

						jQuery('.page-map-right-content .map-company-info .company-logo img').attr('src',this.gallery.logo);
						jQuery('.map-company-info #map-company-info-slogan').html(this.gallery.slogan);
						jQuery('.map-company-info #map-company-info-name>a').before(this.gallery.verified);

						if(this.gallery.gallery.length)
							jQuery('.page-map-right-content .map-info-gallery li:last-child').append('<a class="gal-link" href="'+this.busPermalink+'#'+globalMap.tabs['photo']+'">'+globalMap.translations.viewAll+'</a>');
						jQuery('.map-company-info #map-company-info-desc').html(this.gallery.slogan );
						jQuery('.map-company-info #map-company-info-rating').html(this.gallery.ratings );
						jQuery('.page-map-right-content .search-wrapper').css('background-image','url('+this.gallery.banner_image+')');
						jQuery('.map-info-links').append(this.gallery.share);
						if ( this.gallery.canBooking) {
							jQuery('.page-map-right-content #book-bus').attr('href',this.busPermalink +'#'+globalMap.tabs['booking'] );
							jQuery('.page-map-right-content #book-bus').parent().css('display','block');
							jQuery('.page-map-right-content .map-info-links li').each(function(){
								jQuery(this).css('width','25%');
							});
						} else {
							jQuery('.page-map-right-content #book-bus').attr('href','');
							jQuery('.page-map-right-content #book-bus').parent().css('display','none');
							jQuery('.page-map-right-content .map-info-links li').each(function(){
								jQuery(this).css('width','33%');
							});
						}
						jQuery('.page-map-right-content .map-info-gallery li .gal-link').css('line-height',jQuery('.page-map-right-content .map-info-gallery').width()/4+'px');
					}
				});

				

				markers.push(marker);
				markerSpiderfier.addMarker(marker);
				if( 0 >= radVal && ( searching || 'on' == mapAutoZoom )&& marker != undefined ) {
					bounds.extend(marker.position);
					map.fitBounds(bounds);
				}
			}
			
			mapCntr++;
		}
		if( pageFirstLoad && globalMap.onLoadLocReq &&globalMap.geolocation && navigator.geolocation && 1>globalMap.defRad) {
			jQuery('#map-mask').fadeIn('"slow"');
			var la,lo;
			navigator.geolocation.getCurrentPosition(function(position) {
				wyzSaveLocationCookies(position);
				la = position.coords.latitude;
				lo = position.coords.longitude;

				jQuery('#map-mask').fadeOut('"fast"');
				marker = new google.maps.Marker({
					position: { lat: parseFloat(la), lng: parseFloat(lo) },
					icon: {
						url: searchMarker,
						size: new google.maps.Size(40,55),//WyzMapSize1
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(20, 55),//WyzMapAnchor1
					},
					map: map
				});
				if(2 != globalMap.templateType )
					marker.setAnimation(google.maps.Animation.DROP);
				markers.push(marker);
				markerSpiderfier.addMarker(marker);
				map.setCenter({lat:la, lng:lo});
			}, function(error) {
				wyzLocationCookiesError(error);
				handleLocationError(1);
				jQuery('#map-mask').fadeOut('fast');
			});
		} else {
			if (globalMap.geolocation && pageFirstLoad && globalMap.onLoadLocReq && 1>globalMap.defRad){
				handleLocationError(3);
			}
		}

		pageFirstLoad=false;
		if ((geoEnabled||'dropdown' != globalMap.filterType) && (searching || (globalMap.defRad>0 && globalMap.onLoadLocReq)) && (0!=myLat||0!=myLon)) {

			//setup radius multiplier in miles or km
			var radMult = ('km'==globalMap.radiusUnit ? 1000 : 1609.34);
			// Add circle overlay and bind to marker
			var circleParams = {
				map: map,
				radius: radVal * radMult,
				fillColor: '#42c2ff',
				strokeColor: '#00aeff',
				strokeWeight: 1
			};

			if( '' == searchMarker) {
				circleParams.center = { lat: parseFloat(myLat), lng: parseFloat(myLon) };
				var circle = new google.maps.Circle(circleParams);
				bounds.extend(circleParams.center);
			} else {
				marker = new google.maps.Marker({
					position: { lat: parseFloat(myLat), lng: parseFloat(myLon) },
					icon: {
						url: searchMarker,
						size: new google.maps.Size(40,55),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(20, 55),
					},
					map: map
				});
				if(2 != globalMap.templateType )
					marker.setAnimation(google.maps.Animation.DROP);
				var circle = new google.maps.Circle(circleParams);
				circle.bindTo('center', marker, 'position');
				bounds.extend(marker.position);
			}

			map.fitBounds(bounds);

			var sz = 0;
			sz = (radVal<20? 11 : ( radVal<50 ? 10 : ( radVal<80?9:(radVal < 101 ? 8 : (radVal < 201 ? 7 : (radVal < 401 ? 6 : radVal < 501 ? 5 : 0))))));
			if (0 !== sz)
				map.setZoom(sz);
		} /*else {
			var sz = map.getZoom();
			if(!isNaN(sz)){
				if(sz>3)
					sz--;
				map.setZoom(sz);
			}
		}*/

		

        markerSpiderfier = new OverlappingMarkerSpiderfier(map, spiderConfig);

		// all markers set and added to map, update marker cluster

		markerCluster = new MarkerClusterer(map, markers, { maxZoom: 12, averageCenter: true, styles: clusterStyles });
		
        markerCluster.setMaxZoom(15);
		lastIndex = gpsLen;
	}


	function paginateBusinessList(){
		append = appendTop = appendBottom = '';
		if('' != globalMap.businessList){
			if(globalMap.hasBefore || globalMap.hasAfter){
				if(globalMap.hasBefore)
					append += '<li class="prev-page float-left">' + 
						'<button class="wyz-primary-color wyz-prim-color btn-square list-paginate" data-offset="-1"><i class="fa fa-angle-left"> </i> ' + globalMap.translations.prev + '</button></li>';
				if(globalMap.hasAfter){
					append += '<li class="next-page float-right">'+
						'<button class="wyz-primary-color wyz-prim-color btn-square list-paginate" data-offset="1">' + globalMap.translations.nxt + ' <i class="fa fa-angle-right"></i></button></li>';
				}
				if('' != append){
					appendTop = '<div class="blog-pagination fix" style="margin-bottom:20px;margin-top:0;"><ul>' + append + '</ul></div>';
					appendBottom = '<div class="blog-pagination fix" style="margin-bottom:30px;"><ul>' + append + '</ul></div>';
				}
			}
		}
	}

	// Display Businesses list under the map
	function updateBusinessList(){
		if('' != globalMap.businessList){
			paginateBusinessList();
			if(globalMap.ess_grid_shortcode == '') {
				jQuery('#business-list').html(appendTop + '<div class="bus-list-container">' + globalMap.businessList + '</div>' + appendBottom );
				jQuery(document).trigger('wyzMapListFetch', [globalMap.businessList, appendBottom, 1]);
			 }
			else {
				jQuery('#business-list').html(appendTop + '<div class="bus-list-container">' + globalMap.ess_grid_shortcode + '</div>' + appendBottom);
				jQuery(document).trigger('wyzMapListFetch', [globalMap.ess_grid_shortcode, appendBottom, 2]);
			}
			setTimeout(function(){ jQuery('#business-list').show(); jQuery('#business-list').resize();}, 100);
		}
	}


	var active;
	function mapSearch() {
		if ('dropdown' != globalMap.filterType){
			var tmpMapLocSearch = jQuery('#wyz-loc-filter-txt').val();
			if ( '' == tmpMapLocSearch){
				jQuery("#loc-filter-lat").val('');
				jQuery("#loc-filter-lon").val('');
				jQuery("#wyz-loc-filter").val('');
			}
		}


		geoEnabled = ( globalMap.geolocation && navigator.geolocation && 0 < radVal && 500>= radVal )?true:false;

		if ( geoEnabled && (isNaN(radVal) || 0 > radVal || 500 < radVal) )
			toastr.warning( globalMap.translations.notValidRad);
		else {
			if(mapFirstLoad&&globalMap.defRad>0 && !isNaN(globalMap.defRad)){
				radVal = globalMap.defRad;
			}
			var catId = jQuery("#wyz-cat-filter").val();
			if ( mapFirstLoad && undefined != globalMap.defCat && null != globalMap.defCat )
				catId = globalMap.defCat;
			var busName = jQuery("#map-names").val();

			jQuery('#map-mask').fadeIn('"slow"');
			jQuery('#map-loading').fadeIn('"fast"');
			
			var locData = jQuery("#wyz-loc-filter").val();
			if ( mapFirstLoad && undefined != globalMap.defLoc && null != globalMap.defLoc )
				locData = globalMap.defLoc;
			var locId = '';

			if ( 'dropdown' == globalMap.filterType ) {

				if( -1 != locData && '' != locData){
					locData = JSON.parse(locData);
					myLat = locData.lat;
					myLon = locData.lon;
					searchMarker = globalMap.locLocationMarker;
				}else if(geoEnabled){
					myLat = myTrueLat;
					myLon = myTrueLon;
				}else{
					searchMarker = globalMap.myLocationMarker;
				}
				
				locId = locData.id;

				if( undefined == locId )
					locId = '';
			} else {

				if ( '' != jQuery("#loc-filter-lat").val()){
					myLat = jQuery("#loc-filter-lat").val();
					myLon = jQuery("#loc-filter-lon").val();
					locId = '';
					if ( radVal<1)radVal=500;
				} else if(radVal>0) {
					myLat = myTrueLat;
					myLon = myTrueLon;
				}
				searchMarker = globalMap.myLocationMarker;
			}
			page = 0;

			if(jQuery.active>0&&undefined!=active)
				active.abort();

			if(hasMapSidebar && slided){
				if( jQuery('html').attr('dir') == 'rtl')
					jQuery('#slidable-map-sidebar').animate({right:sidebarWidth}, {queue: false, duration: 500});
				else
					jQuery('#slidable-map-sidebar').animate({right:-sidebarWidth}, {queue: false, duration: 500});
				slided = false;
				if(jQuery('.map-container .location-search-float').length){
					jQuery('.map-container .location-search-float').css({'margin-top':'-90px'});
				}
			}

			fRadVal = radVal;
			if (-1 == catId && '' === busName && "" == locId && 'text' != globalMap.filterType )
				ajax_map_search('', '', '', geoEnabled);
			else
				ajax_map_search(catId, busName, locId, geoEnabled);

			if(mapFirstLoad)
				mapFirstLoad = false;
			else
				searching = true;
		}
	}

	var input_interval;

	function intialize() {
		infowindow = new google.maps.InfoWindow();
		bounds = new google.maps.LatLngBounds();
		input_interval = setTimeout(input_autocomplete, 500);
	}

	function input_autocomplete() {
		if (!(typeof google.maps.places === 'object') )
			return;
		clearInterval(input_interval);
		input = document.getElementById('wyz-loc-filter-txt');
		autocomplete = new google.maps.places.Autocomplete(input);
		google.maps.event.addListener(autocomplete, 'place_changed', function () {
			var place = autocomplete.getPlace();
			document.getElementById('loc-filter-txt').value = place.name;
			document.getElementById('loc-filter-lat').value = place.geometry.location.lat();
			document.getElementById('loc-filter-lon').value = place.geometry.location.lng();

		});
	}

	if('text'==globalMap.filterType){
		google.maps.event.addDomListener(window, 'load', intialize);
	}

	function checkUpdateSlider() {
		if(globalMap.hasNearMe && globalMap.nearMeContent && jQuery('#near-me-businesses').length){
			jQuery('#near-me-businesses').html(globalMap.nearMeContent);
			jQuery('#near-me-businesses .recently-added-area').show();
			jQuery(document).trigger('wyz_rec_added_slider');
		}
	}

	function ajax_map_search(catId, busName, locId, geoEnabled) {
		active = jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: "action=global_map_search&nonce=" + ajaxnonce + "&page_id=" + globalMap.pageId + "&page=" + page + "&bus-name=" + busName + "&loc-id=" + locId + "&is-listing=" + globalMap.isListingPage+"&has-listings="+globalMap.hasLists + "&is-grid=" + globalMap.isGrid + "&cat-id=" + catId + ( ( geoEnabled || 'text' == globalMap.filterType ) ? "&rad=" +  fRadVal + "&lat=" + myLat + "&lon=" + myLon : '') + '&posts-per-page=' +(globalMap.isListingPage ? globalMap.postsPerPage : '-1'),
			success: function(result) {


				result = JSON.parse(result);

				console.log(result.hasAfter);
				if(0==page){
					resetGlobalData(result);
					checkUpdateSlider();
					initMap();
					jQuery('#map-mask').fadeOut('fast');
				}
				else
					updateGlobalData(result);


				if(0==parseInt(result.postsCount)){
					searching=false;
					jQuery('#map-loading').fadeOut('fast');
					return;
				}
				
				updateMap();

				if(globalMap.isListingPage && globalMap.hasLists && 0==page){
					updateBusinessList();
				}
				
				page+=parseInt(result.postsCount);
				ajax_map_search(catId, busName, locId, geoEnabled);
			}
		});
	}

	function resetGlobalData(result){
		var tempMark = globalMap.myLocationMarker;
		var tempLocMark = globalMap.locLocationMarker;
		var tempGeolocation = globalMap.geolocation;
		var defCoor = globalMap.defCoor;
		var defLogo = globalMap.defLogo;
		var radUnit = globalMap.radiusUnit;
		var grid = globalMap.isGrid;
		var translations = globalMap.translations;
		var tmpFilterType = globalMap.filterType;
		var tmpTemplateType = globalMap.templateType;
		var tmpTabs = globalMap.tabs;
		var tmpFavEn = globalMap.favEnabled;
		var tmpSkin = globalMap.mapSkin;
		var onLoadLocReq = globalMap.onLoadLocReq;
		var defRad = globalMap.defRad;
		var radFillColor = globalMap.radFillColor;
		var radStrokeColor = globalMap.radStrokeColor;
		var tmpPageId = globalMap.pageId;
		var tmpHasLists = globalMap.hasLists;

		var range_radius=  result.range_radius;

		globalMap = null;
		google.maps.event.trigger(map, 'resize');
		globalMap = result;



		globalMap.radFillColor = radFillColor;
		globalMap.radStrokeColor = radStrokeColor;
		globalMap.myLocationMarker = tempMark;
		globalMap.locLocationMarker = tempLocMark;
		globalMap.geolocation = tempGeolocation;
		globalMap.defCoor = defCoor;
		globalMap.defLogo = defLogo;
		globalMap.radiusUnit = radUnit;
		globalMap.isGrid = grid;
		globalMap.businessList = result.businessList;
		globalMap.isListingPage = result.isListingPage;
		globalMap.postsPerPage = result.postsPerPage;
		globalMap.businessIds = result.businessIds;
		globalMap.hasAfter = result.hasAfter;
		globalMap.hasBefore = result.hasBefore;
		globalMap.filterType = tmpFilterType;
		globalMap.templateType = tmpTemplateType;
		globalMap.translations = translations;
		globalMap.tabs = tmpTabs;
		globalMap.mapSkin = tmpSkin;
		globalMap.onLoadLocReq = onLoadLocReq;
		globalMap.defRad = defRad;
		globalMap.favEnabled = tmpFavEn;
		globalMap.pageId = tmpPageId;
		globalMap.range_radius=range_radius;
		globalMap.hasLists = tmpHasLists;
	}

	function updateGlobalData(result){
		for(var i=0;i<result.postsCount;i++){
			globalMap.GPSLocations.push(result.GPSLocations[i]);

			globalMap.markersWithIcons.push(result.markersWithIcons[i]);
			globalMap.businessNames.push(result.businessNames[i]);

			globalMap.businessLogoes.push(result.businessLogoes[i]);
			globalMap.businessPermalinks.push(result.businessPermalinks[i]);
			globalMap.businessCategories.push(result.businessCategories[i]);
			globalMap.businessCategoriesColors.push(result.businessCategoriesColors[i]);
			globalMap.range_radius.push(result.range_radius[i]);
			
		}
		globalMap.postsCount = result.postsCount;
		
	}



	function ajax_business_list(ofst){
		if(ofst != 1 && ofst != -1)
			return;
		if((ofst == -1 && offset == 0) || (ofst == 1 && ! globalMap.hasAfter))
			return;
		if(ofst == 1)
			offset++;
		else
			offset--;
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: "action=business_listing_paginate&nonce=" + ajaxnonce + "&business_ids=" + JSON.stringify(globalMap.businessIds) + "&is-grid=" + globalMap.isGrid + "&offset=" + offset + '&posts-per-page=' + globalMap.postsPerPage,
			success: function(result) {
				result = JSON.parse(result);
				if(null != result){
					globalMap.businessList = result.businessList;
					globalMap.hasBefore = result.hasBefore;
					globalMap.hasAfter = result.hasAfter;
					globalMap.ess_grid_shortcode = result.ess_grid_shortcode;
					updateBusinessList();
				}
			}
		});
	}

	function handleLocationError(browserHasGeolocation) {
		switch (browserHasGeolocation) {
			case 1:
				toastr.error(globalMap.translations.geoFail);
				break;
			case 2:
				break;
			case 3:
				toastr.warning(globalMap.translations.geoBrowserFail);
		}
	}

	jQuery(document).ready(function() {

		sidebarWidth = jQuery(window).width();

		if( jQuery('html').attr('dir') == 'rtl')
			jQuery('#slidable-map-sidebar').css({'right':sidebarWidth*2});
		else
			jQuery('#slidable-map-sidebar').css({'right':-sidebarWidth*2});

		jQuery(".map-share-btn").live({
			click: function (e) {
				e.preventDefault();
				jQuery(this).parent().nextAll(".business-post-share-cont").first().toggle();
			}
		});


		jQuery('.search-wrapper .close-button').click(function(event){
			event.preventDefault();
			if(slided){
				if( jQuery('html').attr('dir') == 'rtl')
					jQuery('#slidable-map-sidebar').animate({right:sidebarWidth}, {queue: false, duration: 500});
				else
					jQuery('#slidable-map-sidebar').animate({right:-sidebarWidth}, {queue: false, duration: 500});
				if(jQuery('.map-container .location-search-float').length){
					jQuery('.map-container .location-search-float').css({'margin-top': '-90px'});
				}
				slided = false;
			}
		});

		globalMap.templateType = parseInt(globalMap.templateType);

		switch ( globalMap.templateType ) {
			case 1:
				markerAnchorX = 20;
				markerAnchorY = 55;
				markerWidthX = 40;
				markerWidthY = 55;
			break;
			case 2:
				markerAnchorX = 0;
				markerAnchorY = 60;
				markerWidthX = 60;
				markerWidthY = 60;
			break;
		}

		if( typeof WyzMapAnchorX !== 'undefined' ){
			markerAnchorX = WyzMapAnchorX;
			markerAnchorY = WyzMapAnchorY;
		}
		if( typeof WyzMapWidthX !== 'undefined' ){
			markerWidthX = WyzMapWidthX;
			markerWidthY = WyzMapWidthY;
		}

		var useDimmer = 1 == wyz_template_type;

		//pretty select box
		jQuery('#wyz-cat-filter').selectator({
			labels: {
				search: globalMap.translations.searchText
			},
			useDimmer: useDimmer
		});

		jQuery('#wyz-loc-filter').selectator({
			labels: {
				search: globalMap.translations.searchText
			},
			useDimmer: useDimmer
		});

		//add km or miles to the map radius slider
		if(globalMap.radiusUnit=='mile')
			jQuery('.location-search .input-range p span').addClass('distance-miles');
		else
			jQuery('.location-search .input-range p span').addClass('distance-km');

		var range = jQuery('#loc-radius');
		var radius = jQuery('#loc-radius').attr('value')
		jQuery('#loc-radius').siblings('p').find('span').html( radius );

		var rtl = jQuery('html[dir="rtl"]').length ? 'right' : 'left';
		range.rangeslider({
			polyfill: false,
			direction: rtl,
			fillClass: 'range_fill',
			handleClass: 'range_handle',
			onSlideEnd: function(pos, value) {

				if (locationFirstRun) {
					locationFirstRun = false;

					//geolocation activation
					if (globalMap.geolocation && navigator.geolocation) {
						jQuery('#map-mask').fadeIn('"slow"') ;
						navigator.geolocation.getCurrentPosition(function(position) {
							wyzSaveLocationCookies(position);
							jQuery('#map-mask').fadeOut('"fast"') ;
							myTrueLat = position.coords.latitude;
							myTrueLon = position.coords.longitude;

						}, function(error) {
							jQuery('#map-mask').fadeOut('"fast"') ;
							wyzLocationCookiesError(error)
							handleLocationError(1);
						});
					} else {
						if (globalMap.geolocation)
							handleLocationError(3);
						else
							handleLocationError(2);
					}
				} 
			}
		});

		jQuery('#map-company-info-name-a, .map-company-info .company-logo, .map-info-links #rate-bus').click(function(e){
		    window.location.href = jQuery(this).attr("href");
		});


		jQuery('.fav-bus').click(favoriteBus);

		if ( 2 == globalMap.templateType){
	        jQuery('.range_handle').append('<span></span>');
	    
			var radiusLength = jQuery('.range_handle span');
			range.on('input', function() {
				radiusLength.html( jQuery(this).val() + ' ' + globalMap.radiusUnit );
				radVal = jQuery(this).val();
			});

			var locRadius = jQuery('input[type="range"]').attr('value');
			var radiusLength = jQuery('.range_handle span');
			radiusLength.html( locRadius + ' ' + globalMap.radiusUnit );
		} else{
			range.on('input', function() {
				jQuery(this).siblings('p').find('span').html( jQuery(this).val() );
				radVal = jQuery(this).val();
			});
		}


		jQuery('#map-names').keypress(function(e) {
			if(e.which == 13) {
				jQuery('#map-search-submit').trigger('click');
			}
		});

		jQuery('#map-search-submit').on('click', mapSearch);

		google.maps.event.addDomListener(window, 'load', function(){
			initMap();
			if(globalMap.hasSlider){
				navigator.geolocation.getCurrentPosition(function(position){
					wyzSaveLocationCookies(position);
					resume_work();
				}, function(error){
					wyzLocationCookiesError(error);
					resume_work();
				});
			} else {
				resume_work();
			}

			function resume_work(){
				mapSearch();
				if( globalMap.onLoadLocReq &&globalMap.geolocation && navigator.geolocation && 0<globalMap.defRad) {
					jQuery('#loc-radius').trigger('input');
					navigator.geolocation.getCurrentPosition(function(position) {
						wyzSaveLocationCookies(position);
						myTrueLat = position.coords.latitude;
						myTrueLon = position.coords.longitude;
						mapSearch();
					}, function(error) {
						wyzLocationCookiesError(error);
						handleLocationError(1);
					});
				}
			}
		});


		jQuery(".list-paginate").live('click',function(){
			jQuery(".list-paginate").prop('disabled', true).css('background-color','#68819b'); 
			ajax_business_list(parseInt(jQuery(this).data('offset')));
		});

	});

	function favoriteBus(event){
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
				for(i=0;i<globalMap.length;i++){
					if(globalMap.businessIds == bus_id)
						break;
				}
				if(favType=='fav'){
					if(i<globalMap.length)
						globalMap.favorites[i]=true;
					target.find('i').removeClass('fa-heart-o');
					target.find('i').addClass('fa-heart');
					target.data('fav',1 );
				} else {
					if(i<globalMap.length)
						globalMap.favorites[i]=false;
					target.find('i').removeClass('fa-heart');
					target.find('i').addClass('fa-heart-o');
					target.data('fav',0 );
				}
			}
		});
	}
}