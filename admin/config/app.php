<?php
/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 3:10 PM
 */

// PAGES
define('PAGE_HOME',                 1);
define('PAGE_ERROR',                2);
define('PAGE_SEARCH',               3);
define('PAGE_PRODUCT',              4);
define('PAGE_ABOUT',                5);
define('PAGE_BLOG',                 6);
define('PAGE_SERVICE',              7);
define('PAGE_CONTACT',              8);
define('PAGE_FAQ',                  9);
define('PAGE_PORTFOLIO',            10);


global $routes;
$routes = array(
    PAGE_HOME               => "Home",
    PAGE_ERROR              => "Error",
    PAGE_SEARCH             => "Search",
    PAGE_PRODUCT            => "Product",
    PAGE_ABOUT              => "AboutUs",
    PAGE_BLOG               => "Blog",
    PAGE_SERVICE            => "Service",
    PAGE_CONTACT            => "Contact",
    PAGE_FAQ                => "Faq",
    PAGE_PORTFOLIO          => "Portfolio",
);
define('PAGE_DEFAULT',              $routes[PAGE_HOME]);
define('PAGE_NOT_FOUND',            $routes[PAGE_ERROR]);