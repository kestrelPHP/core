<?php
// Version
define('VERSION', '0.1');
//define('DIR_SYSTEM', str_replace('\'', '/', realpath(dirname(__FILE__) . '/')) . 'system/');
define('DIR_ROOT', '../');
define('DIR_SITE', 'admin/');


// Configuration
if (is_file('../config.php')) {
    require_once('../config.php');
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');


// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

$config->load('database');
//$config->load('AppConfig');
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
$settings = $loader->model('Setting', "getSetting");
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

// Api
$api = new Api($request, $config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));
$registry->set('url', $api);

// Response
global $mime_types;
$response = new Response();
$response->addHeader('Content-Type', 'text/html; charset=utf-8');
//$response->setCompression($config->get('config_compression'));
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
$select_lang = array();

foreach ($langList as $language) {
    $languages[$language['code']] = $language;
    $select_lang[] = array($language['code'],$language['name']);
}

$code = isset($session->data['language']) ? $session->data['language'] : $config->get('config_language');

// Document
$document = new Document();
$registry->set('document', $document);

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


$data = $document->getData();
$data['direction'] = $config->get('config_directory');
$data['lang'] = $config->get('config_language');

if(1!=1){
    $templatePath = "login.html";

    $post['username'] = isset($request->post['login-user']) ? $request->post['login-user'] : "";
    $post['password'] = isset($request->post['login-pass']) ? $request->post['login-pass'] : "";
    $auth = $loader->model('Auth');
    $user = $auth->getAccountInfo($post);
    $language->load('website/login');
    $data['title'] = $language->get('lang_site_title');
    $data['LANG_SIGN_IN'] = $language->get('lang_sign_in_header');
    $data['langList'] = render_html_element($select_lang, ELEMENT_TYPE_SELECT);
    if( $user->perm == USER_ADMIN ) {
        $request->cookie['usess'] = $user;
        $templatePath = "master.html";
    }

} else {
    //$user = $request->cookie['usess'];
    $templatePath = "master.html";
}

$route = $api->getCurrentRoute();
if(!empty($route)){
    $data = $loader->json($route);
    $response->addHeader('Content-Type: application/json');
    $response->setJsonOutput($data);

} else {
    $response->setOutput($loader->view( $templatePath , $data));
}

$response->output();