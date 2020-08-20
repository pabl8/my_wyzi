jQuery( document ).ready(function() {
	//jQuery('#ns-wp-editor-div').append(jQuery('#wp-ns-editor-add-prod-short-desc-wrap'));
	
	/*PRODUCT DATA*/
	jQuery("li").on('click', function () {
		jQuery(".ns-prod-data-tab").addClass("ns-hidden");
		jQuery("li").removeClass("ns-active");
		jQuery("." + jQuery(this).attr("id")).removeClass("ns-hidden");
		jQuery(this).addClass("ns-active");	
	});
	
	jQuery("#ns-manage-stock").on('click', function () {
		if(jQuery("#ns-manage-stock").val() == "no"){
			jQuery('#ns-manage-stock-div').css('display','block');
			jQuery("#ns-manage-stock").val("yes");
		}
		else{
			jQuery('#ns-manage-stock-div').css('display','none');
			jQuery("#ns-manage-stock").val("no");
		}
	});

	jQuery('#attch-rmv').live('click',function(e){
    	e.preventDefault();
    	jQuery("#ns-image-from-thumb").val('');
    	jQuery('#attch-name').html('');
        jQuery('.attch-meta').hide();
    });
	
	//attributes
	var i = 0;
	if ( '' != jQuery('#ns-attribute-list').val())
		i = parseInt(jQuery('#ns-attribute-list').val());
	jQuery('#ns-add-attribute-btn').on('click', addAttrs);

	function addAttrs(event) {

		var attrVal = jQuery('#ns-attribute-taxonomy').val();
		if(attrVal == 'ns-cus-prod-att'){
			jQuery('#ns-inner-attributes').after('<div><h3><label>Custom product attribute</label></h3><div><label>Name:</label><br><input class="ns-input-width" name="ns-attr-names'+i+'" id="ns-attr-names'+i+'" type="text"/></div><div><label>Value(s)</label><textarea name="ns-attribute-values'+i+'"placeholder="Enter some text, or some attributes by &quot;|&quot; separating values."></textarea></div><div><label>Visible on product page </label><input class="checkbox" name="ns-attr-visibility-status'+i+'" id="ns-attr-visibility-status'+i+'" checked="checked" type="checkbox"/></div><button id="ns-attribute-btn-remove" type="button" class="button" style="float:left">Remove</button></div>');
			i++;
		}
		else{

			jQuery('#ns-add-attribute-btn').unbind('click');
			jQuery('#ns-add-attribute-btn').css({'opacity':'0.4'});
			name = jQuery('#ns-attribute-taxonomy').find('option:selected').text();
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: "action=wyz_prod_attr_fetch&nonce=" + ajaxnonce + "&attr=" + attrVal+"&p_id=",
				success: function(a) {
					if(!a.length)return;
					a = JSON.parse(a);
					var appnd = '';
					jQuery('#ns-inner-attributes').after(appendAttr(name,attrVal,a));
					jQuery('#ns-attribute-taxonomy').find('option[value="'+attrVal+'"]').hide();
					jQuery('#ns-attribute-taxonomy').val('ns-cus-prod-att');

					jQuery('.ns-prod-data-tab.ns-attributes .wyz-product-attr .attribute_values').each(function(){
						jQuery(this).selectator({
							useSearch: false,
							useDimmer: false
						});
					});
					jQuery('#ns-add-attribute-btn').on('click', addAttrs);
					jQuery('#ns-add-attribute-btn').css({'opacity':'1'});
					i++;
				},
				error: function(){
					jQuery('#ns-add-attribute-btn').on('click', addAttrs);
					jQuery('#ns-add-attribute-btn').css({'opacity':'1'});
				}
			});
		}
		jQuery('#ns-attribute-list').val(i);
		
	}
	
	//removing attribute
	jQuery(document).on('click', '#ns-attribute-btn-remove, #ns-attribute-btn-remove-col', function(event){
		if(jQuery(this).parent().hasClass('ns-color-attr-class')){
			jQuery('#ns-color-id').prop('disabled', false);
		}
		jQuery(this).parent().remove();
		if(jQuery(this).attr('id') == 'ns-attribute-btn-remove')	// check if theres a need to decrement the counter -- only in case im removing a custom attributes --
			i--;
		jQuery('#ns-attribute-list').val(i);
	});
	
	//saving into hidden input selectable color
	jQuery(document).on('click', '.checkbox-attr-selectable-color', function(event){
		if(jQuery(this).is(':checked')){
			jQuery('#ns-attr-from-list').val(jQuery('#ns-attr-from-list').val()+jQuery(this).attr('name')+',');
		}
		else{
			var new_string = "";
		    new_string = jQuery('#ns-attr-from-list').val();
			new_string = new_string.replace(jQuery(this).attr('name')+',', "");
			jQuery('#ns-attr-from-list').val(new_string);
		}		
		
	});
	
		
	/*PRODUCT IMAGE*/
	/*This is used to create a temporary url (objectURL) to update the thumbnail image after user insert one*/
	/*jQuery('#ns-thumbnail').change( function(event) {
		jQuery("#ns-img-thumbnail").fadeIn("fast").attr('src',URL.createObjectURL(event.target.files[0]));
	});*/
	
	/*GALLERY AND MODAL*/
	/* When the user clicks on the button, open the gallery modal*/
	/*jQuery("#ns-myBtn").on('click', function() {
		jQuery('#ns-myModal').css("display","block");
	});*/

	/* When the user clicks on (x), close the gallery modal*/
	jQuery(".ns-close").on('click', function() {
		jQuery('#ns-myModal').css("display","none");
	});

	/*Used to get the selected image from gallery list*/
	/*var img_array = [];		//this array will contains all the SELECTED images 
	
	jQuery('.ns-image-container img').on('click', function(){
		alert('click 1');
		//Image clicked for the first time
		if(img_array.indexOf(jQuery(this).attr("id")) < 0){
			img_array.push(jQuery(this).attr("id"));
			//setting the value of the input with the urls of images separated by comma
			jQuery('#ns-image-from-list').val(img_array.toString());
			//jQuery('#ns-image-from-list').val( jQuery(this).attr("src") );
			jQuery(this).css('border','5px solid #bdcfed');
		}
		else{
			//Image already being clicked. Removing border and delete element from img_array
			jQuery(this).css('border', '1px solid gray');
			var elementToRemove = jQuery(this).attr("id");
			img_array = jQuery.grep(img_array, function(value) {
			  return value != elementToRemove;
			});
			jQuery('#ns-image-from-list').val(img_array.toString());
		}
			
	});*/

	jQuery("#gallery-upload-button").click(function(a) {
		var uploaded_images = "";
        a.preventDefault(), i = wp.media({
            title: "Upload Gallery",
            multiple: 'toggle',
        }), i.on("open", function() {
            var a = i.state().get("selection"),
                b = jQuery("#ns-image-from-list").val();
            if ("" !== b) {
                var c = wp.media.attachment(b);
                c.fetch(), a.add(c ? [c] : [])
            }
        }), i.on("select", function() {
            var a = i.state().get("selection");
            a.map(function(a) {
                a = a.toJSON();
                var v = jQuery("#ns-image-from-list").val();
                uploaded_images += (uploaded_images.length?',':'')+a.id;
                jQuery("#ns-image-from-list").val(uploaded_images);
            })
        }), i.open()
    });


    jQuery("#thumb-upload-button").click(function(a) {
        a.preventDefault(), i = wp.media({
            title: "Upload Image",
            multiple: false,
        }), i.on("open", function() {
            var a = i.state().get("selection"),
                b = jQuery("#ns-image-from-thumb").val();
            if ("" !== b) {
                var c = wp.media.attachment(b);
                c.fetch(), a.add(c ? [c] : [])
            }
        }), i.on("select", function() {
            var a = i.state().get("selection");
            a.map(function(a) {
                a = a.toJSON();
                jQuery("#ns-image-from-thumb").val(a.id);
                jQuery('#attch-name').html(a.title);
                jQuery('.attch-meta').show();
            })
        }), i.open()
    });

    
	
	/*This one is used to upload into the gallery the image from local path
	jQuery('#ns-image-from-file').change( function(event) {
		jQuery('#ns-image-from-file').attr('src',URL.createObjectURL(event.target.files[0]));
	});*/
	
	
	/*HIDE SHOW DIVS*/
	//product data
	jQuery('#ns-post-prod-data-hide-show').on('click', function(event) {   
	         
			 if(jQuery( '#ns-product-data-inner-container' ).is(':hidden')){ 
				jQuery('#ns-post-prod-data-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-product-data-inner-container').css("display", "block");
			 }
			 else {
				jQuery('#ns-post-prod-data-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				jQuery('#ns-product-data-inner-container').css("display", "none");
				}
			
	});
	
	//short description
	jQuery('#ns-short-desc-hide-show').on('click', function(event) {            
			 if(jQuery( '#ns-wp-editor-div' ).is( ':hidden' )){
				jQuery('#ns-short-desc-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-wp-editor-div').css("display", "block");
			 }
			 else {
				jQuery('#ns-short-desc-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				jQuery('#ns-wp-editor-div').css("display", "none");
			}
			
	});
	
	//post content
	jQuery('#ns-post-content-hide-show').on('click', function(event) {        
             if(jQuery( '#ns-wp-post-content-div' ).is( ':hidden' )){
				jQuery('#ns-post-content-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-wp-post-content-div').css("display", "block");
			 }
			 else {
				jQuery('#ns-post-content-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				jQuery('#ns-wp-post-content-div').css("display", "none");
			}
			
    });
	
	//tags
	jQuery('#ns-prod-tags-hide-show').on('click', function(event) {        
			 if(jQuery( '#ns-prod-tags-div' ).is( ':hidden' )){
				jQuery('#ns-product-tags').css('height', 'auto');
				jQuery('#ns-prod-tags-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');	
				jQuery('#ns-prod-tags-div').css("display", "block");	
			 } else {
				 jQuery('#ns-product-tags').css('height', '100%');
				 jQuery('#ns-prod-tags-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				 jQuery('#ns-prod-tags-div').css("display", "none");
			 }
    });
	
	//add image
	jQuery('#ns-prod-image-hide-show').on('click', function(event) {        
			 if(jQuery( '#ns-image-container-0' ).is( ':hidden' )){
				jQuery('#ns-image-container').css('height', 'auto');	
				jQuery('#ns-prod-image-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-image-container-0').css("display", "block");
			 } else {
			 	 jQuery('#ns-image-container').css('height', '100%');
				 jQuery('#ns-prod-image-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				 jQuery('#ns-image-container-0').css("display", "none");
			 }
    });
	
	//categories
	jQuery('#ns-prod-categories-hide-show').on('click', function(event) {        
			 if(jQuery( '#ns-prod-cat-inner' ).is( ':hidden' )){
				jQuery('#ns-product-categories').css('height', 'auto');
				jQuery('#ns-prod-categories-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-prod-cat-inner').css("display", "block");
			 } else {
			 	 jQuery('#ns-product-categories').css('height', '100%');
				 jQuery('#ns-prod-categories-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				 jQuery('#ns-prod-cat-inner').css("display", "none");
			 }
			
    });
	
	//gallery
	jQuery('#ns-prod-gallery-hide-show').on('click', function(event) {        
			 if(jQuery( '#ns-prod-gallery-inner' ).is( ':hidden' )){
				jQuery('#ns-product-gallery').css('height', 'auto');
				jQuery('#ns-prod-gallery-hide-show').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
				jQuery('#ns-prod-gallery-inner').css("display", "block");
			 } else {
			 	jQuery('#ns-product-gallery').css('height', '100%');
				jQuery('#ns-prod-gallery-hide-show').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
				jQuery('#ns-prod-gallery-inner').css("display", "none");
			 }
    });

	


	jQuery('.attribute-remove').live('click',function(){
		var attrVal = jQuery(this).data('attr');
		jQuery('#ns-attribute-taxonomy').find('option[value="'+attrVal+'"]').show();
		jQuery(this).parent().remove();
	});

	function appendAttr(name,value, terms){
		var ret = '<div class="wyz-product-attr"><h3><label>'+name+' Attribute</label></h3><div><label>Value(s):</label><br><input type="hidden" value="'+value+'" name="attribute_values_tax['+i+']"/>'+
					'<select multiple="" data-placeholder="Select terms" class="multiselect attribute_values wc-enhanced-select enhanced" name="attribute_values['+i+'][]" tabindex="-1" aria-hidden="true">';
		terms.forEach(function(o){
			ret += '<option value="'+o.id+'">'+o.name+'</option>';
		});
		ret += '</select></div><button class="attribute-remove" data-attr="'+value+'" type="button" class="button">Remove</button></div>';

		return ret;
	}
	
	
});