<?php
namespace App\Src\ErrorBundle\Controller;

use App;
use Core\App\Vue;         

/**
 * Description of ErrorController
 *
 * @author EmmCall
 */
class ErrorController extends App
{
  /**
   * Erreur 404
   */
  public function error404()
  {
    $sessionValue = isset($_SESSION['SESS_ERROR_404']) ? $_SESSION['SESS_ERROR_404'] : false;
    // Une fois la session récupérée on la détruit
    if(isset($_SESSION['SESS_ERROR_404'])){ unset($_SESSION['SESS_ERROR_404']); }
    // initialisation d'une variable
    Vue::var("metaTitle", App::getInstance()->title . 'Erreur 404');
    Vue::var("metaDescription", "Cette page n'existe pas.");
    Vue::var("sessionValue", $sessionValue);
    // Nom de la vue dans le dossier view
    Vue::init("error/404");
    // Lance l'affichage de la vue
    Vue::execute();
  }
}