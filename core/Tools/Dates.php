<?php
namespace Core\Tools;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Dates
{
  /*
   * Convertisseur de date
   * $date = 04/05/1979
   * $patternIn = "d/m/Y"
   * $patternOut = "Y-m-d"
   */
  public static function dateToDate(string $date, string $patternIn, string $patternOut ): string
  {
    $d = \DateTime::createFromFormat($patternIn, $date);
    return $d->format($patternOut);
  }
  
  /*
   * Récupère une date en fonction de sa définition en anglais.
   * $date = 04/05/1979
   * $english = "first day of this month"
   * $patternOut = "Y-m-d"
   */
  public static function dateFromString(string $date, string $english, string $patternOut): string
  {
    $d = new \DateTime( $date );
    $d->modify( $english );
    return $d->format($patternOut);
  }
  
  /*
   * Y-m-d => jour 00 mois
   * ou ce que l'on veut selon le format
   * http://php.net/manual/fr/function.strftime.php
   * strftime gérant mal les lettres accentuées on y ajoute un utf8_encode
   */
  public static function dateToFr( string $date, $format="%A %d %B" )
  {
    setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
    return utf8_encode(strftime($format, strtotime($date)));
  }
  
  /*
   * Vérifie la validité d'une date : 2020-02-30 = false
   * Retourne bool
   */
  public static function validateDate($date, $format = 'Y-m-d H:i:s') : bool
  {
    $d = \DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }
}
