<?php
namespace core\Password;

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
    $str = "";
    for($i = 0; $i < $length; $i++){
      if($type == 'crypt'){
        $v = [self::getAlphaMin(), self::getAlphaMaj(), self::getNumeric(), self::getSpecChar()];
        $str .= $v[rand(0,3)];
      }
      
      if($type == 'alphanum'){
        $v = [self::getAlphaMin(), self::getAlphaMaj(), self::getNumeric()];
        $str .= $v[rand(0,2)];
      }
      
      if($type == 'alpha'){
        $v = [self::getAlphaMin(), self::getAlphaMaj()];
        $str .= $v[rand(0,1)];
      }
      
      if($type == 'num'){
        $v = [self::getNumeric(), self::getNumeric()];
        $str .= $v[rand(0,1)];
      }
    }
    return str_shuffle($str);
  }
  
  /*
   * Cryptage d'une chaine
   */
  public static function crypt($str, $algo = PASSWORD_DEFAULT) : string{
    return password_hash($str, $algo);
  }
  
  /*
   * Vérification du mot de passe
   */
  public static function verify($str, $hash) : bool {
    if (password_verify($str, $hash)) {
      return true;
    } else {
      return false;
    }
  }
  
  private static function getAlphaMin(){
    return chr(rand(97,122));
  }
  
  private static function getAlphaMaj(){
    return chr(rand(65,90));
  }
  
  private static function getNumeric(){
    return random_int(0,9);
  }
  
  private static function getSpecChar(){
    $str = "!@#$%^&*()-=+?";
    return substr(str_shuffle($str), 0, 1);
  }
}