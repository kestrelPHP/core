<?php
/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 1/30/2016
 * Time: 6:21 PM
 */

//define('DIR_SYSTEM', str_replace('\'', '/', realpath(dirname(__FILE__) . '/')) . 'system/');
//define('DIR_IMAGE', str_replace('\'', '/', realpath(dirname(__FILE__) . '/')) . '/image/');

define('DIR_APP',               DIR_ROOT . DIR_SITE);
define('DIR_SYSTEM',            DIR_ROOT . 'system/');
define('DIR_IMAGE',             DIR_ROOT . 'image/');

define('DIR_APP_CONFIG',        DIR_APP . 'config/');
define('DIR_CONFIG',            DIR_SYSTEM . 'config/');

define('DIR_LANGUAGE',          DIR_APP . 'language/');
define('DIR_STATIC',            DIR_APP . 'static/');
define('DIR_JAVASCRIPT',        DIR_APP . 'javascript/');
define('DIR_VIEW',              DIR_APP . 'view/');

define('DIR_DATABASE',          DIR_SYSTEM . 'database/');
define('DIR_STORAGE',           DIR_SYSTEM . 'storage/');

define('DIR_LOGS',              DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION',      DIR_STORAGE . 'modification/');
define('DIR_UPLOAD',            DIR_STORAGE . 'upload/');
define('DIR_DOWNLOAD',          DIR_STORAGE . 'download/');
define('DIR_CACHE',             DIR_STORAGE . 'cache/');

// turn on package
define('PACKAGE_ENCRYPTION',        0);
define('PACKAGE_CURRENCY',          0);
define('PACKAGE_EVENT',             0);
define('PACKAGE_AFFILIATE',         0);
define('PACKAGE_TAX',               0);
define('PACKAGE_WEIGHT',            0);
define('PACKAGE_LENGTH',            0);
define('PACKAGE_CART',              0);
define('PACKAGE_OPENBAY',           0);
define('PACKAGE_CUSTOMER',          0);

define('APP_NAME', 'core/');
define('HTTP_SERVER', 'http://localhost/' . APP_NAME);
define('HTTPS_SERVER', 'https://localhost/' . APP_NAME);

define('USER_GUST',                     -1);
define('USER_SUPER_ADMIN',              0);
define('USER_ADMIN',                    1);
define('USER_PARTNER',                  2);
define('USER_MEMBER',                   3);


define('ELEMENT_SELECT',                   1);
define('ELEMENT_INPUT',                    2);
define('ELEMENT_CHECKBOX',                 3);
define('ELEMENT_RADIO',                    4);

define('PAGER_LIMIT',                    20);