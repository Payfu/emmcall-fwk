<?php
namespace core\Tools;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Folders
{
  /*
   * On check un dossier et on regarde son contenu
   * Il retourne une valeur de 0 à 2
   * $path = nécessite un chemin ABSOLU => ROOT
   */
  public static function ctrlFolder($path) : int
  {
    $fichierTrouve=0;
    if (is_dir($path) and $dh = opendir($path))
    {
      while (($file = readdir($dh)) !== false && $fichierTrouve==0){ if ($file!="." && $file!=".." ) { $fichierTrouve=1;} }
      closedir($dh);             
    }
    // Le répertoir n'existe pas
    elseif(!is_dir($path))                           {             $val = 0;     }
    // Le répertoire existe mais il est vide
    if(is_dir($path) and $fichierTrouve == 0)        {             $val = 1;     }
    // Le répertoire contient des fichiers
    if(is_dir($path) and $fichierTrouve == 1)        {             $val = 2;     }

    return $val; 
  }
  
  /*
   * On supprime le contenu d'un dossier, retour true si l'action réussie
   */
  public static function viderDossier($path) : bool
  {
    $var = false;
    if($dh = opendir($path))
    {            
      // On lit chaque fichier du répertoire dans la boucle.
      while (false !== ($file = readdir($dh))) 
      {
        // Si le fichier n'est pas un répertoire…
        // On efface le fichier
        if ($file != ".." AND $file != "." AND !is_dir($file)){ unlink($path.$file); }
      }
      $var = true;
      closedir($dh); 
    }

    return $var;
  }
  
  /*
   * Connaître la taille d’un dossier
   */
  public static function folderSize($path): int 
  {
    $size = 0;
    
    foreach( glob( rtrim( $path, '/' ) . '/*', GLOB_NOSORT ) as $each ) 
    {
      $size += is_file( $each ) ? filesize( $each ) : folderSize( $each );
    }
    
    return $size;
  }
  
  /* Imprimer un arbre des dossiers et fichiers d’un répertoire */
  /**
    * Returns the tree
    * https://gist.github.com/hakre/3599532
    */
   public static function getTree( $folder ) 
   {
      $iterator = new RecursiveDirectoryIterator( $folder, RecursiveDirectoryIterator::KEY_AS_FILENAME | RecursiveDirectoryIterator::SKIP_DOTS);
      $tree = new RecursiveTreeIterator( $iterator );
      $this->unicodeTreePrefix( $tree );
 
      $output = "<div class=\"title\">{$folder}</div>";
      $output .= "<pre class=\"files\">";
      
      foreach( $tree as $filename => $file ) 
      {
         $class="file";
         // preg_match on unicode ├ won't work
         if( preg_match( "/\/{$this->plugin_folder}\/fonts\/[^\/]+$/", $file ) ) 
         {
          $class = 'font-folder';
         }
         $output .= "{$tree->getPrefix()}<span class=\"{$class}\">{$filename}</span><br>";
      }
      $output .= "</pre>";
 
      return $output;
   }
 
 
   /**
    * Nicely formatted directory listing
    */
   private function unicodeTreePrefix( RecursiveTreeIterator $tree ) 
   {
    $prefixParts = 
    [
      RecursiveTreeIterator::PREFIX_LEFT         => ' ',
      RecursiveTreeIterator::PREFIX_MID_HAS_NEXT => '│ ',
      RecursiveTreeIterator::PREFIX_END_HAS_NEXT => '├ ',
      RecursiveTreeIterator::PREFIX_END_LAST     => '└ '
    ];

    foreach ($prefixParts as $part => $string) 
    {
      $tree->setPrefixPart($part, $string);
    }
  }
}
