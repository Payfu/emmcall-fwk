<?php
namespace App\Controller;

use App;
use Core\Controller\Controller; // Appel pour la méthode render(); 

/**
 * Description of ErrorController
 * Cette classe est appelé a évolué, elle permet de gérer les pages d'erreur
 * @author emmanuel.callec
 */
class ErrorController extends AppRootController
{
  public function __construct()
  {
    parent::__construct();
  }
  
  /*
   * Gestion des erreurs 404
   */
  public function error404(){
    $sessionValue = isset($_SESSION['SESS_ERROR_404']) ? $_SESSION['SESS_ERROR_404'] : false;
    
    // Une fois la session récupérée on la détruit
    if(isset($_SESSION['SESS_ERROR_404'])){ unset($_SESSION['SESS_ERROR_404']); }
    
    // Meta donnée
    $metaTitle = App::getInstance()->title. 'Erreur 404';
    $metaDescription = 'Cette page n\'existe pas.';

    // Appel des script JS et CSS (exemple)
    $scripts = $this->scripts([ ]);

    $data = array_merge($scripts, compact( 'metaTitle', 'metaDescription', 'sessionValue'));

    // On envoi un tableau créé avec compact()
    $this->render('error.404', $data);
    
  }
}
