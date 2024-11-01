(function( $ ) {
	'use strict';

	jQuery(document).ready(function(a) {

	if (jQuery('body').hasClass('toplevel_page_streamweasels')) {

		jQuery(document).on("click", "#sw-check-license-submit", function(a) {
			jQuery("#sw-check-license").val("1");
		});

		jQuery(document).on("click", "#sw-refresh-token-submit", function(a) {
			jQuery("#sw-refresh-token").val("1");
		});

		jQuery(document).on("click", "#sw-delete-log-submit", function(a) {
			jQuery("#sw-delete-log").val("1");
			jQuery("#sw-dismiss-for-good").val("1");
		});

		jQuery("#sw-show-hide-members").on("click", function(e) {
			e.preventDefault();
			jQuery("#sw-team-channels").toggle();
		});

		jQuery("#sw-form").on("submit", function(e) {
			if ($('#sw-channels').val() !== '' && $('#sw-team').val() !== '') {
				e.preventDefault();
				$('.postbox-main-settings .inside .notice').remove()
				var error = '<div class="notice notice-error"><p><strong>Error. You cannot use both the Channels field and the Team field together. Choose only one.</strong></p></div>';
				$('.postbox-main-settings .inside').prepend(error)
				$('html').animate({scrollTop: $(".postbox-main-settings").offset().top}, 1000);
			} else {

			}
		});

		jQuery('#sw-tile-bg-colour').wpColorPicker();
		jQuery('#sw-tile-title-colour').wpColorPicker();
		jQuery('#sw-tile-subtitle-colour').wpColorPicker();
		jQuery('#sw-logo-bg-colour').wpColorPicker();
		jQuery('#sw-logo-border-colour').wpColorPicker();
		jQuery('#sw-hover-colour').wpColorPicker();

		var tileRoundedCorners = document.querySelector('#sw-tile-rounded-corners');
		var tileRoundedCornersVal = tileRoundedCorners.value;
		var tileRoundedCornersInit = new Powerange(tileRoundedCorners, { callback: function() {tileRoundedCorners.nextElementSibling.nextElementSibling.innerHTML = tileRoundedCorners.value+'px'}, step: 5, max: 40, start: tileRoundedCornersVal, hideRange: true });			

		var clipboard = new ClipboardJS('#sw-copy-shortcode');

		clipboard.on('success', function (e) {
			jQuery(e.trigger).addClass('tooltipped');
			jQuery(e.trigger).on('mouseleave', function() {
				jQuery(e.trigger).removeClass('tooltipped');
			})
		  });

		jQuery('#sw-form input, #sw-form select').on('change', function() {
			buildShortcode()
		})

		buildShortcode()
	  
	  function buildShortcode() {

		var game = '';
		var channels = '';
		var team = '';
		var limit = '';
		// var colourTheme = '';
		var language = '';
		var layout = '';
		var plugin = 'sw-twitch';

 		if ($('#sw-game').val() !== '') {
		  var game = ($('#sw-game').val() !== '') ? ' game="'+$('#sw-game').val()+'"' : '';
		}
		if ($('#sw-channels').val() !== '') {
		  var channels = ' channels="'+$('#sw-channels').val()+'"';
		} else if ($('#sw-team').val() !== '') {
		  var team = ' team="'+$('#sw-team').val()+'"';
		}
		if ($('#sw-language').val() !== '') {
			var language = ' language="'+$('#sw-language').val()+'"';
		  } else {
			var language = '';
		  }
		if ($('#sw-limit').val() !== '') {
		  var limit = ' limit="'+$('#sw-limit').val()+'"';
		}
		// if ($('#sw-colour-theme').val() !== '') {
		// 	var limit = ' colour-theme="'+$('#sw-colour-theme').val()+'"';
		// }		
		if ($('#sw-layout').val() !== '') {
			var layout = ' layout="'+$('#sw-layout').val()+'"';
		}																	

		var shortcode = '['+plugin+game+channels+team+language+limit+layout+']';
		$('.postbox-shortcode .advanced-shortcode').html(shortcode)
		}

		$('.upload-btn').click(function(e) {
			e.preventDefault();
			var btn = $(this);
			var image = wp.media({ 
				title: 'Upload Image',
				// mutiple: true if you want to upload multiple files at once
				multiple: false
			}).open()
			.on('select', function(e){
				// This will return the selected image from the Media Uploader, the result is an object
				var uploaded_image = image.state().get('selection').first();
				// We convert uploaded_image to a JSON object to make accessing it easier
				// Output to the console uploaded_image
				console.log(uploaded_image);
				var image_url = uploaded_image.toJSON().url;
				// Let's assign the url value to the input field 
				btn.prev().val(image_url);
			});
		});

	}

	if (jQuery('body').hasClass('twitch-integration_page_streamweasels-wall')) {
		var columnCount = document.querySelector('#sw-tile-column-count');
		var columnCountVal = columnCount.value;
		var columnCountInit = new Powerange(columnCount, { callback: function() {columnCount.nextElementSibling.nextElementSibling.innerHTML = columnCount.value+' columns'}, step: 1, max: 6, start: columnCountVal, hideRange: true });		

		var columnSpacing = document.querySelector('#sw-tile-column-spacing');
		var columnSpacingVal = columnSpacing.value;
		var columnSpacingInit = new Powerange(columnSpacing, { callback: function() {columnSpacing.nextElementSibling.nextElementSibling.innerHTML = columnSpacing.value+'px'}, step: 5, max: 100, start: columnSpacingVal, hideRange: true });
		  
	}

	if (jQuery('body').hasClass('twitch-integration_page_streamweasels-vods')) {
		var columnCount = document.querySelector('#sw-tile-column-count');
		var columnCountVal = columnCount.value;
		var columnCountInit = new Powerange(columnCount, { callback: function() {columnCount.nextElementSibling.nextElementSibling.innerHTML = columnCount.value+' columns'}, step: 1, max: 6, start: columnCountVal, hideRange: true });		

		var columnSpacing = document.querySelector('#sw-tile-column-spacing');
		var columnSpacingVal = columnSpacing.value;
		var columnSpacingInit = new Powerange(columnSpacing, { callback: function() {columnSpacing.nextElementSibling.nextElementSibling.innerHTML = columnSpacing.value+'px'}, step: 5, max: 100, start: columnSpacingVal, hideRange: true });
		  
	}        

	if (jQuery('body').hasClass('twitch-integration_page_streamweasels-rail')) {

		  jQuery('#sw-rail-border-colour').wpColorPicker();
		  jQuery('#sw-rail-controls-bg-colour').wpColorPicker();
		  jQuery('#sw-rail-controls-arrow-colour').wpColorPicker();
		  
	}  
	
	if (jQuery('body').hasClass('twitch-integration_page_streamweasels-player')) {

		jQuery('#sw-welcome-bg-colour').wpColorPicker();
		jQuery('#sw-welcome-text-colour').wpColorPicker();

		$('.upload-btn').click(function(e) {
			e.preventDefault();
			var btn = $(this);
			var image = wp.media({ 
				title: 'Upload Image',
				// mutiple: true if you want to upload multiple files at once
				multiple: false
			}).open()
			.on('select', function(e){
				// This will return the selected image from the Media Uploader, the result is an object
				var uploaded_image = image.state().get('selection').first();
				// We convert uploaded_image to a JSON object to make accessing it easier
				// Output to the console uploaded_image
				console.log(uploaded_image);
				var image_url = uploaded_image.toJSON().url;
				// Let's assign the url value to the input field 
				btn.prev().val(image_url);
			});
		});			
		  
	} 
	
	if (jQuery('body').hasClass('twitch-integration_page_streamweasels-status')) {

		  jQuery('#sw-accent-colour').wpColorPicker();
		  jQuery('#sw-logo-background-colour').wpColorPicker();
		  jQuery('#sw-carousel-background-colour').wpColorPicker();
		  jQuery('#sw-carousel-arrow-colour').wpColorPicker();

		  $('.upload-btn').click(function(e) {
			e.preventDefault();
			var btn = $(this);
			var image = wp.media({ 
				title: 'Upload Image',
				// mutiple: true if you want to upload multiple files at once
				multiple: false
			}).open()
			.on('select', function(e){
				// This will return the selected image from the Media Uploader, the result is an object
				var uploaded_image = image.state().get('selection').first();
				// We convert uploaded_image to a JSON object to make accessing it easier
				// Output to the console uploaded_image
				console.log(uploaded_image);
				var image_url = uploaded_image.toJSON().url;
				// Let's assign the url value to the input field 
				btn.prev().val(image_url);
			});
		});			  
		  
	} 	

	$(document).on('click', '.swti-notice button.notice-dismiss', function() {
		var data = {
			action : 'swti_admin_notice_dismiss',
		};

		$.post(ajaxurl, data, function (response) {
			console.log(response, 'DONE!');
		});
	});	
	
	$(document).on('click', '.swti-notice a.dismiss-for-good', function() {
		$('.swti-notice').hide()
		var data = {
			action : 'swti_admin_notice_dismiss_for_good',
		};
		$.post(ajaxurl, data, function (response) {
			console.log(response, 'SWTI - Notice Closed');
		});
	});	

});

})( jQuery );
