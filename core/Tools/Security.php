<?php
namespace Core\Tools;

use Core\Password\Password;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Security
{    
  /*
   * On crypte la valeur en la glissant entre deux chaînes aléatoires de 5 caractères
   * On encode en base64 mais on retire le "=" à la fin
   */
  public static function encodeStr($str = false) : string 
  {  
  
    if(!$str){ exit("Veuillez indiquer une chaîne à encoder"); }
 
    // On encode la chaine base 64
    //$base64 = base64_encode( $this->generate(5).$str.$this->generate(5) );
    $base64 = base64_encode( Password::generate(5).$str.Password::generate(5) );
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
  public static function decodeStr($str) : string 
  {
    
    // on retourne la variable sans modification si celle-ci à une taille inférieure à 10 (voir encodeStr( )) 
    if(strlen($str) <= 10){ return $str; }
    
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
    
  /**
   * Création d'un id unique
   * 
   * return string (len: 27) : 02da40d8f456ae570628802b731
   */
  public static function generateId() : string
  {
    return bin2hex(random_bytes(7)).uniqid();
  }
  
  /**
  * \brief Gestion du cryptage de texte
  * \details $texte Texte a crypter
  * \param $prestation Type de prestation
  * \param $method Methode de cryptage
  * \return Chaine cryptée
  */
  public static function cryptage($texte, $method = "md5") 
  { 
    //Cryptage en fonction de la methode definie
    switch($method) 
    { 
      case 'md5': 
        $texteCrypte = trim(md5($texte)); 
        break; 
      case 'sha1': 
        $texteCrypte = trim(sha1($texte)); 
        break; 
    } 
	
    return $texteCrypte;
  }    
}
