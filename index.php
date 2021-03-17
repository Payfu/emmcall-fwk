<?php
declare(strict_types=1);

use Core\Router\Routing;

/**
* Dispatcheur
*/
define('ROOT', dirname(__FILE__));
define('WEBROOT', 'https://'.$_SERVER['HTTP_HOST']. str_replace('/index.php', '', $_SERVER['PHP_SELF']));

/**
 *  On charge le Singleton + les variables d'environnement 
 */
require ROOT . '/core/App/App.php';
require ROOT . '/config/env.php';

/**
 *  Si dev, alors on affiche les erreurs
 */
if(ENV === 'dev'){ ini_set('display_errors', '1'); }

/**
 *  On appel la méthode statique Load()
 */
App::Load();

/**
 *  On récupère l'url
 */
$page = $_GET['url'] ?? '';

/**
 *  Constante FULL_URI : contient la valeur passer en GET
 */
define('FULL_URI', $page);

/**
 *  On indique le chemin du dossier où les fichiers de routes sont répertoriés
 */
$ymlFile = ROOT."/App/Routes";
$routing = new Routing($ymlFile, $page);
$routing->routeManager();