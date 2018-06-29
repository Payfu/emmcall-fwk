<?php
declare(strict_types=1); // Déclanche une erreur en cas de scalaire incorrect

namespace App\Controller;

use Core\Controller\Controller;
use Core\Auth\DBAuth;
use App\Controller\UserController;
use App;


/**
 * Description of AppController
 * Cette page est importante car elle détermine l'url de views et le nom de la page du template
 * Ces variables son appelées dans Core\Controller\Controller
 * @author EmmCall
 */
class AppController extends Controller
{
    /**
     * Initialisation des variables
     */
    protected $template = 'default';


    public function __construct()
    {
        $this->viewPath = ROOT . '/app/Views/';
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
    
    /**
     * Création d'un ip unique
     * 
     * return string (len: 27) : 02da40d8f456ae570628802b731
     */
    public function generateIp() : string
    {
        return bin2hex(random_bytes(7)).uniqid();
    }
    
   
}
