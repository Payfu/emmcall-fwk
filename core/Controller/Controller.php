<?php
namespace core\Controller;

/**
 * Description of Controller
 *
 * @author EmmCall
 */
class Controller
{   
  /**
   * Renvoie les bon header en fonction de la situation
   *
  protected function forbidden(string $value=null)
  {
    // On enregistre $value en session
    if($value <> null){
      $_SESSION['SESS_ERROR_403'] = $value;
    }
    $url = WEBROOT.'/error403';
    // On redirige vers la route par defaut du controller app/controller/ErrorController.php
    header('Location:'.$url);
    exit;
  }*/
    
  /*
   * Gestion de l'erreur 404
   */
  protected function notFound(string $value=null)
  {
    // On enregistre $value en session
    if($value <> null){
      $_SESSION['SESS_ERROR_404'] = $value;
    }
    $url = WEBROOT.'/error404';
    // On redirige vers la route par defaut du controller App/Src/ErrorBundle/Controller/ErrorController.php
    header('Location:'.$url);
    exit;
  }
}