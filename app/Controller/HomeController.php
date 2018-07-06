<?php
namespace App\Controller;
       
use App;
use Core\Controller\Controller; // Appel pour la méthode render(); 
/**
 * Description of HomeController
 *
 * @author EmmCall
 */
class HomeController extends AppRootController
{

    private static $_instanceTools;

    public function __construct()
    {
        parent::__construct();
        //$this->loadModel('Nom_table');
    }


    /**
     * La methode render envoie la partie HTML, 
     * cette methode se trouve dans le controller situé dans le core
     * $this->Post et $this->category sont initialisé dans le constructeur avec $this->loadModel
     */
    public function index($arg = null)
    { 
      // Meta donnée
      $metaTitle = App::getInstance()->title;
      $metaDescription = App::getInstance()->description;

      // Appel des script JS et CSS (exemple)
      $scripts = $this->scripts([ 
              'main.css',  
              'contact.js']);

      $data = array_merge($scripts, compact( 'metaTitle', 'metaDescription', 'token'));

      // On envoi un tableau créé avec compact()
      $this->render('home.index', $data);
    }
}