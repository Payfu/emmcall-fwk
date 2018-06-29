<?php

namespace Core\Router;

/**
 * Description of Route
 *
 * @author dev.hrteam
 * Permet de représenter une route.
 */
class Route
{
  private $path;
  private $callable;
  private $matches = [];
  private $params = [];
  
  public function __construct($path, $callable)
  {
    $this->path = trim($path, '/');
    $this->callable = $callable;
  }
  
  public function with($param, $regex){
    // Dans le cas où une personne placerait une contraite entre ( ) alors qu'il n'en est pas utile, je lui demande de le remplacer par (?, il ne tiendra plus compte de ces ( )
    // De plus on n'utilise jamais les ( ) dans une url donc aucun risque de faire sauter l'url
    $this->params[$param] = str_replace('(', '(?:', $regex);
    
    // Ce retour permet le chaînage (ou "Fluence") avec with
    return $this;
  }
  
  // Est-ce qu'une route est trouvée
  public function match($url){
    // Je veux supprimer les /
    $url = trim($url, '/');
    
    // Je veux récupérer les paramètres ":param" et les transformer en expression régulière
    // ..._callback permet de faire appel à la méthode paramMatch
    
    $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
    $regex = "#^$path$#i";
    
    if(!preg_match($regex, $url, $matches)){
      // l'url ne correspond pas
      return false;
    }
    // array_shift dégage le premier paramètre
    array_shift($matches);
    
    // On enregistre le résultat dans une variable privé
    $this->matches = $matches;
    
    // L'url correspond
    return true;
  }
  
  private function paramMatch($match){
    // si dans mes paramètre, j'ai un paramètre qui correspond.
    if(isset($this->params[$match[1]])){
      // je retourne cette expression régulière
      return '('.$this->params[$match[1]].')';
    }
    
    // Sinon on retourn le regex de base
    return '([^/]+)';
  }
  
  public function call(){
    // Si la méthode appelée est une chaîne on initialise un bundle et / ou un contrôleur
    // ex : Bundle:Controller:Method  OU  Controller#Method
    if(is_string($this->callable)){
      
      $params = explode(':', $this->callable);
      
      // Si $param = 3 il y a un bundle, si c'est 2 alors  il n'y en a pas
      if(count($params) == 3){
        // On appelle le bundle
        $controller = "App\\Src\\" . ucfirst($params[0]) . "Bundle\\Controller\\" . ucfirst($params[1]) . "Controller";
        $method = $params[2];
      }
      
      if(count($params) == 2){
        // On appelle le contrôleur
        $controller = "App\\Controller\\". $params[0] . "Controller";
        $method = $params[1];
      }
      
      if (class_exists($controller)) {
        // On initialise le contrôleur
        $controller = new $controller();
        // On appelle l'action (ou la méthode)
        return call_user_func_array([$controller, $method], $this->matches);   
      }else{
        throw new RouterException("La classe suivante n'est pas trouvée : {$controller}");
      }
    }
    // On appel la fonction qui se trouve dans la route, ex : $router->get('/post', function(){  echo 'tous les articles'; });
    // Et on passe en paramètre l'ensemble des correspondances du tableau $matches
    else {
      return call_user_func_array($this->callable, $this->matches);
    }
    
  }
  
  public function getUrl($params){
    $path = $this->path; 
    
    foreach ($params as $k => $v) {
      $path = str_replace(":{$k}", $v, $path);
    }
    
    return $path;
  }
}
