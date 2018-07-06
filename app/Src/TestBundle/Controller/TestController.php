<?php
namespace App\src\Test\Controller;

use App;
use App\src\AppController;
use Core\Controller\Controller;
            

/**
 * Description of TestController
 *
 * @author EmmCall
 */
class TestController extends AppController
{
  public function __construct()
  {
    // static::class = nom de la classe
    parent::__construct(static::class);
    //$this->loadModel('NomTable');
  }

  /**
   * La methode render envoie la partie HTML, 
   * cette methode se trouve dans le controller situé dans le core
   * $this->Commandes est initialisé dans le constructeur avec $this->loadModel
   * Les paramètres $username et $id sont retournés par la route donnée en exemple dans app/Routes/routes.yml. 
   * IMPORTANT: les paramètres doivent êtres présentés dans leur ordre de passage dans la route. $1, $2
   */
  public function index()
  {
    // NOTE : Ne pas oublier de créer une route 
    // Meta donnée
    $metaTitle = App::getInstance()->title;
    $metaDescription = App::getInstance()->description;

    // Appel des script JS et CSS
    $scripts = $this->scripts([ 
            'exemple.css', 
            'exemple.js', 
            ]);

    // Connexion à la table
    //$tNomTable = $this->NomTable;
    //$list = $tNomTable->all(["date"=>date("1979-05-04")]);
    //var_dump($list);

    $data = array_merge($scripts, compact( 'metaTitle', 'metaDescription', 'token'));

    // On envoi un tableau créé avec compact()
    $this->render('index', $data);
  }
}