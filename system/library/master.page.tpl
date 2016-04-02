<!DOCTYPE html>
<html lang="en" ng-app="ngAdmin">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ngAdmin</title>

    <title ng-bind="doctitle"></title>

    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="stylesheet" href="static/css/site.css">
    <script src='javascript/library/modernizr.custom-2.6.2.min.js'></script>

</head>

<!--<body ng-controller="AppController" ng-class="{'loggedin': isLoggedIn()}">-->
<body ng-controller="MainController">
<div class="page-wrap">
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="navbar-header">
            <div class="row">
                <div class="col-xs-9">

                    <a class="" href="#!/home">
                        <h1 ng-cloak>
                <span ng-if="config.clientLogo"><img
                            class="img-responsive"
                            ng-src="{{config.clientLogo}}"
                            title="{{config.sitetitle}}"/></span>
                            <span ng-if="!config.clientLogo">{{config.sitetitle}}</span>
                        </h1>
                    </a>

                </div>
                <div class="col-xs-3">
                    <button type="button" class="navbar-toggle" ng-click="sidebarCollapsed = !sidebarCollapsed">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
            </div>


        </div>

    </nav>


    <div class="main container-fluid">
        <div class="row ng-cloak" ng-if="isLoggedIn()">
            <div class="col-sm-12">
                <h1 class="page-header">{{currenttable}}</h1>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12" ng-view>
            </div>
        </div>

    </div>

    <footer class="footer">

        <div class="container text-right">
            {{config.sitetitle}} &copy; {{config.client}}
        </div>
    </footer>

</div>
<!-- js libraries -->
<script src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
<script src='//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js'></script>
<script src='//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-route.min.js'></script>
<script src='//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-resource.min.js'></script>
<script src='//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-animate.min.js'></script>
<script src='//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-touch.min.js'></script>
<script src='//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-cookies.min.js'></script>

<script src="javascript/library/ckeditor/ckeditor.js"></script>
<link rel="stylesheet" href="javascript/library/ckeditor/ng-ckeditor.css">

<!-- js builds -->

<!--SCRIPTS-->
<!--
<script src="assets/js/build/ngadmin-plugins-min.js"></script>
<script src="assets/js/build/ngadmin-app-src.js"></script>
-->
<!--<script src="javascript/app/plugin.js"></script>-->
<script src="javascript/app/app.manage.js"></script>
<script src="javascript/app/guide.js"></script>
<!--SCRIPTS END-->


</body>

</html>
