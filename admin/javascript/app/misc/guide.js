/**
 * Ng controller for Hotel Setting Listing Page
 */
"use strict";

app.controller('LoginController',
    function($scope, $rootScope, $route, LoginScv, $timeout, $location, $compile, $sce) {
        console.log('login controller');
        var pageNum = 1;
        var delayTime = 2000; //micro second
        var calendarType = 1; // 1: standard, 2: date of birth
        $scope.fetched = false;
        $scope.saving = false;
        $scope.resetFilter = false;

        var optionDateEx = {};
        var calendarIds = ['#guideDOB'];
        var filterParams = {tour_id: $rootScope.current_tour_id};
        var filterIds = {};
        var data = {list: {}, header: {}, paging: {}, gender: {}, status: {}, sorter: {}, country_code: 'vn'};

        $scope.init = function (data) {
            if(data != isUndefined){
                data = {list: {}, header: {}, paging: {}, gender: {}, status: {}, sorter: {}, country_code: 'vn'};
                $scope.header = data.header || {};
                $scope.gender = data.gender || {};
                $scope.status = data.status || {};
                $scope.sorter = data.sorter || {};
                $scope.country_code = data.country_code || {};
                $scope.paging = data.paging || {};
                $scope.list = data.list || {};
            }

            //$scope.initCalendar();

            //$timeout(function(){
            //    jQuery('#phone').intlTelInput({
            //        defaultCountry: $scope.country_code
            //    });
            //}, 2000);
        };

        $scope.initCalendar = function () {
            if( calendarType!='undefined' && calendarType == 2 ){
                var dateToday = new Date();
                var yrRange = dateToday.getFullYear()-50 + ":" + (dateToday.getFullYear());
                optionDateEx = {dateFormat: 'dd M yy', numberOfMonths: 1, stepMonths: 1, maxDate: 0 ,changeMonth: true,
                    changeYear: true, yearRange: yrRange};
            } else {
                $scope.textcalendarmonth = jQuery('#TextCalendarMonth').text().trim().split('#');
                $scope.textcalendarmonthso = jQuery('#TextCalendarMonthSo').text().trim().split('#');
                $scope.textcalendarweek = jQuery('#TextCalendarWeek').text().trim().split('#');
                $scope.textcalendarweekso = jQuery('#TextCalendarWeekSo').text().trim().split('#');
                optionDateEx = tls.getConfigCalendar($scope.textcalendarmonth, $scope.textcalendarmonthso, $scope.textcalendarweek, $scope.textcalendarweekso);
            }

            optionDateEx.maxDate = '0';
            if(typeof calendarIds !== 'undefined' && calendarIds !== null){
                for(var calendarId in calendarIds){
                    jQuery(calendarIds[calendarId]).datepicker(optionDateEx);
                }
            }
        };

        $scope.sleep = function ($time) {
            $timeout(function(){
                $scope.loaded = true;
            }, $time);
        };
        $scope.fetchPage = function (pageNum) {
            if (pageNum == 'n') pageNum = $scope.paging.PageNext;
            else if (pageNum == 'p') pageNum = $scope.paging.PagePrev;

            if (pageNum == isUndefined) {
               // tls.infoMsg(null, 1);
                $scope.fetched = false;
            }
            else if ((pageNum > 0 && pageNum <= $scope.paging.TotalPage) || ($scope.paging.TotalPage == 0)) {
                //tls.infoMsg(null, 1);
                jQuery.extend(filterParams, {pageNum: pageNum});
                $scope.fetched = false;
            }

            if (!$scope.fetched) {
                return LoginScv.list(filterParams).then(function (response) {
                    $scope.init(response.data);
                    $scope.fetched = true;
                    $timeout(function(){
                        $scope.loaded = true;
                    }, delayTime);
                   // tls.clearMsg();

                });
            }
        };

        $scope.getFilterParams = function () {
            filterParams = {};
            if (!$scope.resetFilter) {
                for (var key in filterIds) {
                    var value = jQuery(filterIds[key]).val();
                    if (value != "" && value !== undefined) {
                        if (jQuery(filterIds[key]).parent().hasClass("input-calendar")) {
                            value = $scope.revertToString(value);
                        }
                        filterParams[key] = value;
                    }
                    //jQuery.extend(filterParams, {key: value});
                }
            }
        };

        $scope.edit = function(id){
            $scope.submitted = false;
            jQuery('#modalEdit').foundation('reveal', 'open');
            //$compile(jQuery('#modalEdit').html())($scope);
            $scope.guide = {};
            $scope.guide.ID = id;

            SetupGuideScv.get(id).then(function (response) {
                var guide = response.data.guide;
                $scope.guide.Status = {key: guide.GuideStatus};
                $scope.guide.Gender = {key: guide.Sex};
                $scope.guide.FirstName = guide.FirstName;
                $scope.guide.LastName = guide.LastName;
                $scope.guide.Email = guide.Email;
                $scope.guide.Phone = guide.Phone;
                $scope.guide.DOB = guide.DOB;
                jQuery("#guideDOB").datepicker(optionDateEx).datepicker("setDate", guide.DOB);

                $timeout(function(){
                    jQuery('#phone').intlTelInput({
                        defaultCountry: response.data.country_code
                    });
                }, 1000);
            });
        };

        //$scope.save = function(form){
        //    if ( $scope.saving ) {
        //        return;
        //    }
        //    $scope.submitted = true;
        //
        //    if(tls.checkPhoneSMS('phone') == 1){
        //        $scope.phoneInvalid = true;
        //        var $elm = jQuery('#phone');
        //        $elm.focus();
        //        tls.scrollElementToCenter($elm);
        //        return;
        //    }else{
        //        $scope.phoneInvalid = false;
        //    }
        //
        //    if( !form.$invalid ){
        //        $scope.dataHasSaved();
        //        $scope.saving = true;
        //        SetupGuideScv.save(form).then(function (response) {
        //            $timeout(function () {
        //                $scope.saving = false;
        //                jQuery('#modalEdit').foundation('reveal', 'close');
        //                $scope.init(response.data);
        //            }, 1000);
        //        });
        //    }else{
        //        var $elm = jQuery('#modalEdit input.ng-invalid:first');
        //        $elm.focus();
        //        tls.scrollElementToCenter($elm);
        //    }
        //};

        $scope.deleteItem = function (id, name) {
            name = name ? name : '';
            $scope.IDDelete = id;

            var messageDelete = jQuery('#messageDelete').text();
            jQuery('#modalDelContent').html(messageDelete + ' ' + name + '?');
            jQuery('#modalDel').foundation('reveal', 'open');
        };
        $scope.submitDel = function (id) {
            tls.infoMsg(Drupal.t('Deleting ...'));
            SetupGuideScv.delete(id).then(function (response) {
                tls.infoMsg(Drupal.t('Delete data successfully.'));
                $timeout(function () {
                    tls.clearMsg();
                    jQuery('#modalDel').foundation('reveal', 'close');
                    $scope.init(response.data);
                }, 1000);
            });
        };

        $scope.find = function () {
            $scope.fetched = false;
            $scope.getFilterParams();
            $scope.fetchPage(pageNum);
        };

        // Sort on header
        $scope.sort = function (field, enable) {
            if(enable=="true" || enable=="1" || enable==1 || enable==true){
                if ($scope.sorter.sortBy == field)
                    $scope.sorter.sortDir = $scope.sorter.sortDir == 'asc' ? 'desc' : 'asc';
                else {
                    $scope.sorter.sortBy = field;
                    $scope.sorter.sortDir = 'asc';
                }
                jQuery.extend(filterParams, {sortDir: $scope.sorter.sortDir, sortBy: $scope.sorter.sortBy});
                $scope.fetchPage($scope.paging.PageNum);
            }
        };

        //run
        if( data != isUndefined ){
            $scope.init(data);
        }

        $scope.fetchPage(pageNum);

        jQuery('#modalDel, #modalEdit').data('reveal-init', {
            animation: 'fadeAndPop',
            animation_speed: 250,
            close_on_background_click: true,
            close_on_esc: true,
            dismiss_modal_class: 'close-reveal-modal',
            bg_class: 'reveal-modal-bg',
            bg: jQuery('.reveal-modal-bg'),
            css: {
                open: {
                    'opacity': 0,
                    'visibility': 'visible',
                    'display': 'block'
                },
                close: {
                    'opacity': 1,
                    'visibility': 'hidden',
                    'display': 'none'
                }
            }
        });

        $scope.ngDirtyInvalid = function(form, elementName) {
                return ($scope[form][elementName].$dirty
            && $scope[form][elementName].$invalid && $scope.submitted);
        };
        $scope.ngInvalid = function(form, elementName) {
                return ($scope[form][elementName].$invalid && $scope.submitted);
        };
        $scope.ngInvalidEmail = function(form, elementName) {
                return ($scope[form][elementName].$error.multipleEmails && $scope.submitted);
        };
        $scope.ngDirtyErrorRequired = function(form, elementName) {
            return ($scope[form][elementName].$dirty
            && $scope[form].$error.required && $scope.submitted);
        };
        $scope.ngErrorRequired = function(form, elementName) {
            return ($scope[form][elementName].$error.required && $scope.submitted);
        };
        $scope.start_edit_options = false;
        jQuery('#modalEdit').bind('close', function() {
            $timeout(function() {
                $scope.start_edit_options = false;
            }, 100);

            $scope.start_edit_partner = false;
        });
        jQuery('#modalEdit').bind('open', function() {
            $scope.start_edit_options = true;

        });

        //Begin Calendar]
        $scope.showCalendar = function () {
            var dateToday = new Date();
            var yrRange = dateToday.getFullYear()-50 + ":" + (dateToday.getFullYear());
            var optionDate = {dateFormat: 'dd M yy', numberOfMonths: 1, stepMonths: 1, maxDate: 0 ,changeMonth: true,
                changeYear: true, yearRange: yrRange};
            jQuery("#guideDOB").datepicker(optionDate);
        };
        $scope.revertToString = function (date) {
            return Date.parse(date) / 1000;
        };
        $scope.convertToDate = function (stringDate) {
            var myObj = stringDate,
                myDate = new Date(1000 * myObj);
            return (myDate.toDateString());
        };
        $scope.convertDate = function (string) {
            var mnths = {
                1: "Jan", 2: "Feb", 3: "Mar", 4: "Apr", 5: "May", 6: "Jun",
                7: "Jul", 8: "Aug", 9: "Sep", 10: "Oct", 11: "Nov", 12: "Dec"
            };
            var date = new Date(string);
            var date_value = [date.getDate(), mnths[date.getMonth() + 1], date.getFullYear()].join(" ");
            return date_value;
        };
        // translate calender
        $scope.translateCalendar = function (element, calender) {
            var temp = element;
            temp = temp.replace('Jan', calender[0]);
            temp = temp.replace('Feb', calender[1]);
            temp = temp.replace('Mar', calender[2]);
            temp = temp.replace('Apr', calender[3]);
            temp = temp.replace('May', calender[4]);
            temp = temp.replace('Jun', calender[5]);
            temp = temp.replace('Jul', calender[6]);
            temp = temp.replace('Aug', calender[7]);
            temp = temp.replace('Sep', calender[8]);
            temp = temp.replace('Oct', calender[9]);
            temp = temp.replace('Nov', calender[10]);
            temp = temp.replace('Dec', calender[11]);
            element = temp;
            return element;
        };
        //End Calendar
});

/**
 * Setup
 */
app.factory('LoginScv', function($http, $resource, $q) {
    var conf = app.conf;
    return {
        list: function(params) {
            var url = conf.actionPath + 'login' + '?' + jQuery.param(params);
            //var url = conf.actionPath + '/bkngin/guide/1' + '?' + jQuery.param(params);
            var resource = $resource(url);
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
        get: function(pId) {
            var url = conf.actionPath + '/bkngin/guide/act/1?act=get';
            var resource = $resource(url, {ProviderID: '@id'});
            var deferred = $q.defer();
            resource.get(
                {ProviderID: pId},
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
        save: function(form) {
            var url = conf.actionPath + '/bkngin/guide/act/1?act=save';
            var promise= $http.post(url, jQuery('#guide_form').serialize(),
                {headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
                .then(function (response) {
                    return response.data;
                });
            return promise;
        },
        delete: function(pId){
            var url = conf.actionPath + '/bkngin/guide/act/1?act=delete';
            var promise = $http.post(url, 'ProviderID=' + pId,
                {headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
                .then(function (response) {
                    return response.data;
                });
            return promise;
        }
    }
});