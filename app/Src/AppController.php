<?php
declare(strict_types=1); // Déclanche une erreur en cas de scalaire incorrect

namespace App\src;

use Core\Controller\Controller;
use Core\Auth\DBAuth;
use App\Controller\UserController;
use App;


/**
 * Description of AppController pour les Bundles
 * Cette page est importante car elle détermine l'url de views et le nom de la page du template POUR LES BUNDLES
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
    $this->jsPath       = WEBROOT . '/scripts/js/';
    $this->cssPath      = WEBROOT . '/scripts/css/';
  }

  /**
   * Appel à la BDD
   * @param string $model_name
   */
  public function loadModel(string $model_name)
  {
    $this->$model_name = App::getInstance()->getTable($model_name, $this->currentClass);
  }

  /**
   * Récupère le nom de la classe sans le chemin, ex : App\src\TestBundle\Controller\IndexController => TestBundle;
   * return @string
   */
  private function getBundleName(string $classChild) : string {
    return explode('\\', $classChild)[2];
  }
}
