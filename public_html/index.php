<?php

define('ROOT_PATH', __DIR__.'/../');
define('APP_PATH', ROOT_PATH.'app/');
define('PUBLIC_PATH', __DIR__.'/');
define('LIB_PATH', ROOT_PATH.'library/');

$config = array();

if (isset($_SERVER['CONFIG_MAIN_FILE']) && file_exists($_SERVER['CONFIG_MAIN_FILE'])) {
    $config = include_once($_SERVER['CONFIG_MAIN_FILE']);
} else {
    echo 'Unable to load config file';
    die;
}

//-
error_reporting($config['errors']['level']);
ini_set('display_errors', $config['errors']['display']);

//-
date_default_timezone_set($config['application']['timezone']);

//-
define('ASSETS_URL', $config['assets']['url']);
define('BASE_URL', $config['application']['baseUrl']);


try {

	$di = new \Phalcon\DI\FactoryDefault();

	// The URL component is used to generate all kind of urls in the application
	$di->set('url', function() use ($config) {
		$url = new \Phalcon\Mvc\Url();
		$url->setBaseUri($config['application']['baseUrl']);
		return $url;
	});

    // Defining the APC adapter to improve the php and phalcon models speed
    $di->set('modelsMetadata', function() {
        // Create a meta-data manager with APC
        $metaData = new \Phalcon\Mvc\Model\MetaData\Apc(array(
            'lifetime' => 86400,
            'suffix' => 'my_application'
        ));

        return $metaData;
    });


	// Database connection is created based in the parameters defined in the configuration file
	$di->set('db', function() use ($config) {

		$connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
			'host' => $config['database']['general']['host'],
			'username' => $config['database']['general']['username'],
			'password' => $config['database']['general']['password'],
			'dbname' => $config['database']['general']['dbname'],
            'port'  => $config['database']['general']['port']
		));

        return $connection;
	});


    // Setting the views path
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir($config['application']['viewsPath']);
    $di->set('view', $view);


    // Registering an auto-loader
	$loader = new \Phalcon\Loader();

    $loader->registerNamespaces(
        array(
            'Modules'  => APP_PATH.'modules/',
            'Classes'   => APP_PATH.'classes/',
        )
    );

    $loader->register();


    class Application extends \Phalcon\Mvc\Micro
    {
        public static $config = array();
    }

    // Starting the application
    $app = new \Classes\General\Application();
    $app->setDI($di);
    $app::$config = $config;

    $app->setDefaultModule('site');

    $app->get('/', function() use ($app) {
        echo 'Im at home';
    });

	$app->notFound(function() use ($app){
        echo 'oops!!, another page missing';
	});

    $app->start();

} catch (Phalcon\Exception $e) {
    var_dump('E: '.$e);
} catch (PDOException $e){
    var_dump('E: '.$e);
} catch (Exception $e) {
    var_dump('E: '.$e);
}
