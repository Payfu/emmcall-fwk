<?php
namespace App\Controller;

use App;

use Core\ { 
            Controller\Controller, // Appel pour la méthode render(); 
            Tools\Tools,
            Login\Login
            };

//use Core\Auth\ApiAuthUser;


/**
 * Description of HomeController
 *
 * @author EmmCall
 */
class HomeController extends AppController
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
    public function index($arg, $arg2)
    {
      
      //echo $_GET['id']."<br />";
      var_dump($arg);
     
     

      //if(is_null(self::$_instanceTools)){ self::$_instanceTools = new Login(); }
      //$token = self::$_instanceTools->getToken();



      //print($token);

      // Meta donnée
      $metaTitle = App::getInstance()->title;
      $metaDescription = App::getInstance()->description;

      // Form Contact
      //$form = new BootstrapForm($_POST);

      // Appel des script JS et CSS
      $scripts = $this->scripts([ 
              'main.css', 
              'upjs.js', 
              'jquery.easing.min.js', 
              'paralax.js', 
              'simple-lightbox.js', 
              'contact.js']);

      $data = array_merge($scripts, compact( 'metaTitle', 'metaDescription', 'token'));

      // On envoi un tableau créé avec compact()
      $this->render('home.index', $data);
    }
}
