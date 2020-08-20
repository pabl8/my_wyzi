
if('yes'==TandCEnf){
	toastr.options.closeMethod = 'fadeOut';
	toastr.options.showEasing = 'swing';
	toastr.options.hideEasing = 'swing';
	toastr.options.closeDuration = 300;
	toastr.options.preventDuplicates = true;
	toastr.options.timeOut = 0;





	if(jQuery('.terms-and-cond input[type="checkbox"]').length ){
		jQuery('.login-reg-tab-list li:first-child').click(function(){ // case login is clicked in Template 2
			jQuery('.social-login.facebook ').addClass('js-fbl');
		});
		jQuery('.login-reg-tab-list li:nth-child(2)').click(function(){ // case register is clicked in Template 2
			jQuery('.social-login.facebook ').removeClass('js-fbl');	
		});

		jQuery('.terms-and-cond input[type="checkbox"]').on('change',function(){ 
			if(jQuery(this).prop('checked')==true){
				jQuery('.social-login.facebook ').addClass('js-fbl');
			}else {
				jQuery('.social-login.facebook ').removeClass('js-fbl');
			}
		});
		jQuery('.terms-and-cond input[type="checkbox"]').trigger('change');

	}


	jQuery('.social-login').click(function(e){
		if(jQuery('.terms-and-cond input[type="checkbox"]').length && jQuery('.terms-and-cond input[type="checkbox"]').prop("checked") != true){
			if (jQuery('.login-reg-tab-list').length && jQuery('.login-reg-tab-list li:first-child').attr('class') == 'active') { // check if template 2 and Login is selected
				jQuery('.social-login.facebook ').addClass('js-fbl');
				return;
			} 
			e.preventDefault();
			jQuery('.terms-and-cond input[type="checkbox"]').focus();
			toastr.warning('<p>'+TandCText+'</p>');
			jQuery('.terms-and-cond input[type="checkbox"]').animate({
				width: '20px',
				height: '20px',
			}, 400, 'swing', function() {
				jQuery('.terms-and-cond input[type="checkbox"]').animate({
					width: '12.5px',
					height: '12.5px',
				}, 400, 'swing');
			});
		}
	});

	if ( typeof regVar !== 'undefined' //undefined !== regVar
		&& regVar.justVer)toastr.success('<p>'+regVar.emlCnf+'</p>');
}