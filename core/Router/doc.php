<?php
// Définition de la fonctionnalité

// Création du router
$router = new App\Router($_GET['url']);

// Lorsque j'appelle l'url /post, je veux que tu me lance une fonction qui m'affichera "tous les articles"
$router->get('/post', function(){  echo 'tous les articles'; });

// Dans le cas d'une url avec un paramètres, il faudra ajouter /:nomParam qui retournera le paramètre à la fonction annonyme
$router->get('/post/:id', function($id){  echo 'afficher l\'article ' . $id; });

// Si je soummet des informations
$router->post('/post/:id', function($id){  echo 'poster pour l\'articles ' . $id; });

// Est-ce que ce qui a été tapé correspond à l'une des url ?
$router->run();


////////////////////
// Le htaccess : http://cbsa.com.br/tools/online-convert-htaccess-to-web-config.aspx
////////////////////
// Si jamais le fichier demandé n'existe pas, alors tu passes à la suite
// RewriteCond %{REQUEST_FILENAME}% !-f
//
// Tu réécrit tout et tu retourne en paramètre à index (QSA Transfase aussi les variables en get)
// RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

/*
 * Version webconfig
  <rule name="rule 1x" stopProcessing="true">
	<match url="^(.*)$"  />
	<action type="Rewrite" url="/index.php?url={R:1}"  appendQueryString="true" />
  </rule>
 */


// IMPORTANT : Toujours indiquer les routes le plus précises en premier
// Il faudrait aussi penser $router->delete() et $router->put()


// Racine du site
//$router->get('/', function(){ echo "Home Page"; });

// Important : lordre des paramètre doit être le même dans la méthode
//$router->get('/test', "Test#Index#maMethode");
//$router->get('/:slug-:id', "Home#index")->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');

// Lorsque j'appelle l'url /post, je veux que tu me lance une fonction qui m'affichera "tous les articles"
//$router->get('/post', function(){  echo 'tous les articles'; });

// Dans le cas d'une url avec un paramètres, il faudra ajouter /:nomParam qui retournera le paramètre à la fonction annonyme
// Je réalise un chaînage avec la méthode with auquel je donne le nom du paramètre et j'indique les contraintes afin de ne pas passer n'importe quoi
// je nôme mon url : post.show
// Je veux afficher une url, je modifie le echo
//$router->get('/article/:id-:slug', function($id, $slug ) use ($router)  {  echo $router->url('Home#index'); }, 'post.show')->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');
//$router->get('/article/:id-:slug', function($id, $slug ) use ($router)  {  echo $router->url('Home#index'); })->with('id', '[0-9]+')->with('slug', '[a-z\-0-9]+');

// Je veux une route qui appelle un controleur et une méthode, il faut une petite modif dans la methode Route::call() : Post#show
// Post#show sera également le nom à attribuer à ma route, ça se gère dans la methode Router::add()
//$router->get('/article/:id-:slug', "Post#show");



// Si je soummet des informations
//$router->post('/post/:id', function($id){  echo 'poster pour l\'articles ' . $id; });

// Est-ce que ce qui a été tapé correspond à l'une des url ?
//$router->run();
