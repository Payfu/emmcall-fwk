<?php
namespace Core\Login;

class Login extends Token
{
 
 private $_instanceToken;
 
 public function __construct()
 {
  
  if(is_null($this->_instanceToken)){ $this->_instanceToken = new Token(); }
 }
  
 public function getToken() : string {		
  return $this->_instanceToken->generateToken();		
 }
 
 /**
  * Déconnexion
  * Destruction du cookie et de la session
  */
  public function logout(string $cookie_name = null)
  {   
    if (isset($_COOKIE[$cookie_name])) { 
      unset($_COOKIE[$cookie_name]);
      setcookie($cookie_name, null, -1, '/');
    }

    $_SESSION = array();
    session_destroy();    
  }
}
