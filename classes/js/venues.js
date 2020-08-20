(function($) {

	cmb_venues = {

		init: function(cmb2TypeClass) {

			var t = this,
				ajaxtag = $(cmb2TypeClass+' div.ajaxtag, '+cmb2TypeClass+'-sortable div.ajaxtag'),
				start_pos;

			$(cmb2TypeClass+' .hide-if-no-js, '+cmb2TypeClass+'-sortable .hide-if-no-js').removeClass('hide-if-no-js');
			$(cmb2TypeClass+' textarea, '+cmb2TypeClass+'-sortable textarea').hide();

			$(cmb2TypeClass+"-sortable .venueschecklist").sortable({
				start: function(event, ui) {
					start_pos = ui.item.index();
				},
				stop: function(event, ui) {
					cmb_venues.updateTags($(this).closest(cmb2TypeClass+'-sortable'), start_pos, ui.item.index());
				}
			});
			$(cmb2TypeClass+"-sortable .venueschecklist").disableSelection();

			$(cmb2TypeClass+', '+cmb2TypeClass+'-sortable').each(function() {
				cmb_venues.quickClicks(this);
			});

			$('input.button', ajaxtag).click(function() {
				t.flushTags($(this).closest(cmb2TypeClass+', '+cmb2TypeClass+'-sortable'));
			});

			$('input.new', ajaxtag).keyup(function(e) {
				if (13 == e.which) {
					cmb_venues.flushTags($(this).closest(cmb2TypeClass+', '+cmb2TypeClass+'-sortable'));
					return false;
				}
			}).keypress(function(e) {
				if (13 == e.which) {
					e.preventDefault();
					return false;
				}
			});

			// save tags on post save/publish
			/*$('#post').submit(function(){
			$('.cmb-type-tags').each( function() {
	        	cmb_venues.flushTags(this, false, 1);
			});
		});*/

		},

		clean: function(tags) {
			return tags.replace(/\s*,\s*/g, ';').replace(/,+/g, ';').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');
		},

		parseTags: function(el) {
			var id = el.id,
				num = id.split('-check-num-')[1],
				taxbox = $(el).closest(cmb2TypeClass+', '+cmb2TypeClass+'-sortable'),
				thevenues = taxbox.find('textarea'),
				current_venues = thevenues.val().split(';'),
				new_venues = [];
			delete current_venues[num];

			$.each(current_venues, function(key, val) {
				val = $.trim(val);
				if (val) {
					new_venues.push(val);
				}
			});

			thevenues.val(this.clean(new_venues.join(';')));

			this.quickClicks(taxbox);
			return false;
		},

		updateTags: function(el, start_pos, stop_pos) {
			var thevenues = $('textarea', el),
				current_venues, sorted_tags;

			if (!thevenues.length)
				return;

			current_venues = thevenues.val().split(';');

			current_venues.move(start_pos, stop_pos);

			for (var i = 0; i < current_venues.length; i++) {
				if (i == 0) sorted_tags = '';
				else sorted_tags += ';';
				sorted_tags += current_venues[i];
			}

			thevenues.val(sorted_tags);
		},

		quickClicks: function(el) {
			var thevenues = $('textarea', el),
				venueschecklist = $('.venueschecklist', el),
				id = $(el).attr('id'),
				current_venues, disabled;

			if (!thevenues.length)
				return;

			disabled = thevenues.prop('disabled');

			current_venues = thevenues.val().split(';');
			venueschecklist.empty();
			$.each(current_venues, function(key, val) {

				var span, xbutton;

				val = $.trim(val);

				if (!val)
					return;

				// Create a new span, and ensure the text is properly escaped.
				span = $('<span />').text(val);

				// If tags editing isn't disabled, create the X button.
				if (!disabled) {
					xbutton = $('<a id="' + id + '-check-num-' + key + '" class="ntdelbutton">X</a>');
					xbutton.click(function() { cmb_venues.parseTags(this); });
					span.prepend('&nbsp;').prepend(xbutton);
				}

				// Append the span to the tag list.
				venueschecklist.append(span);
			});
		},

		//called on add tag, called on save
		flushTags: function(el, a, f) {
			a = a || false;
			var text, tags = $('textarea', el),
				newtag = $('input.new', el),
				newtags;

			text = a ? $(a).text() : newtag.val();

			tagsval = tags.val();
			newtags = tagsval ? tagsval + ';' + text : text;

			newtags = this.clean(newtags);

			newtags = array_unique_noempty(newtags.split(';')).join(';');

			tags.val(newtags);

			this.quickClicks(el);

			if (!a)
				newtag.val('');
			if ('undefined' == typeof(f))
				newtag.focus();

			return false;
		}

	}

	Array.prototype.move = function(old_index, new_index) {
		if (new_index >= this.length) {
			var k = new_index - this.length;
			while ((k--) + 1) {
				this.push(undefined);
			}
		}
		this.splice(new_index, 0, this.splice(old_index, 1)[0]);
	};
})(jQuery);

jQuery(document).ready(function($) {

	//venue countries selectise
	if ( jQuery('#wyz_event_venue_country').length)
		jQuery('#wyz_event_venue_country').selectize({
			create: false,
			plugins: ['remove_button'],
		});

	//venue tags selectize
	if (jQuery('#wyz-tag-select').length) {

		jQuery('#wyz-tag-select').selectize({
			create: true,
			plugins: ['remove_button']
		});
	}

	//venue categories selectize
	if (jQuery('#wyz-categories-select').length) {

		jQuery('#wyz-categories-select').selectize({
			create: false,
			plugins: ['remove_button']
		});
	}

	var firstPageLoad=true;

	handleVenueOrganizerSelectize('#wyz-veneu-select','venue');
	handleVenueOrganizerSelectize('*[id^="wyz-organier-select-"]','organizer');

	if(jQuery('*[id^="wyz-organier-select-"]').length==1)
		setTimeout(function(){jQuery('button[data-selector="wyz_event_organizers_group_repeat"]').click();
	},500);

	jQuery('button[data-selector="wyz_event_organizers_group_repeat"]').on('click',function(){
		setTimeout(function(){
			handleVenueOrganizerSelectize('*[id^="wyz-organier-select-"]','organizer');

			var ll = jQuery('.wyz-organier-select').length;
			if(ll>1) {
				var cntr =0;
				jQuery('.wyz-organier-select').each(function(){
					cntr++;
					if(cntr == (ll-1)){
						lastOrganizer = jQuery(this).parents('.cmb-repeatable-grouping');
						hideFields('organizer');
						return;
					}
				});
			}
		}, 500);
	});

	var venueFields = ['.cmb2-id-wyz-event-venue-address','.cmb2-id-wyz-event-venue-city','.cmb2-id-wyz-event-venue-country','.cmb2-id-wyz-event-venue-stateprovince',
	'.cmb2-id-wyz-event-venue-postalcode','.cmb2-id-wyz-event-venue-phone','.cmb2-id-wyz-event-venue-website'];
	var organizerFields = ['.cmb-type-text-medium', '.cmb-type-text-url','.cmb-type-text-email'];


	var addedVenues = [];
	var addedOrganizers = [];

	var lastOrganizer = null;
	jQuery('.wyz-organier-select').live('click',function(){
		lastOrganizer = jQuery(this).parents('.cmb-repeatable-grouping');
	});


	function addAdded(value,type,This=null) {
		if(type=='venue') {
			addedVenues.push(value);
		}else if(type=='organizer') {

			addedOrganizers.push(value);
		}
	}

	function showFields(type) {
		if(type=='venue'){
			for (var i = 0; i< venueFields.length ; i++) {
				jQuery( venueFields[i] ).show();
			}
			jQuery('#wyz_event_new_venue_added').val('yes');
		} else {
			for (var i = 0; i< organizerFields.length ; i++) {
				lastOrganizer.find( organizerFields[i] ).show();
			}
			lastOrganizer.find('input[id$="wyz_event_new_organizer_added"]').val('yes');
		}
	}

	function checkIfShow(value,type) {
		var fields = 'venue' == type ? addedVenues : addedOrganizers;
		if(fields.indexOf(value) > -1)
			showFields(type);
		else
			hideFields(type);
	}

	function hideFields(type) {
		if(type=='venue'){
			for (var i = 0; i< venueFields.length ; i++) {
				jQuery( venueFields[i] ).find('input').val('');
				jQuery( venueFields[i] ).hide();
			}
			jQuery('#wyz_event_new_venue_added').val('');
		} else {
			for (var i = 0; i< organizerFields.length ; i++) {
				lastOrganizer.find( organizerFields[i] ).find('input').val('');
				lastOrganizer.find( organizerFields[i] ).hide();
			}
			lastOrganizer.find('input[id$="wyz_event_new_organizer_added"]').val('');
		}
	}


	function handleVenueOrganizerSelectize(id,type){
		//business tags select
		if (jQuery(id).length) {
			var canCreate = 'venue' == type ? eventsInfo.canCreateVenue : eventsInfo.canCreateOrganizer;
			var c =1,l=jQuery(id).length;
			jQuery(id).each(function(){
				if((type=='venue' && c==l)){
					theVenueOrganizerSelectize(jQuery(this),type,canCreate);
				} else if( type=='organizer' ){
					if(firstPageLoad && c < l) {
						theVenueOrganizerSelectize(jQuery(this),type,canCreate);
					} else if(!firstPageLoad && c==l-1) {
						theVenueOrganizerSelectize(jQuery(this),type,canCreate);
					}
				}
				c++;
			});
			if(type=='organizer')
				firstPageLoad = false;
		}
	}

	function theVenueOrganizerSelectize(This,type,canCreate){
		This.selectize({
			create: canCreate,
			plugins: ['remove_button'],
			onOptionAdd: function( value, item){
				addAdded(value,type);
				showFields(type);
			},
			onChange: function( value, item){
				checkIfShow(value,type);
			},
			onItemRemove: function(value, item){
				hideFields(type);
			}
		});
	}



	/*Hide capacity when unlimited*/
	function hideTicketCapacity(This){
		This.parent().parent().prev().hide();
	}

	function showTicketCapacity(This){
		This.parent().parent().prev().show();
	}

	jQuery('input[id$="_ticket_capacity_mode"]:checked').each(function(){
		hideTicketCapacity(jQuery(this));
	});

	jQuery('input[id$="_ticket_capacity_mode"]').live('change',function(){
		if(this.checked){
			hideTicketCapacity(jQuery(this));
		} else{
			showTicketCapacity(jQuery(this));
		}
	});
	/*is(":checked")*/

});
