<?php
namespace core\Router;

use core\Controller\Controller; 

class Router extends Controller {
  private $_url;
  private $_routes = []; // contient l'ensemble des routes
  private $_namedRoutes = []; // Nom de la route
  
  public function __construct($url){
    $this->_url = $url;
  }
  
  // Récupération des url en GET
  public function get($path, $callable, $name = null){
    return $this->add($path, $callable, $name, 'GET');
  }
  
  // Récupération des url en POST
  public function post($path, $callable, $name = null){
    return $this->add($path, $callable, $name, 'POST');
  }
  
  public function double($path, $callable, $name = null){
    return $this->add($path, $callable, $name, 'DOUBLE');
  }
  
  public function add($path, $callable, $name, $method){
    // L'objet route permet de travailler plus simplement
    $route = new Route($path, $callable);
    
    // On crée un tableau indexé par la méthode GET, POST ou DOUBLE
    $this->_routes[$method][] = $route;
    
    // Si le $callable est une chaîne alors on en tire le nom de la route
    if(is_string($callable) && $name === null){
      $name = $callable;
    }
    
    // S'il y a un nom
    if($name){
      $this->_namedRoutes[$name] = $route;
    }
    
    // On retourne une instance de $route
    return $route;
  }
  
  /*
   *  Vérifie si l'url tapée en paramètre correspond à l'une des urls
   */
  public function run(){
    // Si je ne connaît pas la méthode retournée je retourne une Exception
    if(!isset($this->_routes[$_SERVER['REQUEST_METHOD']]) && !isset($this->_routes['DOUBLE'])){
      throw new RouterException("<p><strong>REQUEST_METHOD n'existe pas, si vous avez choisi 'POST' la page devrait donc être appellée via _POST. </strong><br />");
    } 
    
    // Traite les données en fonction des méthodes GET, POST ou DOUBLE
    foreach (['GET','POST','DOUBLE'] as $method) {
      if(isset($this->_routes[$method]) && $this->_routes[$method]){
        // Pour chaque route je check si l'url correspond à la route saisie
        foreach ($this->_routes[$method] as $route){
          // Correspondance trouvée, on appel la méthode call
          if($route->match($this->_url)){ return $route->call(); } 
        }
      }
    } 
    
    // Rien c'est trouvé on retourne une erreur.
    // On récupère l'URL complète à afficher dans le message d'erreur.    
    $url = WEBROOT.'/'.FULL_URI;
    // Je ne trouve aucune correspondance, je retourne une exception 404
    if (class_exists("\core\Controller\Controller")) {
      \core\Controller\Controller::notFound($url); // Ne marche pas
    }
    else {
      throw new RouterException("<p><strong>Aucune route trouvée => <span style='color:#FF0000';>{$url}</span></strong></p>");
    }
  }
  
  public function url(string $name, array $params = []){
    // Si aucune route ne correspond à ce nom
    if(!isset($this->_namedRoutes[$name])){
      throw new RouterException("<p><strong>Aucune route n\'a été trouvé à ce nom => <span style='color:#FF0000';>{$name}</span> </strong></p>");
    }
    // Sinon
    return $this->_namedRoutes[$name]->getUrl($params);
  } 
}