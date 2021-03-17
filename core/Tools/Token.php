<?php
namespace Core\Tools;

class Token
{
 
 /*
  * Génère un Token, que l'on place également dans une session
  * return string : 5d86062d6447567a6443356c62573168626e566c6243316a595778735a574d755a6e493d8
  */
 public function generateToken() : string {		
  $str = $this->createToken(); 
  $_SESSION['pg_a'] = $str;
  return $str;
 }
 
 /*
  * Vérifie si la valeur cachée dans le Token est correcte et si ce token est également identique à la session
  * return bool
  */
 public function checkToken($token) : bool
 {
   if(!isset($_SESSION['pg_a'])){ die("Erreur de token !"); }
   if($this->decodeToken($token) === $this->getUri() AND $token == $_SESSION['pg_a'] ){ return true; }
   return false;
 }
 
 /*
  * Récupère le domaine du site
  */
 private static function getUri(){
  return $_SERVER['HTTP_HOST'];
 }
 
 /*
  * Crée un token
  * return : @string
  */
 public static function createToken() : string{

  // Récupère l'URI
  $uri = self::getUri();
  
  // Récupère le dernier chiffre
  $key = substr(date("s"), -1);
  $key = ($key == 0) ? 1 : $key; // Key ne peut pas être égale à 0 sinon le random plus bas ne marche pas.
  
  // Encode l'URI
  $uri_encode =  bin2hex(base64_encode($uri));
  
  // Crée une chaine aléatoire en utilisant $key comme longueur
  $randomStr = bin2hex(random_bytes($key));
  
  // Récupère la chaîne aléatoire et je la coupe à x en fonction de $key
  $str = substr($randomStr, 0, $key);
  
  // Retourne une chaîne concaténé.
  return $str . $uri_encode . $key;
 }
 
 /*
  * Decode le token
  * return @string : domaine.com
  */
 private function decodeToken($token) : string {
  
  // Récupère le dernier chiffre 
  $key = substr($token, -1);

  // Récupère l'URI encodée et retire $key
  $uri_encode = substr( substr($token, $key), 0, -1 );

  // Si $uri_encode est une chaîne hexadecimale : retourne l'URI
  if( ctype_xdigit ( $uri_encode ) ){
   return base64_decode(hex2bin($uri_encode));
  }
  
  // Sinon retourne une chaîne vide
  return '';
 }
}
