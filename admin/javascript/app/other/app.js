var app = angular.module('ngAdmin', ['ngRoute', 'ngAnimate', 'ngResource', 'ngCookies']);
// configure your app
//app.factory('routeService', ['$timeout', '$location', function ($timeout, $location) {
app.config(['$locationProvider',function ($locationProvider) {
    $locationProvider.html5Mode(false).hashPrefix('!');
}]);


(function (app) {
    function AppConfig() {
        throw "Static Class. AppConfig cannot be instantiated.";
    }

    var self = AppConfig;

    self.templatePath="view/";
    self.testMode = false;
    self.uploadPath = "../uploads/";

    app.AppConfig = AppConfig;

}(app = app || {}));
var app;

var $defer, loaded = false;

app.run(['$q', '$timeout', function($q, $timeout) {
    $defer = $q.defer();

    //if (angular.isUndefined(CKEDITOR)) {
    //    throw new Error('CKEDITOR not found');
    //}
    //CKEDITOR.disableAutoInline = true;
    //function checkLoaded() {
    //    if (CKEDITOR.status == 'loaded') {
    //        loaded = true;
    //        $defer.resolve();
    //    } else {
    //        checkLoaded();
    //    }
    //}
    //CKEDITOR.on('loaded', checkLoaded);
    //$timeout(checkLoaded, 100);
}]);


app.Routes = [];
/* add view routes here. Title, URL, Template and Controller names will be generated based on the name if not provided */
app.Routes.push({name: 'home'});
app.Routes.push({name: 'edit'});
app.Routes.push({name: 'login'});
app.Routes.push({name: 'edit', url:'edit/:table/:id', templateUrl: app.AppConfig.templatePath + 'edit.tpl.html'});
app.Routes.push({name: 'list', url:'list/:table', templateUrl: app.AppConfig.templatePath + 'list.tpl.html'});

//Routes.push({name: 'food', url: 'food/browse/:category', templateUrl:Config.templatePath+'food-categories.html'});

app.config(['$routeProvider', function ($routeProvider) {
    for (var i in app.Routes) {
        var o = app.Routes[i];
        o.url = o.url || o.name;
        o.title = o.title || o.name;
        o.templateUrl = o.templateUrl || app.AppConfig.templatePath + o.url.replace("/", "-") + '.tpl.html';
        o.controller = o.controller || (o.name.charAt(0).toUpperCase() + o.name.substr(1).toLowerCase()) + 'Controller';
        $routeProvider.when('/' + o.url, { templateUrl: o.templateUrl, controller: o.controller});
    }
    // $routeProvider.when('/food/:category', {templateUrl: Config.templatePath +'food-categories.html', controller: 'FoodController'});
    // $routeProvider.when('/exercise/recording', { templateUrl: Config.templatePath + 'exercise-recording.html', controller: 'ExerciseController'});
    $routeProvider.otherwise({redirectTo: '/login'});
}]);

app.factory('routeService', ['$timeout', '$location', function ($timeout, $location) {

    var service = {};
    /*
     * @description redirect to a new view by changing the location hash.
     * @usage service.redirectTo(['mytheme', 'myalbum']);
     * @param arr Array The values to make up the new address.
     *
     */
    service.redirectTo = function (arr) {
        arr = angular.isArray(arr) ? arr : [arr];
        var hash = "";
        if (arr)
            hash = arr.length > 0 ? arr.join("/") : "";

        $timeout(function () { $location.path(hash); });
    };

    service.currentPath = function () {
        var path = $location.path().replace("/", "");
        path = path.split("/");
        return path;
    };

    service.getRoutePosition = function(name) {
        var i=0;
        for(var r in app.Routes){
            if(app.Routes[r].url == name)
                return i;
            i++;
        }
        return -1;
    };



    service.currentRoute = function () {

        var route = $location.path().replace("/", "");
        route = route.split("/")[0];

        if (route == '')route = 'home';
        return route;
    };

    return service;


}]);

app.controller('AppController', ['$scope', '$timeout', '$rootScope', 'routeService','eventService', '$routeParams', 'apiService','$cookieStore','CmsConfig','$modal',
    function AppController($scope, $timeout, $rootScope, routeService, eventService, $routeParams, apiService, $cookieStore, CmsConfig, $modal) {
        /* structure hack for intellij structrue panel */
        var scope = this;
        if (true)scope = $scope;
        /* end */
        scope.pageClass = '';
        scope.debug = $('body').hasClass('debug-enabled');


        scope.initialize = function () {
            log("8", "AppController", "initialize", "", routeService.currentRoute());

            //scope.config = CmsConfig;//app.CmsConfig;

            toggleListeners(true);
        };
        var toggleListeners = function (enable) {
            scope.$on('$destroy', onDestroy);
            scope.$on('$routeChangeStart', onRouteStart);
            scope.$on('$routeChangeSuccess', onRouteChange);
            scope.$on('$viewContentLoaded', onViewContentLoaded);
        };
        var onDestroy = function (enable) {
            toggleListeners(false);
        };

        var onViewContentLoaded = function() {

        };
        var onActionClick = function(e) {
        };
        var onRouteChange = function($event, current) {
            scope.pageClass = routeService.currentRoute();
        };

        scope.refresh = function () {
            $timeout(function () {
            });
        };
        scope.redirectTo = function (address) {
            routeService.redirectTo(address);
        };

        scope.isLoggedIn = fun

        var onRouteStart = function ($event, next, current) {
               if(! scope.isLoggedIn()){
                   routeService.redirectTo('login');
               }
        };



        scope.initialize();
        return scope;
    }]);
