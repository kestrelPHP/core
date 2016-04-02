'use strict';
// Declare app level module which depends on filters, and services
var app = angular.module('ngAdmin', ['ngRoute', 'ngResource']);

var isObject = angular.isObject,
    isUndefined = angular.isUndefined,
    isDefined = angular.isDefined,
    isFunction = angular.isFunction,
    isString = angular.isString,
    isArray = angular.isArray,
    isNumber = angular.isNumber,
    isDate = angular.isDate,
    isInvalid = 'undefined',
    isEmpty = '',
    forEach = angular.forEach,
    bodyElement = angular.element(document.body),
    injector = angular.injector(['ng']),
    $q = injector.get('$q'),
    $http = injector.get('$http'),
    loadingClass = 'deferred-bootstrap-loading',
    errorClass = 'deferred-bootstrap-error';

app.config(['$locationProvider', function ($locationProvider) {
    $locationProvider.html5Mode(false).hashPrefix('!');
}]);


(function (app) {
    function AppConfig() {
        throw "Static Class. AppConfig cannot be instantiated.";
    }

    var self = AppConfig;

    self.actionPath = "http://core.local/admin/";
    self.templatePath = "view/";
    self.testMode = false;
    self.uploadPath = "../uploads/";

    app.conf = AppConfig;

}(app = app || {}));


var controllers = [];
controllers.push({name: 'DashboardController', template: 'dashboard.html', url: '/dashboard', title: 'Dashboard'});
controllers.push({
    name: 'PageController',
    template: 'website/page.html',
    url: '/website/page',
    title: 'Page Management'
});
controllers.push({
    name: 'LanguageController',
    template: 'website/language.html',
    url: '/website/language',
    title: 'Language Management'
});
controllers.push({
    name: 'SettingController',
    template: 'website/setting.html',
    url: '/website/setting',
    title: 'Website Development'
});
controllers.push({
    name: 'MemberController',
    template: 'misc/member/list.html',
    url: '/misc/member',
    title: 'User Management'
});

app.config(['$routeProvider', function ($routeProvider) {
    var conf = app.conf;
    for ( var i in controllers ) {
        var c = controllers[i];
        c.path = c.url;
        c.template = conf.templatePath + c.template || app.conf.templatePath + c.url.replace("/", "_") + '.html';
        c.controller = c.name;
        c.title = c.title || "Administrator | Website Management";
        $routeProvider.when(c.path, {templateUrl: c.template, controller: c.controller, title: c.title});
    }
    $routeProvider.otherwise({redirectTo: '/dashboard'});
}]);

app.run(function ($rootScope, $location) {
    //jQuery(document).foundation();
    var conf = app.conf;

    $rootScope.$on('$locationChangeStart', function (event) {
        //Prevent run app
        if ($location.path() == '/' || $location.path() == '') {
            event.preventDefault();
        }

        var cpath = $location.path();
        if (cpath[cpath.length - 1] == '/') {
            cpath = cpath.slice(0, -1);
        }
        //console.log(cpath);

        //Redirect special links
        //if ( typeof mappedUrl[cpath] !== 'undefined' ) {
        //    $location.path(mappedUrl[cpath].replace('{hotel_id}', 1));
        //    return;
        //}

    });


    $rootScope.$on('$locationChangeSuccess', function (event) {
        jQuery('#page_current_url').val($location.path());
    });

    $rootScope.$on("$routeChangeSuccess", function (event, currentRoute, previousRoute) {
        var pageTitle = '';
        //Change page title, based on Route information
        if (currentRoute && currentRoute.title) {
            pageTitle = currentRoute.title;
        }
        $rootScope.pageTitle = pageTitle;
    });

    $rootScope.$on('$viewContentLoaded', function ($scope) {
        if ($rootScope.pageTitle != '')
            $rootScope.pageTitle = $rootScope.pageTitle + ' | Hotel Link Solutions';
        else {
            $rootScope.pageTitle = jQuery("#meta-title").val() + ' | Hotel Link Solutions';
        }

        var cpath = $location.path();
    });

    $rootScope.dataHasChanged = function () {
        jQuery('#data_not_save').val(1);
        //console.log('aaaa');
    };

    $rootScope.dataHasSaved = function () {
        jQuery('#data_not_save').val(0);
    };


    $rootScope.hotel_list_change = false;
    //$cope.$watch('hotel_list_change') //reload hotel list

    $rootScope.changeNotSavePage = function ($route) {
        jQuery('#data_not_save').val(0);
        jQuery('#page_change_url_abc').val(0);
        jQuery('#data_not_save_alert').foundation('reveal', 'close');
        jQuery('.reveal-modal-bg').hide();

        if (jQuery('#data_key_press').val() == '1') {
            jQuery('#data_key_press').val(0);
            window.location.reload();
        }

        if (jQuery('#data_change_language').val() == '1') {
            jQuery('#data_change_language').val(0);
            if (jQuery('#select_language').length) {
                jQuery('#select_language').trigger('change');
                return;
            }
        }

        $rootScope.$apply(function () {
            //var url = jQuery('#page_change_url').val();
            //var cur_url = window.location.hash.replace('#!/', '/');
            //var redirect_url = url;
            //if ( typeof mappedUrl[url] !== 'undefined' ) {
            //    redirect_url = mappedUrl[url].replace('{hotel_id}', 111);
            //}
            //
            //$location.path(redirect_url);
            //if ( url == cur_url ) {
            //    $route.reload();
            //}
        });
    };
});

app.controller('MainController',
    function ($rootScope, $scope, $route, $location, $http, $sce, $timeout, $compile) {
        $rootScope.$on('$locationChangeSuccess', function (event) {
            if (!$route.current) {
                console.log('success: ' + $location.path().slice(0, -1));
                var cpath = $location.path();
                if (cpath[cpath.length - 1] == '/') {
                    cpath = cpath.slice(0, -1);
                }
                //Redirect special links
                //if ( typeof mappedUrl[cpath] !== 'undefined' ) {
                //    $location.path(mappedUrl[cpath].replace('{hotel_id}', 111));
                //    return;
                //}

                return;
            }

            if (!$rootScope.has_load_data) {

                jQuery('#hotel_loading').show();

                $timeout(function () {
                    TopMenu.initMenu();
                    changeActiveMenuItem();
                });

                $rootScope.has_load_data = true;

            }

        });
    });

app.factory('apiService', function($http, $resource, $q) {
    var conf = app.conf;
    return {
        list: function(url, params) {
            var query = ( params != isUndefined || params != "") ? '?'+jQuery.param(params) : "";
            var path = conf.actionPath + url + query;
            var resource = $resource(path);
            var deferred = $q.defer();
            resource.get(
                {},
                function(event) {
                    deferred.resolve(event);
                },
                function(response) {
                    deferred.reject(response);
                }
            );
            var promise = deferred.promise;
            return promise;
        },
        get: function( url, pid ) {
            var url = conf.actionPath + url;
            var resource = $resource(url, {ProviderID: '@id'});
            var deferred = $q.defer();
            resource.get(
                { ProviderID: pid },
                function( event ) {
                    deferred.resolve(event);
                },
                function( response ) {
                    deferred.reject( response );
                }
            );
            var promise = deferred.promise;
            return promise;
        },
        save: function( url, form_id ) {
            var path = conf.actionPath + url;
            var form = jQuery( form_id ).serialize();
            var promise= $http.post( path, form, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}} )
                .then( function (response ) {
                    return response.data;
                });
            return promise;
        },
        delete: function( url, pid ){
            var url = conf.actionPath + url;
            var query = 'ProviderID=' + pid;
            var promise = $http.post(url, query, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
                .then(function (response) {
                    return response.data;
                });
            return promise;
        }
    }
});

/**
 * @file
 * Angular Directives
 */
//var directive = {};
//
//directive.ngBreadcrumb = function() {
//    return {
//        restrict: 'C',
//        template: '<div ng-include="getBreadCrumb()"></div>',
//        controller: 'Controller'
//    };
//};
//
//
//
//directive.preventDefault = function() {
//    return function(scope, element, attrs) {
//    	element.bind('click', function(event) {
//            event.preventDefault();
//        });
//    };
//};
//
//directive.ngName = function($compile) {
//	return {
//		restrict: "A",
//		terminal: true,
//		priority: 1000,
//		link: function(scope, element, attrs) {
//			var name = scope.$eval(element.attr('ng-name'));
//	    	element.removeAttr('ng-name');
//	    	element.attr('name', name);
//			$compile(element)(scope);
//		}
//	};
//};
//
//
//directive.autoUrl = function($timeout) {
//
//    return {
//        restrict: 'A',
//        link: function(scope, elm, attrs) {
//        	$timeout(function() {
//        		if ( elm.hasClass('ng-pristine') && !elm.val() ) {
//    	        	var title = attrs.autoUrl;
//    	        	if ( title ) {
//    		            scope.$watch(title, function() {
//    		            	var url = scope.$eval(title);
//    		            	if ( url ) {
//	    		            	url = url.replace(/[^0-9a-zA-Z]/g, " ");
//	    		                url = url.replace(/\s\s+/g, " ").trim();
//	    		                url = url.replace(/\s/g, "-");
//	    		                url = url.toLowerCase();
//	    		                elm.val(url);
//
//	    		                if ( scope.autoUrlChange ) {
//	    		                	scope.autoUrlChange();
//	    		                }
//    		            	}
//    		            }, true);
//    	        	}
//            	}
//        	}, 300);
//        }
//    };
//};
//
//
//angular.module('hlsDirectives', []).directive(directive);
//
///**
// * Ng controller for Hotel Setting Listing Page
// */
//"use strict";
//app.controller('DashboardListController',
//		function($scope, DashboardSvc, $location, $sce, $timeout, $route) {
//
//    // Init scope
//    var conf = app.conf;
//
//    $scope.hotel_id = $route.current.pathParams.hotel_id;
//
//    $scope.indexPath = '';
//    $scope.fetched = false;
//    $scope.textcalendarmonthso = jQuery('#TextCalendarMonthSo').text().trim().split('#');
//    $scope.initData = function (filter, paging, header, display, displayOrder, totalPage, totalItem, totalResult, list) {
//        $scope.totalPage = parseInt(totalPage || 0);
//        $scope.totalItem = parseInt(totalItem || 0);
//        $scope.totalResult = totalResult || 0;
//        $scope.list = list || [];
//        $scope.header = header || [];
//        $scope.display = display || [];
//        $scope.displayOrder = displayOrder || [];
//        $scope.filter = filter || [];
//        $scope.paging = paging || {};
//        $scope.pagingNow = $scope.paging.page;
//        if (_.size($scope.paging) > 0) {
//            $scope.paging.page = parseInt($scope.paging.page);
//            $scope.paging.itemPerPage = parseInt($scope.paging.itemPerPage);
//        }
//        $scope.headerLength = _.size($scope.header) - 1;
//        $scope.itemFrom = 0;
//    };
//    $scope.initData();
//
//    $scope.to_trusted = function(html_code) {
//    	if ( !html_code ) {
//    		return '';
//    	}
//        return $sce.trustAsHtml(html_code);
//    }
//    // translate calender
//    $scope.translateCalendar = function (element,calender){
//        var temp =element;
//        temp = temp.replace('Jan',calender[0]);
//        temp = temp.replace('Feb',calender[1]);
//        temp = temp.replace('Mar',calender[2]);
//        temp = temp.replace('Apr',calender[3]);
//        temp = temp.replace('May',calender[4]);
//        temp = temp.replace('Jun',calender[5]);
//        temp = temp.replace('Jul',calender[6]);
//        temp = temp.replace('Aug',calender[7]);
//        temp = temp.replace('Sep',calender[8]);
//        temp = temp.replace('Oct',calender[9]);
//        temp = temp.replace('Nov',calender[10]);
//        temp = temp.replace('Dec',calender[11]);
//        element = temp;
//        return element;
//    };
//    // Get News
//    $scope.fetch = function (page) {
//        page = page || $scope.paging.page;
//        jQuery('#list-news .sending').show();
//        jQuery('.list-news-dashboard').addClass('hide');
//        for (var key in $scope.filter) {
//            $scope.filter[key] = jQuery('#' + key).val();
//        }
//        var _paging = {};
//        if (!_.isUndefined(page) || page != null) {
//            _paging = _.clone($scope.paging);
//            _paging.page = page;
//        }
//
//        return DashboardSvc.dashboard($scope.hotel_id, _paging, $scope.filter).then(function (response) {
//            var data = response.data;
//            $scope.initData(data.filter.news, data.paging.news, data.header.news, data.display.news, data.displayOrder.news,
//                data.totalPage.news, data.totalItem.news, data.totalResult.news, data.list.news);
//            $scope.fetched = true;
//            $scope.itemFrom = $scope.paging.itemPerPage * ($scope.paging.page - 1);
//            $timeout(function () {
//                jQuery('#list-news .sending').hide();
//                jQuery('.list-news-dashboard').removeClass('hide');
//            }, 0);
//
//        }, function () {
//            $scope.initData();
//        });
//    };
//    // Go to page
//    $scope.getPage = function (page) {
//        page = !_.isUndefined(page) ? (parseInt($scope.paging.page) + page) : page;
//
//        return $scope.fetch(page);
//    };
//    $scope.getPage();
//});
//
//app.controller('DashboardPageViewController',
//		function($scope, DashboardSvc, $route, $sce, $interpolate) {
//
//	$scope.hotel_id = $route.current.pathParams.hotel_id;
//    jQuery('#page-view .sending').show();
//    var conf = app.conf;
//    $scope.saveSuccess = false;
//    $scope.indexPath = '#';
//    DashboardSvc.check($scope.hotel_id).then(function (data) {
//        $scope.engine_solution_block  = data.data.checkBookingEngine;
//        $scope.website_solution_block = data.data.checkWebiste;
//        $scope.mobile_solution_block  = data.data.checkMobile;
//        $scope.widget_solution_block  = data.data.checkWidget;
//        $scope.profile                = data.data.profileId;
//        $scope.profileWidgetId        = data.data.profileWidgetId;
//        if(($scope.website_solution_block!=0 || $scope.mobile_solution_block!=0) && $scope.profile!=0){
//            jQuery('#website_traffic_block').removeClass('hide');
//            DashboardSvc.page_view($scope.hotel_id).then(function (data) {
//                jQuery('#hbe_save_ga_web').val('1');
//                $scope.page_views      = data.data.visits_home;
//                $scope.error_page_view = data.data.error;
//                $scope.h_start    = data.data.h_start;
//                $scope.h_end      = data.data.h_end;
//                $scope.h_f        = data.data.f;
//                $scope.point      = $interpolate('{{h_start}}{{page_views}}{{h_end}}')($scope);
//                $scope.profileId  = data.data.profileID;
//
//                jQuery('#page-view').removeClass('hide');
//                if($scope.profileWidgetId ==0 || $scope.widget_solution_block==0){
//                    jQuery('#page-view .sending').hide();
//                }
//            });
//            jQuery("#page-view-widget").hide();
//            if($scope.profile!=0 && $scope.engine_solution_block!=0){
//                jQuery('#conversion_block').removeClass('hide');
//                jQuery("#conversion-widget").hide();
//                DashboardSvc.conversion_rate($scope.hotel_id, $scope.profile).then(function (data) {
//                    $scope.conversion_rates_website = data.data.conversion_rates_website;
//                    $scope.totalBooking     = data.data.totalBooking;
//                    jQuery('.rate-data').removeClass('hide');
//                    if($scope.profileWidgetId ==0 || $scope.widget_solution_block==0){
//                        jQuery('#conversionRate .sending').hide();
//                    }
//                });
//            }
//
//            setTimeout(function() {
//                DashboardSvc.saveGA($scope.hotel_id, $scope.profile, 1).then(function (data) {});
//            }, 500);
//
//        }
//        if($scope.profileWidgetId !=0 && $scope.widget_solution_block!=0){
//            jQuery('#website_traffic_block').removeClass('hide');
//            DashboardSvc.page_view_widget($scope.hotel_id).then(function (data) {
//                jQuery('#hbe_save_ga_wit').val('1');
//                $scope.page_views_widget      = data.data.visits_home_widget;
//                $scope.error_page_view_widget = data.data.error_widget;
//                $scope.wh_start    = data.data.wh_start;
//                $scope.wh_end      = data.data.wh_end;
//                $scope.w_f         = data.data.w_f;
//
//                $scope.point_widget  = $interpolate('{{wh_start}}{{page_views_widget}}{{wh_end}}')($scope);
//                $scope.profileWId    = data.data.profileWidgetID;
//                jQuery('#page-view').removeClass('hide');
//                jQuery('#page-view .sending').hide();
//                jQuery("#page-view-widget").show();
//
//                if($scope.profileWidgetId!=0 && $scope.engine_solution_block!=0){
//                    jQuery('#conversion_block').removeClass('hide');
//                    DashboardSvc.conversion_rate($scope.hotel_id, $scope.profileWidgetId).then(function (data) {
//                        $scope.website_solution  = data.data.checkPageViews.checkWebsite;
//                        $scope.booking_solution  = data.data.checkPageViews.checkBookingEngine;
//                        $scope.mobile_solution   = data.data.checkPageViews.checkMobile;
//                        $scope.widget_solution   = data.data.checkPageViews.checkWidget;
//                        $scope.percent_widget    = data.data.conversion_rates_widget;
//                        $scope.totalBooking      = data.data.totalBooking;
//                        jQuery('#conversionRate .sending').hide();
//                        jQuery("#conversion-widget").show();
//                    });
//                }
//            });
//
//            setTimeout(function() {
//                DashboardSvc.saveGA($scope.hotel_id, $scope.profileWidgetId, 2).then(function (data) { });
//            }, 500);
//        }
//    });
//});
//
//
//app.factory('DashboardSvc', function($http, $resource, $q) {
//    var conf = app.conf;
//    return {
//        dashboard: function (hotel_id, paging, filter) {
//            var param = jQuery.extend(paging, filter);
//            param = jQuery.param(param);
//            param = param ? '&' + param : param;
//            param = 'hotel_id=' + hotel_id + param;
//            var promise = $http.get(conf.action + '/index/hls_dashboard?' + param)
//                .then(function (response) {
//                    return response.data;
//                });
//            return promise;
//        },
//        send_mail: function(hotel_id) {
//            var promise= $http.get(
//            		conf.action
//            		+ '/callback?module=hls_dashboard&invoke=send_email_health_check'
//            		+ '&hotel_id=' + hotel_id
//        		).then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        page_view: function(hotel_id) {
//            var promise= $http.get(
//            		conf.action
//            		+ '/callback?module=hls_dashboard&invoke=page_view'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        page_view_widget: function(hotel_id) {
//            var promise= $http.get(
//            		conf.action
//            		+ '/callback?module=hls_dashboard&invoke=page_view_widget'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        bookings_today: function(hotel_id) {
//        	var hbeId = jQuery('#hbe_hotel_id').val();
//            var promise= $http.get(
//            		conf.action
//            		+ '/callback?module=hls_dashboard&invoke=bookings_today'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        arrivals: function(hotel_id) {
//        	var hbeId = jQuery('#hbe_hotel_id').val();
//            var promise= $http.get(
//            		conf.action
//            		+ '/callback?module=hls_dashboard&invoke=arrivals'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        website_traffic: function(hotel_id) {
//            var promise= $http.get(
//            		conf.action
//            		+ '/callback?module=hls_dashboard&invoke=website_traffic'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        invoices_unpaid: function(hotel_id) {
//            var promise= $http.get(
//            		conf.action
//            		+ '/callback?module=hls_dashboard&invoke=invoices_unpaid'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//
//        rates: function(hotel_id) {
//            var hbeId = jQuery('#hbe_hotel_id').val();
//            var promise= $http.get(conf.action
//            		+ '/callback?module=hls_dashboard&invoke=rates'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        saveGA: function(hotel_id, id, type) {
//            var promise= $http.get(conf.action
//            		+ '/callback?module=hls_dashboard&invoke=save_google_analytic&id=' + id
//            		+ '&type=' + type
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        conversion_rate: function(hotel_id, id) {
//            var hbeId = jQuery('#hbe_hotel_id').val();
//            var promise= $http.get(conf.action
//            		+ '/callback?module=hls_dashboard&invoke=conversion_rate&id=' + id
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        conversion_rate_widget: function(hotel_id) {
//            var hbeId = jQuery('#hbe_hotel_id').val();
//            var promise= $http.get(conf.action
//            		+ '/callback?module=hls_dashboard&invoke=conversion_rate_widget'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        tip: function(hotel_id) {
//            var promise= $http.get(conf.action
//            		+ '/callback?module=hls_dashboard&invoke=tip'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        health_check: function(hotel_id) {
//            var promise= $http.get(conf.action
//            		+ '/callback?module=hls_dashboard&invoke=health_check'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        viewPdf: function(hotel_id) {
//            var promise= $http.get(conf.action
//                    + '/callback?module=health&invoke=hls_dashboard_invoices_unpaid_view_pdf'
//                    + '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        check: function(hotel_id) {
//            var promise= $http.get(conf.action
//            		+ '/callback?module=hls_dashboard&invoke=check_solution'
//            		+ '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        },
//        occupancy: function(hotel_id) {
//            var promise= $http.get(
//                    conf.action
//                        + '/callback?module=hls_dashboard&invoke=occupancy'
//                        + '&hotel_id=' + hotel_id)
//                .then(function (response) {
//                    return response.data;});
//            return promise;
//        }
//    };
//});