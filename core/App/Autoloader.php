<?php
/**
 * IMPORTANT : Chaque nom de fichier doit correspondre au nom de la classe qu'il contient.
 */
namespace App;

class Autoloader {
    
  static function register()
  {
    /* 
       * Le premier paramètre du tableau est le nom de la classe courante
       * Le second est le nom de la méthode à appeler.
       */
    spl_autoload_register(array(__CLASS__,'autoload'));
  }
    
  /**
    * 
    * Cette méthode static autoload a été convertie en __autoload() grâce à spl_autoload_register
    * $class n'est autre que le nom des classes appelées dans les scripts, ex: $c = new nomClass();
    */
  static function autoload($class)
  { 
    // Les sous-namespace sont convertis en chaîne UNIX
    $class = str_replace('\\', '/', $class);
    // On crée les chemins vers les bundles et le vendor
    $pathBundle = ROOT . "/{$class}.php";
    $pathVendor = ROOT . "/vendor/{$class}.php";
    // Si le fichier n'existe pas on tente de le charger via le dossier vendor sinon on affiche un message d'erreur
    if(file_exists($pathBundle))      { require_once $pathBundle; } 
    else if(file_exists($pathVendor)) { require_once $pathVendor; } 
    else                              { die("La classe <b>{$class}</b> n'existe pas."); }
  }
}
/**
 * On charge l'autoloader
 */
Autoloader::register();