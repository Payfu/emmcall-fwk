<?php
declare(strict_types=1);

use Core\Router\Routing;

/**
* Dispatcheur
*/
define('ROOT', dirname(__FILE__));
define('WEBROOT', 'https://'.$_SERVER['HTTP_HOST']. str_replace('/index.php', '', $_SERVER['PHP_SELF']));

// On charge le Singleton
require ROOT . '/app/App.php';
require ROOT . '/config/env.php';

// Si dev, alors on affiche les erreurs
if(ENV === 'dev'){  ini_set('display_errors', '1'); }

// On appel la méthode statique Load()
App::Load();

// On récupère l'url
$page = isset($_GET['url']) ? $_GET['url'] : '';

// Constante : contient la valeur passer en GET
define('FULL_URI', $page);

// On indique le chemin du fichier où les routes sont répertoriées
$ymlFile = ROOT."/app/Routes/routes.yml";
$routing = new Routing($ymlFile, $page);
$routing->routeManager();

// Nous sommes en dev, une ligne rouge apparaît
// Par principe on place les paramètres de bootstrap
if(ENV === 'dev'){
  echo '<div class="col bg-danger" style="background-color:#f00; position: fixed;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 7px;
        z-index: 99999;
        ">
        </div>';
}
