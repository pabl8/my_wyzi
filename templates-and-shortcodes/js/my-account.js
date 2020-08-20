"use strict";

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

var passOK = false;
function checkPasswordStrength($pass1,
	$pass2,
	$strengthResult,
	$submitButton,
	blacklistArray) {
	var pass1 = $pass1.val();
	var pass2 = $pass2.val();

	// Reset the form & meter
	$strengthResult.removeClass('short bad good strong');

	// Extend our blacklist array with those from the inputs & site data
	blacklistArray = blacklistArray.concat(wp.passwordStrength.userInputBlacklist());

	// Get the password strength
	var strength = wp.passwordStrength.meter(pass1, blacklistArray, pass2);

	// Add the strength meter results
	switch (strength) {

		case 2:
			$strengthResult.addClass('bad').html(pwsL10n.bad);
			break;

		case 3:
			$strengthResult.addClass('good').html(pwsL10n.good);
			break;

		case 4:
			$strengthResult.addClass('strong').html(pwsL10n.strong);
			break;

		case 5:
			$strengthResult.addClass('short').html(pwsL10n.mismatch);
			break;

		default:
			$strengthResult.addClass('short').html(pwsL10n.short);

	}

	// The meter function returns a result even if pass2 is empty,
	// enable only the submit button if the password is strong and
	// both passwords are filled up
	if (4 === strength && ( undefined == pass2 || '' !== pass2.trim() ) ) {
		passOK = true;
	} else {
		passOK = false;
	}

	return strength;
}

function isEmptyPass() {
	return ('' === jQuery('input[name=wyz_user_pass]').val() && '' === jQuery('input[name=wyz_user_pass_confirm]').val());
}

function enableDisableSubmit(userFirst, userLast, userEmail, submitBtn) {
	if ('' !== userEmail.val() && '' !== userFirst.val() && '' !== userLast.val() && (isEmptyPass() || passOK)) {
		submitBtn.removeAttr('disabled');
		submitBtn.prop('disabled', false);
	} else {
		submitBtn.attr('disabled', 'disabled');
		submitBtn.prop('disabled', false);
	}
}


jQuery(document).ready(function() {

	if (jQuery('.user-acc-tabs').length) {

		jQuery('.user-acc-tabs .booking').on('click',function(){
			jQuery(window).trigger('resize');
		});

		var t = jQuery('.page-content').height() + 50;
		var h;

		if (jQuery('#tab1').is(':checked')) {
			h = t + jQuery('#tab-content1').height();
			jQuery('.page-content').height(h);
		} else if (jQuery('#tab2').is(':checked')) {
			h = t + jQuery('#tab-content2').height();
			jQuery('.page-content').height(h);
		}

		jQuery('#tab1').click(function() {
			h = t + jQuery('#tab-content1').height();
			jQuery('.page-content').height(h);
			jQuery('#lbl1').addClass('wyz-tab-current');
			jQuery('#lbl2').removeClass('wyz-tab-current');
		});
		jQuery('#tab2').click(function() {
			h = t + jQuery('#tab-content2').height();
			jQuery('.page-content').height(h);
			jQuery('#lbl2').addClass('wyz-tab-current');
			jQuery('#lbl1').removeClass('wyz-tab-current');
		});

		
	}


	var previousActive;

	jQuery(function(){
		var hash = window.location.hash;
		hash && jQuery('.profile-tab-list a.profile-tab[href="' + hash + '"]').tab('show');

		if(hash)previousActive = jQuery('.profile-tab-list a.profile-tab[href="' + hash + '"]');
		else if(myAccount.isWoocommerceEndpoint)previousActive = jQuery('.profile-tab-list a.profile-tab[href="#woo-profile"]');
		else previousActive=jQuery('.profile-tab-list ul>li:first-child');

		previousActive.addClass('wyz-prim-color');

		//search for query arguments
		var url = window.location.search;
		if( (null != getParameterByName("add-job",url) && jQuery('#link-jobs').length ) ||
			('edit' == getParameterByName("action",url) && null!= getParameterByName("job_id",url) ) ) {
			previousActive.removeClass('wyz-prim-color');
			previousActive=jQuery('#link-jobs');
			previousActive.addClass('wyz-prim-color');
			previousActive.tab('show');
		}


		jQuery('.profile-tab-list a.profile-tab').click(function (e) {
			e.preventDefault();
			jQuery(this).addClass('wyz-prim-color');
			handle_tab_click(jQuery(this),this.hash);
			previousActive.removeClass('wyz-prim-color');
			previousActive=jQuery(this);
			jQuery(this).addClass('wyz-prim-color');
		});
	});

	function handle_tab_click(This,newHash){
		var hash = window.location.hash;
		previousActive.removeClass('wyz-prim-color');
		previousActive=This;
		This.tab('show');
		var scrollmem = jQuery('body').scrollTop() || jQuery('html').scrollTop();
		window.location.hash = newHash;
		jQuery('html,body').scrollTop(scrollmem);
	}

	var profileDropdown = jQuery('#profile-tab-list-dropdown');
	if (jQuery('.profile-tab-list').length) {
		jQuery('.profile-tab-list a').click(function(){
			profileDropdown.val(jQuery(this).data('link'));
		});

		profileDropdown.on('change',function(){
			jQuery('#link-'+jQuery(this).val()).trigger('click');
			handle_tab_click(jQuery('#link-'+jQuery(this).val()),jQuery(this).val());
		});
	}


	jQuery('body').on('keyup', 'input[name=wyz_user_pass], input[name=wyz_user_pass_confirm]',
		function(event) {
			enableDisableSubmit(
				jQuery('input[name=first-name]'),
				jQuery('input[name=last-name]'),
				jQuery('input[name=email]'),
				jQuery('#update-user')
			);

			checkPasswordStrength(
				jQuery('input[name=wyz_user_pass]'), // First password field
				jQuery('input[name=wyz_user_pass_confirm]'), // Second password field
				jQuery('#password-strength'), // Strength meter
				jQuery('#update-user'), // Submit button
				['black', 'listed', 'word'] // Blacklisted words
			);
		});

	if(jQuery('#amount-notif').length){
		jQuery('#transfer-points').val(parseInt(jQuery('#transfer-points').val())+parseInt(jQuery('#points-fee').html()));
		jQuery('body').on('keyup', '#transfer-points',function(){
			var points = parseInt(jQuery(this).val());
			if(points<0||isNaN(points)){
				jQuery('#amount-notif').html(myAccount.invalidText);
			} else{
				var cost = parseInt(jQuery('#points-fee').html());
				points += cost;
				if(myAccount.pointsAvailable < points){
					jQuery('#amount-notif').html('<span style="color:red;">'+points+' ' + myAccount.exceeds +'</span>');
				} else{
					jQuery('#amount-notif').html('<b>'+points+'</b> ' + myAccount.reduce + '.');
				}
			}
		});
	}

	toastr.options.closeMethod = 'fadeOut';
	toastr.options.showEasing = 'swing';
	toastr.options.hideEasing = 'swing';
	toastr.options.closeDuration = 300;
	toastr.options.preventDuplicates = true;
	toastr.options.timeOut = 0;

	jQuery('#logout-btn').click(function(e){
		e.preventDefault();
		toastr.warning('<p>'+myAccount.logoutText+'</p><div><a id="yeslogout">'+myAccount.logout+'</a><a id="nologout" style="float: right;">'+myAccount.cancel+'</a></div>');
	});

	if(myAccount.justVerified=='yes')
		toastr.success('<p>'+myAccount.verifiedText+'</p>');

	jQuery('#yeslogout').live('click',function(e){
		e.preventDefault();
		window.location.href = jQuery('#logout-btn').attr('href');
	});

	jQuery('.profile-tab-list li.favorite').click(refreshFavorites);
	function refreshFavorites (){
		jQuery(this).unbind('click',refreshFavorites);
		jQuery(window).trigger('resize')
	}

	jQuery('body').on('keyup', 'input[name=first-name], input[name=last-name], input[name=email]',
		function() {
			enableDisableSubmit(
				jQuery('input[name=first-name]'),
				jQuery('input[name=last-name]'),
				jQuery('input[name=email]'),
				jQuery('#update-user')
			);
		}
	);

	//open close all day tabs
	if(jQuery('.open_close_status').length) {
		jQuery('.open_close_status').each(function(){
			var This = jQuery(this);
			var Rad = jQuery(this).find('input[type="radio"]');
			Rad.on('change',function(){
				switch(jQuery(this).val()){
					case 'open_all_day':
						This.siblings('.cmb-row.cmb-repeatable-grouping').hide();
						This.siblings('.closed_all_day-info').hide();
						This.siblings('.cmb-row').find('.cmb-td .cmb-add-row').closest('.cmb-row').hide();
						This.siblings('.open_all_day-info').fadeIn(200);
					break;
					case 'closed_all_day':
						This.siblings('.cmb-row.cmb-repeatable-grouping').hide();
						This.siblings('.open_all_day-info').hide();
						This.siblings('.cmb-row').find('.cmb-td .cmb-add-row').closest('.cmb-row').hide();
						This.siblings('.closed_all_day-info').fadeIn(200);
					break;
					case 'custom':
						This.siblings('.open_all_day-info').hide();
						This.siblings('.closed_all_day-info').hide();
						This.siblings('.cmb-row').find('.cmb-td .cmb-add-row').closest('.cmb-row').show();
						This.siblings('.cmb-row.cmb-repeatable-grouping').fadeIn(200);
					break;
				}
			});

		});

		jQuery('.open_close_status').find('input[type="radio"]:checked').trigger('change');
	}

	var deleteTost;
	jQuery('#delete-user').click(deleteUser);

	function deleteUser(e){
		e.preventDefault();
		jQuery('#delete-user').unbind('click',deleteUser);
		toastr.options.preventDuplicates = true;
		toastr.options.timeOut = 0;
		toastr.options = {
			"debug": false,
			"newestOnTop": false,
			"progressBar": false,
			"positionClass": "toast-top-center",
			"preventDuplicates": false,
			"onclick": null,
			"timeOut": 0,
			"extendedTimeOut": 0,
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut",
			"tapToDismiss": false,
		}
		deleteTost = toastr.warning('<p>'+myAccount.deleteText+'</p><div><input type="password" style="color:#000;" name="delete-pass" id="delete-pass"/></div><div><a id="yesdelete">'+myAccount.delete+'</a><a href="#" id="nodelete" style="float: right;">'+myAccount.cancel+'</a></div>');
	}

	jQuery('#nodelete').live('click', function(e){e.preventDefault();
	jQuery('#delete-user').click(deleteUser);toastr.remove();});


	jQuery('#yesdelete').live('click', function(){
		var pass = jQuery('#delete-pass').val();
		toastr.remove();
		toastr.info('...');
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: "action=wyz_delete_user&nonce=" + jQuery('#delete-user').data('nonce') + "&password=" + pass,
			success: function(res) {
				toastr.clear();
				if(1==res)
					window.location.href = myAccount.deleteReload;
				else {
					toastr.options = {
						"closeButton": true,
						"tapToDismiss": true
					}
					toastr.info(myAccount.deleteError);
				}
			}
		});
	});


	jQuery( '#export-user' ).click( wyzToExportUser );


	jQuery('#noexport').live('click', function(e){e.preventDefault();
	jQuery( '#export-user' ).click( wyzToExportUser );toastr.remove();});

	function wyzToExportUser(e) {
		e.preventDefault();
		jQuery( '#export-user' ).unbind( 'click',wyzToExportUser );
		toastr.options.preventDuplicates = true;
		toastr.options.timeOut = 0;
		toastr.options = {
			"debug": false,
			"newestOnTop": false,
			"progressBar": false,
			"positionClass": "toast-top-center",
			"preventDuplicates": false,
			"onclick": null,
			"timeOut": 0,
			"extendedTimeOut": 0,
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut",
			"tapToDismiss": false,
		}
		toastr.info('<p>'+myAccount.exportText+'</p><div><input type="password" style="color:#000;" name="export-pass" id="export-pass"/></div><div><a id="yesexport">'+myAccount.export+'</a><a href="#" id="noexport" style="float: right;">'+myAccount.cancel+'</a></div>');
	}

	jQuery('#yesexport').live('click',wyzUserExport);
	function wyzUserExport( event ) {

		var pass = jQuery('#export-pass').val();
		toastr.remove();
		toastr.info('...');

		var $this          = jQuery( this ),
			exportersCount = myAccount.exportersCount,
			sendAsEmail    = false,
			nonce			= jQuery('#export-user').data('nonce');

		event.preventDefault();



		jQuery.ajax(
			{
				url: ajaxurl,
				data: {
					action: 'wyz_personal_data_export',
					email: myAccount.userEmail,
					nonce: nonce,
					pass: pass,
				},
				method: 'post'
			}
		).done( function( response ) {
			var responseData = response.data;

			if ( ! response.success ) {

				// e.g. invalid request ID
				toastr.warning(myAccount.deleteError+" "+response.data);
				return;
			}
			else {
				toastr.success(responseData);
				return;
			}
		}).fail( function( jqxhr, textStatus, error ) {
			toastr.warning(myAccount.deleteError+" "+error.data);
		});
/*

		function onExportDoneSuccess( zipUrl ) {
			if ( 'undefined' !== typeof zipUrl ) {
				window.location = zipUrl;
			} else if ( ! sendAsEmail ) {
				alert('Error, no export file!');
			}
			toastr.remove();
		}


		function doNextExport( exporterIndex, pageIndex ) {
			jQuery.ajax(
				{
					url: ajaxurl,
					data: {
						action: 'wyz_export_personal_data',
						exporter: exporterIndex,
						page: pageIndex,
						email: myAccount.userEmail,
						nonce: nonce,
						pass: pass,
					},
					method: 'post'
				}
			).done( function( response ) {
				var responseData = response.data;

				if ( ! response.success ) {

					// e.g. invalid request ID
				toastr.warning(myAccount.deleteError+" "+error);
					return;
				}

				if ( ! responseData.done ) {
					setTimeout( doNextExport( exporterIndex, pageIndex + 1 ) );
				} else {
					if ( exporterIndex < exportersCount ) {
						setTimeout( doNextExport( exporterIndex + 1, 1 ) );
					} else {
						onExportDoneSuccess( responseData.url );
					}
				}
			}).fail( function( jqxhr, textStatus, error ) {
				toastr.warning(myAccount.deleteError+" "+error);
			});
		}

		// And now, let's begin
		doNextExport( 1, 1 );
	}*/
}
});