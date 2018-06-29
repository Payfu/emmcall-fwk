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
  private $_path;
  private $_defaults;
  private $_controller;
  private $_method;
  private $_params = [];
  private $_url;

  public function __construct(string $fileName, $url)
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
    
    // On crée les routes
    $this->createRoute();  
  }
  
  private function checkParams() : bool {
    $array = $this->_array;
        
    foreach($array as $k => $v)
    {
      
      $routeName = $k;
      $tabRoute = $v;
      // Gestion du PATH
      if( empty($tabRoute['path']) ){ $this->alertMsg("path", $routeName); } else { $this->_path = $tabRoute['path']; }

      if( empty($tabRoute['defaults']) ){ $this->alertMsg("defaults", $routeName); } else {   $this->_defaults = $tabRoute['defaults']; }

      if( empty($tabRoute['defaults']["_controller"]) ){ $this->alertMsg("_controller", $routeName); } else { $this->_controller = $tabRoute['defaults']["_controller"]; }

      // On regarde si requirements existe sinon la methode est = GET
      if(array_key_exists('requirements', $tabRoute)){

        // Si c'est le cas, on vérifie si _method et déféni, si ce n'est pas le cas alors c'est un GET par défaut
        if( !empty($tabRoute['requirements']["_method"]) ){ $this->_method = $tabRoute['requirements']["_method"]; } else { $this->_method = 'GET'; }

        // je vérifie toute les clefs qui ne commencent pas par "_", ce sont alors des noms de paramètre
        foreach($tabRoute['requirements'] as $nomParam => $valParam){

          //$nomParam[0] = premier caractère
          if($nomParam[0] != '_'){
            // Le nom paramètre clef ne peut pas être sans valeur
            if(empty($valParam)){ $this->alertMsg($nomParam, $routeName, 1); }

            // Le nom paramètre clef doit aussi se retrouver dans la chaîne de path, ex : {nomParam}
            if(!preg_match("/{{$nomParam}}/i", $tabRoute['path'])){ $this->alertMsg($nomParam, $routeName, 2); } else {
              $this->_params[$nomParam] = $valParam;
            }
          }
        }
      } 
      // sinon _method = GET
      else { $this->_method = 'GET'; }
      
      return true;
    }
  }
  
  /*
   * Converti place les donnée de route dans les méthodes php et appelle les bundles associé
   */
  private function createRoute(){
    $router = new Router($this->_url);
    $method = strtolower($this->_method);
    
    // On remplace /test/{username}/page/{id} => /test/:username/page/:id pour la gestion des paramètres
    $path  = str_replace('{', ':', str_replace('}', '', $this->_path));
    
    if($method === "get"){
      $laRoute = $router->get($path, $this->_controller);//->with('username', "[a-zA-Z]{2,150}")->with('id', "[0-9]{2,150}");
    } else {
      //$router->post('/test', "Test:Index:maMethode");
      $laRoute = $router->post($path, $this->_controller);
    }
    
    // Normalement la méthode with() est chaînée, mais ici je l'ajoute dynamiquement
    // Gestion de paramètres 
    if(count($this->_params) > 0){
      foreach ($this->_params as $nomParam => $regex) {        
        $laRoute->with($nomParam, $regex);
      }
    }
    
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
  public function getListeRoute(): string {
    $list = null;
    foreach($this->_array as $k => $v){
      $list .= "[{$k}] : {$v['path']} <br />";
    }
    return $list;
  }
}