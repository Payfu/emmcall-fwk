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
  public function generate($length = 10, $type = 'crypt') : string {
    
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
  public function crypt($str) : string{
    return password_hash($str, PASSWORD_DEFAULT);
  }
  
  /*
   * Decryptage de password
   */
  public function verify($str, $hash) : bool {
    if (password_verify($str, $hash)) {
      return true;
    } else {
      return false;
    }
  }
  
  /*
   * On crypte la valeur en la glissant entre deux chaînes aléatoires de 5 caractères
   * On encode en base64 mais on retire le "=" à la fin
   */
  public function encodeStr($str){  
    // On encode la chaine base 64
    $base64 = base64_encode( $this->generate(5).$str.$this->generate(5) );
    // On compte le nom de signe '=' à la fin
    $n = substr_count($base64,"=");
    // S'il y a au moins un =
    if($n > 0){
      $base64 = substr( $base64, 0, '-'.$n);
    }
    // On place le nombre de '=' en fin de chaine
    $str = $base64.$n;
    
    return str_replace("/", "-", strrev($str) );
  }
  
  /*
   * On décode la chaîne pour récupérer la partie qui nous intéresse
   * On remet le '=' à la fin puis on décode
   * Enfin on retire les 5 premiers et 5 derniers caractères de la chaine
   */
  public function decodeStr($str){
    $a = str_replace("-", "/", $str);
    $s = strrev($a);
    // on récupère le dernier chiffre
    $n = substr($s, -1);
    // on récupère la chaine SANS le dernier chiffre
    $s = substr($s, 0, -1);
    // On répète le '=' autant de fois que nécessaire
    $nn = str_repeat("=", $n);
    // On ajoute les '=', s'ils extiste
    $str = $s.$nn;
    // On décode
    $cDecode = base64_decode($str);
    // On récupère la chaîne
    return substr(substr($cDecode, 0, -5), 5) ;
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
