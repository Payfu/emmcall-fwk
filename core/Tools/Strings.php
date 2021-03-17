<?php
namespace Core\Tools;

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
  
  public static function autoCopyright($year = 'auto')
  {  
  
    if(intval($year) == 'auto'){ $year = date('Y'); }
    if(intval($year) == date('Y')){ $copyright = intval($year); }
    if(intval($year) < date('Y')){ $copyright = intval($year) . ' - ' . date('Y'); }
    if(intval($year) > date('Y')){ $copyright = date('Y'); }
    
    return $copyright;
  }
}
