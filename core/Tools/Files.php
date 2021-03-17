<?php
namespace Core\Tools;

/**
 * Fonctions diverses et utiles
 * @author EmmCall
 */
class Files
{
  public static function minimizeCss(string $path )
  {
    $buffer = file_get_contents( $path );
    
    $pattern = 
    [
      '/\/\*(.+?)\*\//s',     // Skip comments
      '/[\r\n\t]/',           // Line return, tab
      '/\s*([\{\}\:;,])\s*/'  // blanks
    ];
    
    $replace = 
    [
      '', 
      '', 
      '$1',
    ];
 
    $buffer = preg_replace( $pattern, $replace, $buffer );
    return $buffer;
  }
  
  public static function file_exists_url($url)
  {    
    if ($fileopen = @fopen($url, 'r')):         
      fclose($fileopen);
      return true;
    else:
      return false;
    endif;
  }
}
