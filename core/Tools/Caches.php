<?php
namespace Core\Tools;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Caches
{
  /* Obliger le navigateur à ne pas utiliser les fichiers en cache (js, css)*/
  public static function browserClearingCache() 
  {
    header( "Expires: Tue, 01 Jan 2000 00:00:00 GMT" );
    header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
    header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
    header( "Cache-Control: post-check=0, pre-check=0", false );
    header( "Pragma: no-cache" );
  }
}
