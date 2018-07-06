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
        $this->jsPath   = WEBROOT . 'scripts/js/';
        $this->cssPath  = WEBROOT . 'scripts/css/';
    }
    
    /**
     * Appel à la BDD
     * @param string $model_name
     */
    public function loadModel(string $model_name)
    {
        $this->$model_name = App::getInstance()->getTable($model_name);
    }
}
