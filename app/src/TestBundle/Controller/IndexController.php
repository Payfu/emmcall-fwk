<?php
//namespace App\Controller;
namespace App\src\TestBundle\Controller;

use App;
use App\src\AppController;

use Core\ { 
            Controller\Controller, // Appel pour la méthode render(); 
            Login\Login
            };

/**
 * Description of HomeController
 *
 * @author EmmCall
 */
class IndexController extends AppController
{
    public function __construct()
    {
      // static::class = nom de la classe
      parent::__construct(static::class);
      $this->loadModel('Commandes');
    }

    /**
     * La methode render envoie la partie HTML, 
     * cette methode se trouve dans le controller situé dans le core
     * $this->Commandes est initialisé dans le constructeur avec $this->loadModel
     * Les paramètres $username et $id sont retournés par la route donnée en exemple dans app/Routes/routes.yml. 
     * IMPORTANT: les paramètres doivent êtres présentés dans leur ordre de passage dans la route. $1, $2
     */
    public function maMethode($username, $id)
    {
      
      var_dump($username);
      
      // Meta donnée
      $metaTitle = App::getInstance()->title;
      $metaDescription = App::getInstance()->description;

      // Appel des script JS et CSS
      $scripts = $this->scripts([ 
              'main.css', 
              'upjs.js', 
              'jquery.easing.min.js', 
              'paralax.js', 
              'simple-lightbox.js', 
              'contact.js']);
      
      $tCommande = $this->Commandes;
      $list = $tCommande->all(["date"=>date("2018-06-25")]);
      
      //var_dump($list);

      $data = array_merge($scripts, compact( 'metaTitle', 'metaDescription', 'token'));

      // On envoi un tableau créé avec compact()
      $this->render('index', $data);
    }
}

