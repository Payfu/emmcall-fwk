<?php
namespace Core\Router;

use Core\Yaml\YamlParser;
use Core\Router\Router;

/**
 * Description of Routing
 * C'est ici que commence le système de routage
 * @author dev.hrteam
 */
class Routing extends YamlParser
{
  private $_array = [];
  private $_url;

  public function __construct(string $fileName, $url = null)
  {
    parent::__construct($fileName);
    // On récupère le tableau formé à partir de YML
    $this->_array = $this->getArray();
    // On récupère l'url
    $this->_url = $url;    
    
  }
  
  public function routeManager(){
    // On contrôle les données
    $this->checkParams(); 
  }
  
  /*
   * Redirection 
   */
  public function redirectManager($routeName, $params = null, $redirect = true){
    // On récupère le path de la route du nom X
    if(!array_key_exists($routeName, $this->_array) ){
      die("La route n'existe pas : <strong>{$routeName}</strong>");
    }
    
    $routes = $this->_array;
    $route = $routes[$routeName];
    $path = $route["path"];
    $chemin  = WEBROOT."{$path}";
    
    // Remplace les valeurs {val} etc. par les valeur du tableau params, ex : /cdsadmin/board/{username}/{id} => /cdsadmin/board/blabla/123
    if($params){
      foreach($params as $k=>$v){
        $patterns[] = '/{'.$k.'}/';
        $replacements[] = $v;
      }

      $chemin = preg_replace($patterns, $replacements, $chemin);
    }
    
    // On lance la redirection
    if($redirect){
      header("Location: {$chemin}");
    } else {
      return $chemin;
    }
    
  }
  
  /*
   * Contrôle tous les paramètres de toutes les routes
	 * Puis on lance le router
	 * Enfin on lance la route
   */
  private function checkParams() {
    $arrayRoutes = $this->_array;
    
    /**
      * Etape 1
      * Gestion du PATH
      * On check l'ensemble des routes et des infos indiqués
      */
    foreach($arrayRoutes as $k => $v)
    {
      $routeName = $k;
      $tabRoute = $v;
			
      if( empty($tabRoute['path']) ){ $this->alertMsg("path", $routeName); }
      if( empty($tabRoute['defaults']) ){ $this->alertMsg("defaults", $routeName); }
      if( empty($tabRoute['defaults']["_controller"]) ){ $this->alertMsg("_controller", $routeName); }
      // On regarde si requirements existe sinon la methode est = GET
      if(array_key_exists('requirements', $tabRoute)){
        // je vérifie toute les clefs qui ne commencent pas par "_", ce sont alors des noms de paramètre
        foreach($tabRoute['requirements'] as $nomParam => $valParam){
          //$nomParam[0] = premier caractère
          if($nomParam[0] != '_'){
            // Le nom paramètre clef ne peut pas être sans valeur
            if(empty($valParam)){ $this->alertMsg($nomParam, $routeName, 1); }
            // Le nom paramètre clef doit aussi se retrouver dans la chaîne de path, ex : {nomParam}
            if(!preg_match("/{{$nomParam}}/i", $tabRoute['path'])){ $this->alertMsg($nomParam, $routeName, 2); }
          }
        }
      } 
    }
    
    /**
     * Etape 2
     * Gestion du PATH
     * On check l'ensemble des routes et des infos indiqués
     */	
    // On instancie le router
    $router = new Router($this->_url);
    
    // On boucle sur le yaml et on récupère les infos utiles pour la génération des routes.
    foreach($arrayRoutes as $k => $v){
			
      $routeName = $k; // ex: nom_de_ma_route
      $tabRoute = $v; // array
      $params = [];
      
      // On remplace /test/{username}/page/{id} => /test/:username/page/:id pour la gestion des paramètres
      $path  = str_replace('{', ':', str_replace('}', '', $tabRoute['path']));
      $controller = $tabRoute['defaults']["_controller"]; 
	
      // Si requirements est renseigné
      if(array_key_exists('requirements', $tabRoute)){        
        // On récupère la valeur facultative _method (GET par defaut)
        $method = !empty($tabRoute['requirements']["_method"]) ? $tabRoute['requirements']["_method"] : 'GET'; 
        // je vérifie toute les clefs qui ne commencent pas par "_", ce sont alors des noms de paramètre
        foreach($tabRoute['requirements'] as $nomParam => $valParam){
          //$nomParam[0] = premier caractère					
          if($nomParam[0] != '_'){
            // Le nom paramètre clef doit aussi se retrouver dans la chaîne de path, ex : {nomParam}
            $params[$nomParam] = $valParam; 
          }
        }
      } else { $method = 'GET';	}
      
      // Si get sinon post, ex : $router->get('/test', "Test:Index:maMethode");
      //$laRoute = (strtolower($method) === "get") ? $router->get($path, $controller) : $router->post($path, $controller);
	
      // Soit GET, soit DOUBLE, soit POST
      if(strtolower($method) === "get"){
        $laRoute = $router->get($path, $controller);
      } 
      if (strtolower($method) === "double"){
        $laRoute = $router->double($path, $controller);
      } 
      if (strtolower($method) === "post") {
        $laRoute = $router->post($path, $controller);
      }
            
      // Gestion de paramètres 
      // Normalement la méthode with() est chaînée, mais ici je l'ajoute dynamiquement
      if(count($params) > 0){
        foreach ($params as $nomParam => $regex) {        
          $laRoute->with($nomParam, $regex);
        }
      }
    } // endForeach
		
    // On lance la route
    $router->run();
  }
	
  /*
   * Messages d'erreur de syntaxe
   */
  private function alertMsg(string $value, string $route, int $param = 3){
    if($param === 1){
      echo "Le paramètre <strong>\"{$value}\"</strong> n'est pas renseigné pour la route : <strong>{$route}</strong>";
    } elseif ($param === 2) {
      echo "Le paramètre <strong>\"{$value}\"</strong> n'existe pas dans le 'PATH' de la route : <strong>{$route}</strong>";
    } else {
      echo "La valeur <strong>\"{$value}\"</strong> n'est pas renseigné pour la route : <strong>{$route}</strong><br />";
    }
    exit();
  }
  
  /*
   * On récupère la liste des routes, ex :
   *    [nom_de_ma_route] : /recettes/{username}/cat/{tag}.html 
   *    [nom_de_ma_route2] : /recettes/{username}/cat/eye/ok.html 
   * 
   * return @string
   */
  public function getListeRoute() : string {
    $list = null;
    foreach($this->_array as $k => $v){
      $list .= "[{$k}] : {$v['path']} <br />";
    }
    return $list;
  }
}
