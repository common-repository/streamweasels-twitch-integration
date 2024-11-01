(function($) {
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
                    $('html').animate({
                        scrollTop: $(".postbox-main-settings").offset().top
                    }, 1000);
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
            var tileRoundedCornersInit = new Powerange(tileRoundedCorners, {
                callback: function() {
                    tileRoundedCorners.nextElementSibling.nextElementSibling.innerHTML = tileRoundedCorners.value + 'px'
                },
                step: 5,
                max: 40,
                start: tileRoundedCornersVal,
                hideRange: true
            });

            var clipboard = new ClipboardJS('#sw-copy-shortcode');

            clipboard.on('success', function(e) {
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
                var language = '';
                var layout = '';
                var embed = '';
                var embedTheme = '';
                var embedChat = '';
                var embedTitle = '';
                var embedMuted = '';
                var showOffline = '';
                var showOfflineText = '';
                var showOfflineImage = '';
                var autoplay = '';
                var featuredStream = '';
                var title = '';
                var subtitle = '';
                var offlineImage = '';
                var logoImage = '';
                var profileImage = '';
                var logoBgColour = '';
                var logoBorderColour = '';
                var maxWidth = '';
                var tileLayout = '';
                var tileSorting = '';
                var liveInfo = '';
                var tileBgColour = '';
                var tileTitleColour = '';
                var tileSubtitleColour = '';
                var tileRoundedCorners = '';
                var hoverEffect = '';
                var hoverColour = '';
                var plugin = 'sw-twitch';

                if ($('#sw-game').val() !== '') {
                    var game = ($('#sw-game').val() !== '') ? ' game="' + $('#sw-game').val() + '"' : '';
                }
                if ($('#sw-channels').val() !== '') {
                    var channels = ' channels="' + $('#sw-channels').val() + '"';
                } else if ($('#sw-team').val() !== '') {
                    var team = ' team="' + $('#sw-team').val() + '"';
                }
                if ($('#sw-language').val() !== '') {
                    var language = ' language="' + $('#sw-language').val() + '"';
                } else {
                    var language = '';
                }
                if ($('#sw-limit').val() !== '') {
                    var limit = ' limit="' + $('#sw-limit').val() + '"';
                }
                if ($('#sw-layout').val() !== '') {
                    var layout = ' layout="' + $('#sw-layout').val() + '"';
                }
                if ($('#sw-embed').val() !== '') {
                    var embed = ' embed="' + $('#sw-embed').val() + '"';
                }
                if ($('#sw-embed-theme').val() !== '') {
                    var embedTheme = ' embed-theme="' + $('#sw-embed-theme').val() + '"';
                }

                var embedChat = ($('#sw-embed-chat').prop('checked') ? ' embed-chat="1"' : ' embed-chat="0"');
                var embedTitle = ($('#sw-embed-title').prop('checked') ? ' embed-title="1"' : ' embed-title="0"');
                var embedMuted = ($('#sw-embed-muted').prop('checked') ? ' embed-muted="1"' : ' embed-muted="0"');

                var showOffline = ($('#sw-show-offline').prop('checked') ? ' show-offline="1"' : ' show-offline="0"');
                var autoplay = ($('#sw-autoplay').prop('checked') ? ' autoplay="1"' : ' autoplay="0"');
                var profileImage = ($('#sw-profile-image').prop('checked') ? ' profile-image="1"' : ' profile-image="0"');

                if ($('#sw-show-offline-text').val() !== '') {
                    var showOfflineText = ' show-offline-text="' + $('#sw-show-offline-text').val() + '"';
                }
                if ($('#sw-show-offline-image').val() !== '') {
                    var showOfflineImage = ' show-offline-image="' + $('#sw-show-offline-image').val() + '"';
                }
                if ($('#sw-featured-stream').val() !== '') {
                    var featuredStream = ' featured-stream="' + $('#sw-featured-stream').val() + '"';
                }
                if ($('#sw-title').val() !== '') {
                    var title = ' title="' + $('#sw-title').val() + '"';
                }
                if ($('#sw-subtitle').val() !== '') {
                    var subtitle = ' subtitle="' + $('#sw-subtitle').val() + '"';
                }
                if ($('#sw-offline-image').val() !== '') {
                    var offlineImage = ' offline-image="' + $('#sw-offline-image').val() + '"';
                }
                if ($('#sw-logo-image').val() !== '') {
                    var logoImage = ' logo-image="' + $('#sw-logo-image').val() + '"';
                }
                if ($('#sw-logo-bg-colour').val() !== '') {
                    var logoBgColour = ' logo-bg-colour="' + $('#sw-logo-bg-colour').val() + '"';
                }
                if ($('#sw-logo-border-colour').val() !== '') {
                    var logoBorderColour = ' logo-border-colour="' + $('#sw-logo-border-colour').val() + '"';
                }
                if ($('#sw-max-width').val() !== '') {
                    var maxWidth = ' max-width="' + $('#sw-max-width').val() + '"';
                }
                if ($('#sw-tile-layout').val() !== '') {
                    var tileLayout = ' tile-layout="' + $('#sw-tile-layout').val() + '"';
                }
                if ($('#sw-tile-sorting').val() !== '') {
                    var tileSorting = ' tile-sorting="' + $('#sw-tile-sorting').val() + '"';
                }
                if ($('#sw-live-info').val() !== '') {
                    var liveInfo = ' live-info="' + $('#sw-live-info').val() + '"';
                }
                if ($('#sw-tile-bg-colour').val() !== '') {
                    var tileBgColour = ' tile-bg-colour="' + $('#sw-tile-bg-colour').val() + '"';
                }
                if ($('#sw-tile-title-colour').val() !== '') {
                    var tileTitleColour = ' tile-title-colour="' + $('#sw-tile-title-colour').val() + '"';
                }
                if ($('#sw-tile-subtitle-colour').val() !== '') {
                    var tileSubtitleColour = ' tile-subtitle-colour="' + $('#sw-tile-subtitle-colour').val() + '"';
                }
                if ($('#sw-tile-rounded-corners').val() !== '') {
                    var tileRoundedCorners = ' tile-rounded-corners="' + $('#sw-tile-rounded-corners').val() + '"';
                }
                if ($('#sw-hover-colour').val() !== '') {
                    var hoverColour = ' hover-colour="' + $('#sw-hover-colour').val() + '"';
                }
                if ($('#sw-hover-effect').val() !== '') {
                    var hoverEffect = ' hover-effect="' + $('#sw-hover-effect').val() + '"';
                }

                var shortcode = '[' + plugin + game + channels + team + language + limit + layout + embed + embedTheme + embedChat + embedTitle + embedMuted + showOffline + showOfflineText + showOfflineImage + autoplay + featuredStream + title + subtitle + offlineImage + logoImage + profileImage + logoBgColour + logoBorderColour + maxWidth + tileLayout + tileSorting + liveInfo + tileBgColour + tileTitleColour + tileSubtitleColour + tileRoundedCorners + hoverColour + hoverEffect + ']';
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
                    .on('select', function(e) {
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
        
		if (jQuery('body').hasClass('twitch-integration_page_streamweasels-nav')) {

            jQuery('#sw-logo-colour').wpColorPicker();  
            jQuery('#sw-online-dot-colour').wpColorPicker();  
            
        }
        
		if (jQuery('body').hasClass('twitch-integration_page_streamweasels-feature')) {

			  jQuery('#sw-feature-controls-bg-colour').wpColorPicker();
			  jQuery('#sw-feature-controls-arrow-colour').wpColorPicker();
			  
		}
        
        if (jQuery('body').hasClass('twitch-integration_page_streamweasels-showcase')) {

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

			$('#sw-showcase-add-streamer').on('click', function(e) {

				e.preventDefault();

				var streamerWrapper = $('.sw-showcase-wrapper');
				var streamerCount = streamerWrapper.attr('data-streamer-count')
				var streamerCountPlus = parseInt(streamerCount) + 1;

				var html = `
				<fieldset class="row${streamerCountPlus}">
				<span></span>
				<input type="text" class="sw-showcase-name" name="swti_options_showcase[swti_showcase_array][row${streamerCountPlus}][name]" size='40' placeholder="Streamer Name" value="" />
				<input type="text" class="sw-showcase-image" name="swti_options_showcase[swti_showcase_array][row${streamerCountPlus}][image]" size='40' placeholder="Streamer Image" value="" />
				<input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
				<br>
				</fieldset>
				`

				$('.sw-showcase-wrapper').append(html)
				streamerWrapper.attr('data-streamer-count', streamerCountPlus);
			})

            $('.sw-showcase-wrapper fieldset:last-child .upload-btn').click(function(e) {
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

			$(document).on('click', '.sw-showcase-wrapper fieldset span', function(e) {

				e.preventDefault();
				var row = $(this).parent('fieldset')
				row.remove()

				var streamerWrapper = $('.sw-showcase-wrapper');
				var streamerCount = streamerWrapper.attr('data-streamer-count')
				var streamerCountMinus = parseInt(streamerCount) - 1;
				streamerWrapper.attr('data-streamer-count', streamerCountMinus);


				var wrapper = $('.sw-showcase-wrapper')
				var rows = $('.sw-showcase-wrapper fieldset')
				var $i = 0;
				rows.each(function() {
					$i++;
					$(this).attr("class","row-"+$i);
					$(this).find(".sw-showcase-name").attr("name","swti_options_showcase[swti_showcase_array][row"+$i+"][name]");
					$(this).find(".sw-showcase-image").attr("name","swti_options_showcase[swti_showcase_array][row"+$i+"][image]");
				})			

			})

			jQuery('#sw-showcase-controls-bg-colour').wpColorPicker();
			jQuery('#sw-showcase-controls-arrow-colour').wpColorPicker();


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
})(jQuery);