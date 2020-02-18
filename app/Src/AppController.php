<?php
declare(strict_types=1); // Déclanche une erreur en cas de scalaire incorrect

namespace App\src;

use Core\Controller\Controller;
use App;

/**
 * Description of AppController pour les Bundles
 * Cette page est importante car elle détermine l'url de views et le nom de la page du template
 * Ces variables son appelées dans Core\Controller\Controller
 * @author EmmCall
 */
class AppController extends Controller
{
  
  /**
   * Initialisation des variables
   * Toutes ces variable sont lu dans core/controller/controller.php
   * @param1 = nom de la classe enfant
   * @param2 = nom du template cible
   */
  private $currentClass;
  protected $template = 'default';
  
  
  public function __construct($classChild, $newTemplate = null)
  {
    if(!is_null($newTemplate)){ $this->template = $newTemplate; }
    $this->currentClass = $this->getBundleName($classChild);
    $this->viewPath     = ROOT    . '/app/src/'.$this->getBundleName($classChild).'/Views/';
    $this->templatePath = ROOT    . '/app/Views/';
    $this->jsPath       = WEBROOT . '/scripts/'.$this->getBundleName($classChild, true).'/js/';
    $this->cssPath      = WEBROOT . '/scripts/'.$this->getBundleName($classChild, true).'/css/';
  }

  /*
    * Cette methode magique me permet de récupérer le nom de la clef de la base et de la table souhaité dans les contrôleurs
    */
  public function __call(string $nomBase, array $nomTable){
    $nomBase = strtolower(explode("load", $nomBase)[1]);
    $nomTable = $nomTable[0];

    if(in_array($nomBase, array_keys(App::getInstance()->getDatabases()))){
      $this->$nomTable = App::getInstance()->getTable($nomTable, $nomBase, $this->currentClass);
    } else {
      die("Clef BDD ({$nomBase}) incorrecte !");
    } 
  }
  
  /**
   * Récupère le nom de la classe sans le chemin, ex : App\src\TestBundle\Controller\IndexController => TestBundle;
   * @withoutName = bool, si true alors TestBundle = Test
   * return @string
   */
  private function getBundleName(string $classChild, $withoutName = false) : string {
    if($withoutName == false){
      return explode('\\', $classChild)[2];
    } else {
      return preg_replace("/Bundle/", "", explode('\\', $classChild)[2]);
    }
  }
}
