<?php
/**
 * IMPORTANT : Chaque nom de fichier doit correspondre au nom de la classe qu'il contient.
 */
namespace Manager;

class Autoloader {
  static function register(){
    /* 
       * Le premier paramètre du tableau est le nom de la classe courante
       * Le second est le nom de la méthode à appeler.
       */
    spl_autoload_register(array(__CLASS__,'autoload'));
  }
    
  /**
    * 
    * Cette méthode statit autoload a été convertie en __autoload() grâce à spl_autoload_register
    * $class n'est autre que le nom des classes appelées dans les scripts -> new nomClass();
    */
  static function autoload($class){
    // S'il n'existe pas on défini le chemin
    if(!defined('ROOT')){
      define('ROOT', dirname(__FILE__));
    }
    // Si Core (avec majuscule) alors on doit faire quelques manipulation pour récupérer les class en dehors du dossier Manager
    if(str_contains($class, 'core')){
      $class = str_replace('core\\Manager', '', ROOT . lcfirst($class)) . ".php" ;
    } else {
      $class = ROOT .'/'. $class . '.php';
    }
    require_once $class;
  }
}
Autoloader::register();