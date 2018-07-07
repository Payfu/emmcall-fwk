<?php
namespace Core\Router;

class Router {
  private $url;
  private $routes = []; // contient l'ensemble des routes
  private $namedRoutes = []; // Nom de la route
  
  public function __construct(string $url){
    $this->url = $url;
  }
  
  // Récupération des url en GET
  public function get($path, $callable, $name = null){
    return $this->add($path, $callable, $name, 'GET');
  }
  
  // Récupération des url en POST
  public function post($path, $callable, $name = null){
    return $this->add($path, $callable, $name, 'POST');
  }
  
  public function add($path, $callable, $name, $method){
    // L'objet route permet de travailler plus simplement
    $route = new Route($path, $callable);
    
		//print_r($route);
    // On crée un tableau indexé par la méthode GET ou POST
    $this->routes[$method][] = $route;
    
    // Si le $callable est une chaîne alors on en tire le nom de la route
    if(is_string($callable) && $name === null){
      $name = $callable;
    }
    
    // S'il y a un nom
    if($name){
      $this->namedRoutes[$name] = $route;
    }
    // On retourne une instance de $route
    return $route;
  }
  
  // Vérifie si l'url tapée en paramètre correspond à l'une des urls
  public function run(){
    
    // J'ai besoin de connaître la méthode retournée sinon je retourne une Exception
    if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
      throw new RouterException("<p><strong>REQUEST_METHOD n'existe pas, si vous avez choisi 'POST' la page devrait donc être appellée via _POST. </strong><br />");
    }
    
    // Je connais la méthode alors je sort les routes
    foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
      
      
      // Pour chaque route je check si l'url correspond à la route tapée
      if($route->match($this->url)){
        // Correspondance trouvée, on appel la méthode call
        return $route->call();
      }
    }
    
    // Je ne trouve aucune correspondance, je retourne une exception
    throw new RouterException("<p><strong>Aucune route trouvée</strong></p>");
  }
  
  public function url(string $name, array $params = []){
    // Si aucune route ne correspond à ce nom
    if(!isset($this->namedRoutes[$name])){
      throw new RouterException('<p><strong>Aucune route n\'a été trouvé à ce nom</strong></p>');
    }
        
    // Sinon
    return $this->namedRoutes[$name]->getUrl($params);
  }
  
}

