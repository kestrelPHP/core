var TopMenu = {};
TopMenu.timer = null;
TopMenu.opening = false;
TopMenu.auto_close = 1000;
TopMenu.has_touch = false;
TopMenu.touch_opened = false;
TopMenu.mainnav = jQuery('#main-nav');
TopMenu.subnav = jQuery('#sub-nav');

TopMenu.initMenu = function() {
    TopMenu.initEvent();

    jQuery(document).click(function() {
        if ( TopMenu.opening ) {
            TopMenu.autoCloseSubnav();
            clearTimeout(TopMenu.timer);
        }
        else if ( TopMenu.touch_opened ) {
            TopMenu.timer = setTimeout(function() {
                TopMenu.autoCloseSubnav();
                TopMenu.touch_opened = false;
            }, TopMenu.auto_close * 6);
        }
    });
};

TopMenu.initEvent = function() {
    var $mainnav = TopMenu.mainnav.find('.top-bar-section ul');
    var $item = $mainnav.find('li');
    var $pitem = $mainnav.find('li.has-child');
    var $hover_item = $mainnav.find('li.mouseover');

    var $subnav = TopMenu.subnav;
    var $sub_item = $subnav.find('ul li');
    var $preview_link = $subnav.find('li.ng-preview-website');
    var $preview_mobile_link = $subnav.find('li.sub-menu-preview-mobile-website');

    $mainnav.unbind();
    $item.unbind();
    $pitem.unbind();
    $hover_item.unbind();
    $subnav.unbind();
    $sub_item.unbind();

    if ( $preview_link.length ) {
        $preview_link.unbind();
        $preview_link.click(function(evt) {
            evt.preventDefault();
            var link = jQuery('#hotel_staging_domain').val();
            if ( link ) {
                link = jQuery('#select_hotel').val() + link;
                window.open('http://' + link);
            }
        });
    }

    if ( $preview_mobile_link.length ) {
        $preview_mobile_link.unbind();
        $preview_mobile_link.click(function(evt,$sce) {
            evt.preventDefault();
            var link = jQuery('#hotel_staging_domain').val();
            if ( link ) {
                //link = jQuery('#select_hotel').val() + link;
                //var preview_link   = '/console/website/preview?link=' + 'http://'+ link + '&demo=true&time=' + (new Date()).getTime();
                //preview_link   = $sce.trustAsResourceUrl(preview_link);
                jQuery('#preview-mobile-website').foundation('reveal', 'open');
                //jQuery("#preview-general").attr("src", preview_link);
                //document.getElementById('preview-general').contentWindow.location.reload();
            }
        });
    }

    jQuery('#preview-mobile-website').bind('open', function() {
        var link = jQuery('#hotel_staging_domain').val();
        if ( link ) {
            link = jQuery('#select_hotel').val() + link;
            var preview_link   = '/console/website/preview?link=' + 'http://'+ link + '&demo=true&time=' + (new Date()).getTime();
            jQuery("#preview-general").attr("src", preview_link);
        }
    });

    jQuery('#preview-mobile-website').bind('close', function() {
        //jQuery("#preview-general").attr("src", "");
    });

    jQuery('#close-preview-mobile-website').click(function(evt) {
        //jQuery("#preview-general").attr("src", "");
        jQuery('#preview-mobile-website').foundation('reveal', 'close');
    });

    var is_touch = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));

    $pitem.find('a').click(function(evt) { evt.preventDefault(); });

    if ( is_touch ) {
        $item.bind('touchend', function() {
            TopMenu.has_touch = true;
            clearTimeout(TopMenu.timer);

            if ( TopMenu.touch_opened ) {
                TopMenu.autoCloseSubnav();
                TopMenu.touch_opened = false;
                return;
            }

            TopMenu.touch_opened = true;

            if ( jQuery(this).hasClass('has-child') ) {
                var id = jQuery(this).attr('ref');
                if ( $subnav.find('div.' + id + ' ul li').length ) {
                    $subnav.removeClass('hide');
                }
                $subnav.find('div.sub-row.active').removeClass('active').addClass('hide');
                $subnav.find('div.' + id).removeClass('hide').addClass('active');

            }
            else {
                if ( jQuery('#data_not_save').val() == '1' ) {
                    return;
                }
            }
            //TopMenu.opening = true;
        });
    }

    $item.mouseover(function() {
        if ( TopMenu.has_touch ) {
            return;
        }

        TopMenu.opening = true;

        if ( jQuery(this).hasClass('has-child') ) {
            return;
        }

        $subnav.addClass('hide');
    }).mouseout(function() {
        if ( TopMenu.has_touch ) {
            return;
        }

        if ( jQuery(this).hasClass('has-child') ) {
            return;
        }

        if ( $mainnav.find('li.has-child.active').length ) {
            $subnav.removeClass('hide');
        }
        else {
            TopMenu.opening = false;
        }
    });

    $pitem.mouseover(function() {
        if ( TopMenu.has_touch ) {
            return;
        }

        clearTimeout(TopMenu.timer);

        $hover_item.removeClass('mouseover');
        jQuery(this).addClass('mouseover');

        var id = jQuery(this).attr('ref');
        if ( $subnav.find('div.' + id + ' ul li').length ) {
            $subnav.removeClass('hide');
        }
        $subnav.find('div.sub-row.active').removeClass('active').addClass('hide');
        $subnav.find('div.' + id).removeClass('hide').addClass('active');
    }).click(function() {
        if ( TopMenu.has_touch ) {
            return;
        }

        clearTimeout(TopMenu.timer);
        $hover_item.removeClass('mouseover');

        var $this = jQuery(this);
        $this.addClass('mouseover');
        var id = $this.attr('ref');
        if ( $subnav.find('div.' + id + ' ul li').length ) {
            $subnav.removeClass('hide');
        }
        $subnav.find('div.sub-row.active').removeClass('active').addClass('hide');
        $subnav.find('div.' + id).removeClass('hide').addClass('active').find('a:first').trigger('click');
        $this.trigger('mouseover');
    }).mouseout(function() {
        if ( TopMenu.has_touch ) {
            return;
        }

        TopMenu.timer = setTimeout(function() {
            TopMenu.autoCloseSubnav();
        }, TopMenu.auto_close);
    });

    $subnav.mouseover(function() {
        clearTimeout(TopMenu.timer);
    }).mouseout(function() {
        TopMenu.timer = setTimeout(function() {
            TopMenu.autoCloseSubnav();
        }, TopMenu.auto_close);
    });

    $item.click(function() {
        if ( TopMenu.has_touch ) {
            return;
        }

        //Prevent click if not save data
        if ( jQuery('#data_not_save').val() == '1' ) {
            return;
        }

        if ( !jQuery(this).hasClass('active') && !jQuery(this).hasClass('has-child') ) {
            $item.removeClass('active').removeClass('mouseover');
            jQuery(this).addClass('active');

            $sub_item.removeClass('active');
            $subnav.addClass('hide');
        }
    });

    $sub_item.click(function() {
        //Prevent click if not save data
        if ( jQuery('#data_not_save').val() == '1' ) {
            return;
        }

        if ( TopMenu.touch_opened ) {
            TopMenu.autoCloseSubnav();
            TopMenu.touch_opened();
            return;
        }

        $sub_item.removeClass('active');
        jQuery(this).addClass('active');
        if ( $hover_item.length ) {
            $mainnav.find('li.active').removeClass('active').removeClass('mouseover');
            $hover_item.addClass('active').removeClass('mouseover');
        }
    });
};

TopMenu.autoCloseSubnav = function() {
    var $mainnav = TopMenu.mainnav.find('.top-bar-section ul');
    var $parentnav = $mainnav.find('li.has-child');
    var $pactive = $mainnav.find('li.has-child.active');
    var $subnav = TopMenu.subnav;

    TopMenu.opening = false;
    TopMenu.touch_opened = false;

    $parentnav.each(function() {
        if ( jQuery(this).hasClass('mouseover') ) {
            //jQuery(this).removeClass('active');
            jQuery(this).removeClass('mouseover');

            var id = jQuery(this).attr('ref');
            $subnav.find('div.' + id).removeClass('active').addClass('hide');
        }
    });

    $subnav.addClass('hide');
    if ( !$pactive.length ) {
    }
    else {
        var id = $pactive.attr('ref');
        $subnav.find('div.' + id).removeClass('hide').addClass('active');
    }
};
