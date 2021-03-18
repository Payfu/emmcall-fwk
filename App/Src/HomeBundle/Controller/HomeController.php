<?php
namespace App\Src\HomeBundle\Controller;

use App;
use Core\App\Vue;

/**
 * Description of HomeController
 *
 * @author emmanuel.callec
 */
class HomeController extends App
{ 
  /**
    * La méthode static Vue gère la partie HTML, 
    * $username, $id sont des valeurs envoyées en GET 
    */
  public function index($username, $id){
    // Appel à une entité, les entités se créent depuis le manager
    // $t = new App\Entities\Objects\EntityNameObj();
    // $res = $t->getEntities(["id"=>1], ["select"=>"id"]);

    /*
       * A l'exception de Vue::execute() et Vue::html("autre/test"); 
       * Toutes les autres méthode de Vue peuvent être placées dans un constructeur ce qui peut éviter des redondances de code dans les méthodes
       */
    // Génération d'un lien : link("linkName","routeName")
    // Vue::link("linkIndex","index"); 

    // Défini un autre template que celui-par défaut. Pour l'appliquer à tout le controller il suffit d'ajouter cette ligne dans un constructeur.
    // Vue::template("default2"); 

    // On implément une vue html dans une variable, les variables de cette vue sont initialisé via Vue::var()
    // $var = Vue::html("autre/test");

    // initialisation d'une variable
    Vue::var("metaTitle", App::getInstance()->title);
    Vue::var("metaDescription",  App::getInstance()->description);
    
    // On peut appeler un script js ou css depuis les bundle courrant, un autre bundle ou une url
    Vue::scripts(['https://url-script.js', 'exempleScript.js', 'newbundle::test.js', 'exempleScript.css']);

    // Nom de la vue dans le dossier view
    Vue::init("index");

    // Lance l'affichage de la vue
    Vue::execute();
  }
}
