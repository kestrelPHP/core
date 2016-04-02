/**
 * 
 */

var path = window.location.pathname;
if ( !path || path.indexOf('user/teams') == -1 ) {
	//Check url
	var url = window.location.hash;
	var hash = '/admin/#!/dashboard';
	var str = window.location.href;

	if ( !url || url == '#!' || url == '#!/' ) {
		if ( window.history.pushState ) {
			window.history.pushState(null, '', hash);
		}
		else {
			window.location.hash = '/dashboard';
		}
	}
}

window.onbeforeunload = function (e) {
	if ( jQuery('#data_not_save').val() == '1' ) {	
		e = e || window.event;
	
		// For IE and Firefox prior to version 4
		if ( e ) {
		    e.returnValue = Drupal.t("You haven't saved your work. Do you want to leave without saving?");
		}
	
		// For Safari
		return Drupal.t("You haven't saved your work. Do you want to leave without saving?");
	}
};
window.onunload = function() {
	jQuery('#data_not_save').val(0);
};

window.onkeydown = function(evt) {
	evt = evt || window.event;
	if ( evt.keyCode == 116 ) {
		//evt.preventDefault();
		if ( jQuery('#data_not_save').val() == '1' ) {	
			evt.preventDefault();
			jQuery('#data_key_press').val(1);
			jQuery('#data_not_save_alert').foundation('reveal', 'open');			
		}
	}
};

function changeActiveMenuItem() {
	var url = window.location.hash;
	if ( url == '#/misc/accm_setting/add' ) {
		url = '#/misc/new_accm';
	}

	url = url.replace('#/', '');
	if ( url ) {
		var params = url.split('/');
		if ( params[0] != 'dashboard' ) {
			var data_origin = 'console_' + params[0];
			var $li = jQuery('#main-nav .top-bar-section ul li[data-origin="' + data_origin + '"]');
			if ( $li.length ) {
				jQuery('#main-nav .top-bar-section ul li').removeClass('active');
				$li.addClass('active');
				jQuery('#sub-nav div.row').removeClass('active').addClass('hide');
				if ( $li.hasClass('has-child') ) {
					//Remove this line
					//jQuery('#sub-nav').removeClass('hide').css('position', 'static');
					var child_name = $li.attr('ref');
					jQuery('#sub-nav div.' + child_name).removeClass('hide').addClass('active');
					
					if ( params[1] ) {
						jQuery('#sub-nav ul li.active').removeClass('active');
						//var root_url = '/console/index/#/' + params[0];
						var root_url = '/#/' + params[0];
						root_url += '/' + params[1];

                        if(root_url == '/#/front-desk/setup'){
                            root_url = '/#/front-desk/setup/settings'
                        }
                        if(root_url == '/#/front-desk/booking-details'){
                            root_url = '/#/front-desk/booking-details/0'
                        }
                        //console.log(root_url);
						var $alink_child = jQuery('#sub-nav a[href="' + root_url + '"]');
                        //console.log($alink_child.length);
                        //console.log($alink_child.parent());
						if ( $alink_child.length ) {
							$alink_child.parent().addClass('active');
						}
						else {
							jQuery('#sub-nav div.' + child_name + ' li:first').addClass('active');
						}
					} 
				}
			} 
			else {
				jQuery('#main-nav .top-bar-section ul li.active').removeClass('active');
				jQuery('#sub-nav ul li.active').removeClass('active');
				jQuery('#sub-nav').addClass('hide');
			}
		} 
		else {
			jQuery('#main-nav .top-bar-section ul li.active').removeClass('active');
			jQuery('#sub-nav ul li.active').removeClass('active');
			jQuery('#sub-nav').addClass('hide');
			jQuery('.top-bar-section ul li.mni-dashboard').addClass('active');
		}
	}
}

function groupTable($rows, startIndex, total){
    if (total === 0){
        return;
    }
    var i , currentIndex = startIndex, count=1, lst=[];
    var tds = $rows.find('td:eq('+ currentIndex +')');
    var ctrl = jQuery(tds[0]);
    lst.push($rows[0]);
    for (i=1;i<=tds.length;i++){
        if (ctrl.text() ==  jQuery(tds[i]).text()){
            count++;
            jQuery(tds[i]).addClass('deleted');
            lst.push($rows[i]);
        }else{
            if (count>1){
                ctrl.attr('rowspan',count);
                groupTable(jQuery(lst),startIndex+1,total-1)
            }
            count=1;
            lst = [];
            ctrl=jQuery(tds[i]);
            lst.push($rows[i]);
        }
    }
}