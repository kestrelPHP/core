<?php

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

$config->load('database');
$config->load('app');
//$config->load('FilterConfig');
//$config->load('RouteConfig');
//$config->load('GlobalConfig');

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$registry->set('db', $db);

// Domain
$config->set('config_url', HTTP_SERVER);
$config->set('config_ssl', HTTPS_SERVER);


// Settings
$settings = $loader->model('Setting', 'getSetting');
foreach ($settings as $result) {
    if (!$result['serialized']) {
        $config->set($result['key'], $result['value']);
    } else {
        $config->set($result['key'], json_decode($result['value'], true));
    }
}
// Log
$log = new Log($config->get('config_error_filename'));
$registry->set('log', $log);

function error_handler($code, $message, $file, $line) {
    global $log, $config;

    // error suppressed with @
    if (error_reporting() === 0) {
        return false;
    }

    switch ($code) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $error = 'Warning';
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $error = 'Fatal Error';
            break;
        default:
            $error = 'Unknown';
            break;
    }

    if ($config->get('config_error_display')) {
        echo '<b>' . $error . '</b>: ' . $message . ' in <b>' . $file . '</b> on line <b>' . $line . '</b>';
    }

    if ($config->get('config_error_log')) {
        $log->write('PHP ' . $error . ':  ' . $message . ' in ' . $file . ' on line ' . $line);
    }

    return true;
}

// Error Handler
set_error_handler('error_handler');

// Request
$request = new Request();
$registry->set('request', $request);

// Url
$url = new Url($request, $config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));
$registry->set('url', $url);

// Response
global $mime_types;
$response = new Response();

$response->addHeader('Content-Type', 'text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache);

// Session
$session = new Session();
$registry->set('session', $session);

// Language Detection
$languages = array();
$langList = $loader->model('Language', 'fetchAll');

$pages = array();
$pageList = $loader->model('Page', 'fetchAll');

foreach ($langList as $language) {
    $languages[$language['code']] = $language;
    foreach ($pageList as $page) {
        $page['code'] = get_lang_text($page['code'], $language['code']);
        if(empty($page['code'])) continue;
        $page['title'] = get_lang_text($page['title'], $language['code']);
        $page['description'] = get_lang_text($page['description'], $language['code']);
        $page['keywords'] = get_lang_text($page['keywords'], $language['code']);
        $page['lang'] = $language['code'];

        $pages[$page['code']] = $page;
    }
}

$url->addRewrite($pages);

// Route Detection
$route = $url->getCurrentRoute();
global $routes;
if(in_array($route, array_keys($pages))){
    $pageCurrent = $pages[$route];
    $code = $pageCurrent['lang'];
} else {
    $code = isset($session->data['language']) ? $session->data['language'] : $config->get('config_language');
    $pageCurrent = function() use($pages, $code) {
        foreach ($pages as $page) {
            if ($page['lang'] == $code && $page['id'] == PAGE_ERROR) {
                return $page;
            }
        }
    };
//    $pageCurrent = $route;
//    $code = isset($session->data['language']) ? $session->data['language'] : $config->get('config_language');
//    foreach ($pages as $page) {
//        if( $page['lang'] == $code &&  $route == PAGE_HOME ){
//            $pageCurrent =  $page;
//            break;
//        } else if( $page['lang'] == $code && $page['id'] == PAGE_ERROR ){
//            $pageCurrent =  $page;
//            break;
//        }
//    }
}

$route = $routes[$pageCurrent['id']];

// Document
$document = new Document();
$registry->set('document', $document);

$domain = $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url');
//$document->setBase($domain);
$document->setTitle($pageCurrent['title'] . " | " . $config->get('config_name'));
$document->setDescription($pageCurrent['description']);
$document->setKeywords($pageCurrent['keywords']);
$document->setLanguage(array("code"=>$languages[$code]['code'], "direction"=>$languages[$code]['directory']));

/*
if (isset($session->data['language']) && array_key_exists($session->data['language'], $languages)) {
    $code = $session->data['language'];
} elseif (isset($request->cookie['language']) && array_key_exists($request->cookie['language'], $languages)) {
    $code = $request->cookie['language'];
} else {
    $detect = '';
    if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && $request->server['HTTP_ACCEPT_LANGUAGE']) {
        $browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);

        foreach ($browser_languages as $browser_language) {
            foreach ($languages as $key => $value) {
                if ($value['status']) {
                    $locale = explode(',', $value['locale']);

                    if (in_array($browser_language, $locale)) {
                        $detect = $key;
                        break 2;
                    }
                }
            }
        }
    }

    $code = $detect ? $detect : $config->get('config_language');
}
*/

if (!isset($session->data['language']) || $session->data['language'] != $code) {
    $session->data['language'] = $code;
}

if (!isset($request->cookie['language']) || $request->cookie['language'] != $code) {
    setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
}

$config->set('config_language_id', $languages[$code]['language_id']);
$config->set('config_language', $languages[$code]['code']);
$config->set('config_directory', $languages[$code]['directory']);

// Language
$language = new Language($languages[$code]['directory']);
$language->load($languages[$code]['directory']);
$language->set('current', $code);
$registry->set('language', $language);


// Customer
if(PACKAGE_CUSTOMER) {
    $customer = new Customer($registry);
    $registry->set('customer', $customer);

    // Customer Group
    if ($customer->isLogged()) {
        $config->set('config_customer_group_id', $customer->getGroupId());
    } elseif (isset($session->data['customer']) && isset($session->data['customer']['customer_group_id'])) {
        // For API calls
        $config->set('config_customer_group_id', $session->data['customer']['customer_group_id']);
    } elseif (isset($session->data['guest']) && isset($session->data['guest']['customer_group_id'])) {
        $config->set('config_customer_group_id', $session->data['guest']['customer_group_id']);
    }
}

// Affiliate
if(PACKAGE_AFFILIATE)
    $registry->set('affiliate', new Affiliate($registry));

// Currency
if(PACKAGE_CURRENCY)
    $registry->set('currency', new Currency($registry));

// Tax
if(PACKAGE_TAX)
    $registry->set('tax', new Tax($registry));

// Weight
if(PACKAGE_WEIGHT)
    $registry->set('weight', new Weight($registry));

// Length
if(PACKAGE_LENGTH)
    $registry->set('length', new Length($registry));

// Cart
if(PACKAGE_CART)
    $registry->set('cart', new Cart($registry));

// Encryption
if(PACKAGE_ENCRYPTION)
    $registry->set('encryption', new Encryption($config->get('config_encryption')));

// OpenBay Pro
if(PACKAGE_OPENBAY)
    $registry->set('openbay', new Openbay($registry));

// Event
if(PACKAGE_EVENT) {
    $event = new Event($registry);
    $registry->set('event', $event);

    $loader->model('Event');
    $events = $registry->get('model_event')->getEvent();

    foreach ($events as $result) {
        $event->register($result['trigger'], $result['action']);
    }
}

// Front Controller
$controller = new Front($registry);

// Maintenance Mode
//$controller->addPreAction(new Action('common/maintenance'));

// SEO URL's
//$controller->addPreAction(new Action('common/seo_url'));

// Router
$action = new Action($route);

// Dispatch
$controller->dispatch($action, new Action('Error@not_found'));

// Output
$response->output();