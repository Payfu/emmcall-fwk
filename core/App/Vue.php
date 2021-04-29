<?php
namespace core\App;

use core\Router\Route;
use core\Controller\VueController;
use core\Router\Routing;
/**
 * Description of Vue
 * Vue est le moteur de rendu html, il permet de générer des variables pour les page vues html
 * @author Emmanuel Callec
 */
class Vue extends VueController
{
  private static $_view;
  private static $_variables = [];
  private static $_template_name = null;
  private static $_ymlFile = ROOT."/App/Routes/";
  
  // On initialise la vue avec le nom du fichier se trouvant dans NomBundle/Views/$fileName
  public static function init(string $fileName){ 
    self::$_view = $fileName;
  }
  
  // Chaque variable est ajouté au tableau
  public static function var(string $varName, mixed $value){ 
    self::$_variables[$varName] = $value;
  }
  
  // On place dans un tableau les scripts (css, js, json) sous la forme [url, nomfichier.ext, NomDuBundle::Fichier.ext]
  public static function scripts(array $array = []){
    if(empty($array)){
      die("Attention: vous n'avez pas indiqué de script dans <pre>Vue::scripts</pre>");
    }
    $v = new VueController(Route::$_current_bundle);
    
    // On fusionne les tableaux
    self::$_variables = $v->scriptsManager($array) + self::$_variables;
  }
  
  // Modification du template
  public static function template(string $templateName){
    self::$_template_name = $templateName;
  }
  
  /*
   * Lance le render mais pour retourner uniquement le HTML qu'il faudra stocker dans une variable
   */
  public static function html(string $view = '', $fromBundleName = false){
    if($view === ''){
      die("Attention: vous n'avez pas indiqué de vue dans <pre>Vue::html('nomDeMaVue', 'nomBundle'(facultatif));</pre>");
    }
    // La propriété static $_current_bundle est crée dans core/Router/Route.php
    $bundleName = $fromBundleName ? $fromBundleName : Route::$_current_bundle ;
    $v = new VueController($bundleName);
    return $v->render($view, self::$_variables, true); 
  }
  
  /*
   * On tape le nom d'une route et après avoir récupèré le chemin "path" il redirige automatiquement
   * @routeName = nom de la route
   * @$params = tableau des paramètres GET de la route
   */
  public static function redirect(string $routeName, array $params = []){
    $r  = new Routing(self::$_ymlFile, null);
    $r->redirectManager($routeName, $params, true);
    exit;
  }
  
  /**
   * On crée un lien à partir d'une route et on l'intègre parmi les variables à afficher dans la page lorsque la méthode execute est lancée
   * @param string $varName = nom de la variable à appeler dans la page html
   * @param string $routeName = nom de la route
   * @param array $params = paramètres (GET) de la route 
   * @param type $str = si true alors on n'ajoute pas à la liste des variables mais on peut le sortir vers une variable externe
   *             exemple: Vue::link(routeName:"privateIndex", str:true);
   */
  public static function link(string $varName = "", string $routeName = "", array $params = [], $str = false){
    $r  = new Routing(self::$_ymlFile, null);
    if($str){
      return $r->redirectManager($routeName, $params, false);
    }
    if($varName == "" || $routeName == ""){
      die("Attention: Les valeurs de Vue::link semblent incorrectes, vous devez indiquer un nom de variable et de route");
    }
    self::$_variables[$varName] = $r->redirectManager($routeName, $params, false);
  }
  
  /*
   *  Cette méthode lance le render
   *  Elle nécessite d'avoir __CLASS__ comme paramètre depuis le controller afin de savoir de quel bunble il est question
   */
  public static function execute(){
    // La constante CURRENT_BUNDLE est crée dans core/Router/Route.php
    $bundleName = Route::$_current_bundle ;
    $v = new VueController($bundleName, self::$_template_name);
    $v->render(self::$_view, self::$_variables);    
  }
}
