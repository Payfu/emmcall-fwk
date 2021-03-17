<?php
namespace core\Router;

/**
 * Description of Route
 *
 * @author Emmanuel CALLEC
 * Permet de représenter une route.
 */
class Route
{
  private $path;
  private $callable;
  private $matches = [];
  private $params = [];
  public static $_current_bundle;
  
  public function __construct($path, $callable)
  {
    $this->path = trim($path, '/');
    $this->callable = $callable;
  }

  public function with($param, $regex){
    // Dans le cas où une personne placerait une contrainte entre ( ) alors qu'il n'en est pas utile, je lui demande de le remplacer par (?, il ne tiendra plus compte de ces ( )
    // De plus on n'utilise jamais les ( ) dans une url donc aucun risque de faire sauter l'url
    $this->params[$param] = str_replace('(', '(?:', $regex);
    
    // Ce retour permet le chaînage (ou "Fluence") avec with
    return $this;
  }
  
  // Est-ce qu'une route est trouvée
  public function match($url){
    // Suppression des "/" superflux
    $url = trim($url, '/');
    
    // Récupère les paramètres ":param" et les transforme en expression régulière
    // ..._callback permet de faire appel à la méthode paramMatch
    $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
    $regex = "#^$path$#i";
    
    // l'url ne correspond pas au regex, alors on retourne false
    if(!preg_match($regex, $url, $matches)){ return false; }
    
    // array_shift dégage le premier paramètre
    array_shift($matches);
    
    // On enregistre le résultat dans une variable privé
    $this->matches = $matches;
    
    // L'url correspond
    return true;
  }
  
  /*
   * Méthode appelée depuis la méthode match()
   */
  private function paramMatch($match){
    // si dans mes paramètre, j'ai un paramètre qui correspond.
    if(isset($this->params[$match[1]])){
      // je retourne cette expression régulière
      return '('.$this->params[$match[1]].')';
    }
    // Sinon on retourn le regex de base
    return '([^/]+)';
  }
  
  /*
   * Récupère le paramètre $this->callable qui retourne la chaîne: Bundle:Controller:Method 
   */
  public function call(){
    // Si la méthode appelée est une chaîne on initialise un bundle et son contrôleur
    // ex : Bundle:Controller:Method 
    if(is_string($this->callable)){
      $params = explode(':', $this->callable);
           
      // Si $param = 3 il y a un bundle, si c'est 2 alors  il n'y en a pas
      if (count($params) == 3){
        // IMPORTANT: Cette propiété static 'current_bundle' est utilisée dans core/App/Vue.php
        self::$_current_bundle = ucfirst($params[0]);
        // On appelle le bundle
        $controller = "App\\Src\\" . ucfirst($params[0]) . "Bundle\\Controller\\" . ucfirst($params[1]) . "Controller";
        $method = $params[2];
      } else {
        die("<p>Les paramètres suivant ne sont pas corrects : <pre><span style='color:#FF0000';>{$this->callable}</span></pre> Il doit y avoir 3 paramètres : <pre>Bundle:Controller:method</pre></p>");
      }
      
      // Si la class $controller existe
      if (class_exists($controller)) {
        // Si la méthode $method existe
        if(method_exists($controller,$method)){
          // On initialise le contrôleur
          $controller = new $controller();
          // On appelle la méthode
          return call_user_func_array([$controller, $method], $this->matches);  
        } else {
          die("<p>La méthode suivante n'a pas été trouvée : <pre>{$method}</pre> Dans la classe : <pre>{$controller}</pre> Pour les paramètres de route suivants :<pre>{$this->callable}</pre></p>");
        }
      }else{
        die("<p>La classe suivante n'a pas été trouvée : <pre>{$controller}</pre></p>");
      }
    }
    // On appel la fonction qui se trouve dans la route, ex : $router->get('/post', function(){  echo 'tous les articles'; });
    // Et on passe en paramètre l'ensemble des correspondances du tableau $matches
    // Encore utile ? Manu
    else {
      exit("Voir Router/Route.php Ligne:110");
      //return call_user_func_array($this->callable, $this->matches);
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