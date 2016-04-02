/**
 * 
 */

jQuery(document).ready(function() {
    jQuery('#preview-mobile-website').data('reveal-init', {
        animation: 'fadeAndPop',
        animation_speed: 250,
        close_on_background_click: false,
        close_on_esc: true,
        dismiss_modal_class: 'close-reveal-modal',
        bg_class: 'reveal-modal-bg',
        bg : jQuery('.reveal-modal-bg'),
        css : {
            open : {
                'opacity': 0,
                'visibility': 'visible',
                'display' : 'block'
            },
            close : {
                'opacity': 1,
                'visibility': 'hidden',
                'display': 'none'
            }
        }
    });

    jQuery('#data_not_save_alert').data('reveal-init', {
        animation: 'fadeAndPop',
        animation_speed: 250,
        close_on_background_click: true,
        close_on_esc: true,
        dismiss_modal_class: 'close-reveal-modal',
        bg_class: 'reveal-modal-bg',
        bg : jQuery('.reveal-modal-bg'),
        css : {
            open : {
                'opacity': 0,
                'visibility': 'visible',
                'display' : 'block'
            },
            close : {
                'opacity': 1,
                'visibility': 'hidden',
                'display': 'none'
            }
        }
    });

    jQuery('.close-reveal-modal').click(function() {
    	setTimeout(function() {
    		jQuery('.reveal-modal-bg').hide();
    	}, 500);
    });

    jQuery("#support-panel .button-toggle").click(function() {
  		jQuery("#support-panel .content-toggle").animate({
              height:'toggle'
  		});
    });

  });

