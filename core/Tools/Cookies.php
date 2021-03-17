<?php
namespace Core\Tools;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class cookies 
{
  public function __construct() 
  {    
    
  }
  
  public static function writeCookies(array $cookies, int $expires, string $domain  ): void
  {
    if(is_array($cookies)):
      
      foreach($cookies as $key => $value):
      
        setcookie($key, $value, $expires, "/", $domain, true, true);	
      
      endforeach;
      
    endif;
  }
  
  public static function readCookie(string $name)
  {
    if(isset($_COOKIE[$name])):
      
      return str_replace("+", " ", $_COOKIE[$name]);
      
    endif;
  }
  
  public function __destruct() 
  {
    
  }
}
