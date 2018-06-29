<?php
declare(strict_types=1);

// ini_set('display_errors', '1');
use Core\Router\Routing;

/**
* Dispatcheur
*/
define('ROOT', dirname(__FILE__));
define('WEBROOT', 'https://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

// On charge le Singleton
require ROOT . '/app/App.php';

// On appel la méthode statique Load()
App::Load();


// On récupère l'url
$page = $_GET['url'] ?? '';

// On indique le chemin du fichier où les routes sont répertoriées
$ymlFile = ROOT."/app/Routes/routes.yml";
$routing = new Routing($ymlFile, $page);
$routing->routeManager();