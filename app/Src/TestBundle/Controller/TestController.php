<?php
namespace App\src\TestBundle\Controller;

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
    /*
     * la classe partente peut prendre deux paramètres
     * @param1 = static::class = nom de la classe
     * @param2 = string "nomDuNouveauTemplate" (optionnel) 
     */
    parent::__construct(static::class, null);
    // Pas utile si les entités sont créés via le manager.
    //$this->loadClefbdd('NomTable');
  }

  /**
   * La methode render envoie la partie HTML, 
   * cette methode se trouve dans le controller situé dans le core
   * $this->Commandes est initialisé dans le constructeur avec $this->loadModel
   * Les paramètres $username et $id sont retournés par la route donnée en exemple dans app/Routes/routes.yml. 
   * IMPORTANT: les paramètres doivent êtres présentés dans leur ordre de passage dans la route. $1, $2
   */
  public function index($var, $var2)
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
    // Création d'une requête via une instance
    //$result = $tNomTable->query("select *  form nom_table");
    
    // Dans le cas d'entités créées avec le manager
    // $table = new NomTableObj();
    // $listPlat = $table->getEntities(["id_categorie"=>5], ["select"=>"nom_plat, id_plat, id_categorie", "cache"=>60]);
    //var_dump($result);
    
    

    $data = array_merge($scripts, compact( 'metaTitle', 'metaDescription'));

    // On envoi un tableau créé avec compact()
    $this->render('index', $data);
  }
}
