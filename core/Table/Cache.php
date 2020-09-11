<?php
namespace Core\Table;

/**
 * Description of Cache
 * Permet des actions sur le cache
 * @author emmanuel.callec
 */
class Cache
{
  /*
   * On purge le dossier core/Table/tmp
   * Cette méthode facultative doit être appelée via un controller qui lui-même sera appelé via une route
   */
  public function purge(int $validite = 3600 * 24 ){
    $root = ROOT."/core/Table/tmp";
    $listFiles = scandir($root);
    
    foreach ($listFiles as $filename) {
      // On ne tient pas compte des chemins '.' et '..'
      if(!in_array($filename, ['.', '..'])){        
        // Pour information on convertis le filemtime en date
        //$date = date ("F d Y H:i:s", filemtime($root.'/'.$filename));
        
        // On supprime les fichiers ayant une ancienneté de plus de 24h (par défaut)
        if(filemtime($root.'/'.$filename)<time() - $validite){
          unlink($root.'/'.$filename);
        }
      }
    }
  }
}