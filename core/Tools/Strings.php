<?php
namespace core\Tools;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Strings
{
  /**
   * Suppression des accents
   * @param string $str
   * @param string $charset
   * @return string
   */
  public static function removeAccents($str, $charset='utf-8') : string
  {
    // transformer les caractères accentués en entités HTML
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    // remplacer les entités HTML pour avoir juste le premier caractères non accentués
    // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    // Remplacer les ligatures tel que : Œ, Æ ...
    // Exemple "Å“" => "oe"
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'

    // Supprimer tout le reste
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

    return $str;
  }
  
  /*
   * Suppression de TOUS les caractères spéciaux (accent inclus), on ne garde que les alphanum 
   * $replaceBy est ce qui remplacera les caractères speciaux (espace vide par défaut) et on supprime les éventuels doublons (ex: --- => -)
   */
  public static function removeSpecChar($str, $replaceBy=false) : string
  {
    $rep = $replaceBy ?? '';
    $str = preg_replace("#[^a-zA-Z0-9]#", $rep, $str);
    if($replaceBy){ $str = preg_replace('#(?:(['.$rep.'])\1)\1*#', $rep, $str); }
    return $str; 
  }
  
  /*
   * On ne garde que les mots qui ont au minimum la taille $length (défaut 4)
   */
  public static function keepWords($str, $length=4, $delimiter=' ') : string
  {
    $strSanitize = '';
    foreach (explode($delimiter, $str) as $value) {
        if(strlen($value) >= $length){ $strSanitize .= $delimiter.$value;}
    }
    return substr($strSanitize, 1);
  }
  
  /*
   * Slugify
   */
  public static function slugify($str)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $str);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }
  
  public static function autoCopyright($year = 'auto'){  
    if(intval($year) == 'auto'){ $year = date('Y'); }
    if(intval($year) == date('Y')){ $copyright = intval($year); }
    if(intval($year) < date('Y')){ $copyright = intval($year) . ' - ' . date('Y'); }
    if(intval($year) > date('Y')){ $copyright = date('Y'); }
    
    return $copyright;
  }
  
  /*
   * On crypte la valeur en la glissant entre deux chaînes aléatoires de 5 caractères
   * On encode en base64 mais on retire le "=" à la fin
   */
  public static function encodeStr($str = false, $numGenerate = 5) : string {  
    if(!isset($str) && !$str){ exit("Veuillez indiquer une chaîne à encoder"); }
 
    // On encode la chaine base 64
    //$base64 = base64_encode( $this->generate(5).$str.$this->generate(5) );
    $base64 = base64_encode( Password::generate($numGenerate).$str.Password::generate($numGenerate) );
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
  public static function decodeStr($str, $numGenerated = 5) : string {
    
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
    //return substr(substr($cDecode, 0, -5), 5) ;
    return substr(substr($cDecode, 0, -$numGenerated), $numGenerated) ;
  }
}
