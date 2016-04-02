<?php

define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'core');
define('DB_PORT', '3306');
define('DB_PREFIX', 'sys_');

// Table
define('TABLE_PAGE',                DB_PREFIX . 'pages');
define('TABLE_CATEGORY',            DB_PREFIX . 'category');
define('TABLE_NEWS',                DB_PREFIX . 'news');
define('TABLE_LANGUAGE',            DB_PREFIX . 'language');
define('TABLE_SETTING',             DB_PREFIX . 'setting');
define('TABLE_USER',                DB_PREFIX . 'users');
define('TABLE_MEMBER',              DB_PREFIX . 'user');
define('TABLE_TOUR_GUIDE',         DB_PREFIX . 'tour_guide');