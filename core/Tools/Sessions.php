<?php
namespace core\Tools;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Sessions
{
  /**
   * Lecture d'une variable de session
   * @return string|null
   */
  public static function readSession($session) 
  {
    return $_SESSION[$session] ?? null;
  }
    
  /*
   * Convertisseur de date
   * $date = 04/05/1979
   * $patternIn = "d/m/Y"
   * $patternOut = "Y-m-d"
   */
  public static function writeSession($session, $value)
  {
    $_SESSION[$session] = $value;
  }     
  
  public static function checkSession($session): bool
  {
    return(isset($_SESSION[$session]));
  }
  
  public static function destroySession()
  {
    // Détruit toutes les variables de session
    $_SESSION = array();
    
    // Si vous voulez détruire complètement la session, effacez également le cookie de session.
    // Note : cela détruira la session et pas seulement les données de session !
    if (ini_get("session.use_cookies")) 
    {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
    }

    // Finalement, on détruit la session.
    session_destroy();
  }
}
