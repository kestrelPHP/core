/**
 * Created by Nam Dinh on 3/12/2016.
 */
var mappedUrl = {};

var isObject = angular.isObject,
    isUndefined = angular.isUndefined,
    isDefined = angular.isDefined,
    isFunction = angular.isFunction,
    isString = angular.isString,
    forEach = angular.forEach,
    bodyElement = angular.element(document.body),
    injector = angular.injector(['ng']),
    $q = injector.get('$q'),
    $http = injector.get('$http'),
    loadingClass = 'deferred-bootstrap-loading',
    errorClass = 'deferred-bootstrap-error';


var app  = angular.module('ngAdmin', ['ngRoute', 'ngResource']);

(function (app) {
    function AppConfig() {
        throw "Static Class. AppConfig cannot be instantiated.";
    }

    var self = AppConfig;

    self.templatePath = "view/";
    self.testMode = false;
    self.uploadPath = "../uploads/";

    app.conf = AppConfig;

}(app = app || {}));

app.config(['$locationProvider', function ($locationProvider) {
    $locationProvider.html5Mode(false).hashPrefix('!');
}]);

mappedUrl.Routes = routes;

app.config(['$routeProvider', function ($routeProvider) {
    for (var i in mappedUrl.Routes) {
        var o = mappedUrl.Routes[i];
        o.url = o.url || o.name;
        o.title = o.title || o.name;
        o.templateUrl = o.templateUrl || app.conf.templatePath + o.url.replace("/", "-") + '.tpl.html';
        o.controller = o.controller || (o.name.charAt(0).toUpperCase() + o.name.substr(1).toLowerCase()) + 'Controller';
        $routeProvider.when('/' + o.url, { templateUrl: o.templateUrl, controller: o.controller});
    }
    $routeProvider.otherwise({redirectTo: '/login'});
}]);