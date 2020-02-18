<?php
namespace Core\Password;

/**
 * Description of Password
 * Générateur de mot de passe
 * @author Emm Call
 */
class Password
{ 
  /*
   * Génération de password
   */
  public static function generate($length = 10, $type = 'crypt') : string {
    
    $str      = "";
    for($i = 0; $i < $length; $i++){
      if($type == 'crypt'){
        $v = [$this->getAlphaMin(), $this->getAlphaMaj(), $this->getNumeric(), $this->getSpecChar()];
        $str .= $v[rand(0,3)];
      }
      
      if($type == 'alphanum'){
        $v = [$this->getAlphaMin(), $this->getAlphaMaj(), $this->getNumeric()];
        $str .= $v[rand(0,2)];
      }
      
      if($type == 'alpha'){
        $v = [$this->getAlphaMin(), $this->getAlphaMaj()];
        $str .= $v[rand(0,1)];
      }
      
      if($type == 'num'){
        $v = [$this->getNumeric(), $this->getNumeric()];
        $str .= $v[rand(0,1)];
      }
    }
    return str_shuffle($str);
  }
  
  /*
   * Cryptage de password
   */
  public static function crypt($str) : string{
    return password_hash($str, PASSWORD_DEFAULT);
  }
  
  /*
   * Decryptage de password
   */
  public static function verify($str, $hash) : bool {
    if (password_verify($str, $hash)) {
      return true;
    } else {
      return false;
    }
  }
  
  private function getAlphaMin(){
    return chr(rand(97,122));
  }
  
  private function getAlphaMaj(){
    return chr(rand(65,90));
  }
  
  private function getNumeric(){
    return random_int(0,9);
  }
  
  private function getSpecChar(){
    $str = "!@#$%^&*()-=+?";
    return substr(str_shuffle($str), 0, 1);
  }
}
