<?php
namespace App\Controller;

use Core\Controller\Controller;
use App;

/**
 * Description of AppController
 * Cette page est importante car elle détermine l'url de views et le nom de la page du template HORS BUNDLE
 * Ces variables son appelées dans Core\Controller\Controller
 * @author EmmCall
 */
class AppRootController extends Controller
{
    /**
     * Initialisation des variables
     */
    protected $template = 'default';

    public function __construct()
    {
      $this->viewPath     = ROOT . '/app/Views/';
      $this->templatePath = ROOT . '/app/Views/';
      $this->jsPath   = WEBROOT . '/scripts/js/';
      $this->cssPath  = WEBROOT . '/scripts/css/';
    }
    
    /*
     * Cette methode magique me permet de récupérer le nom de la clef de la base et de la table souhaité dans les contrôleurs
     */
    public function __call(string $nomBase, array $nomTable){
      $nomBase = strtolower(explode("load", $nomBase)[1]);
      $nomTable = $nomTable[0];
      
      if(in_array($nomBase, array_keys(App::getInstance()->getDatabases()))){
        $this->$nomTable = App::getInstance()->getTable($nomTable, $nomBase);
      } else {
        die("Clef BDD ({$nomBase}) incorrecte !");
      } 
    }
    
    /*
     * 
     *
    public function loadModel(string $nomBase, string $nomTable){
  
      if(in_array($nomBase, array_keys(App::getInstance()->getDatabases()))){
        $this->$nomTable = App::getInstance()->getTable($nomTable, $nomBase);
      } else {
        die("Clef BDD ({$nomBase}) incorrecte !");
      }
    }*/
    
}