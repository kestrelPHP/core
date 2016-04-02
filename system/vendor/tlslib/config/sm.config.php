<?php
return array(
//    'db' => array(
//        'driver' => 'Pdo',
//        'dsn' => 'mysql:dbname=core;host=localhost',
//        'username'       => 'root',
//        'password'       => ''
//    ),
    'service_manager' => array(
        'factories' => array(
            'cache' => 'Zend\Cache\Service\StorageCacheFactory',
            'dbadapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            
            'GuideEntity' => 'tlslib\db\GuideEntityFactory',
            'GuideTable' => 'tlslib\db\GuideTableFactory',
        )
    )
);
