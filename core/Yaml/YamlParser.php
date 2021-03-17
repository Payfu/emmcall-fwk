<?php
namespace core\Yaml;
use core\Yaml\YamlParseFilePhp;
/**
 * Description of YamlParser
 * Convertiseur Yaml -> Array()
 *
 * @author emmanuel callec
 */
class YamlParser
{
  private $_yamlFileName;
  
  public function __construct($fileName){
    $this->_yamlFileName = $fileName;
  }
  
  /**
   * On scan le dossier indiqué dans $this->_yamlFileName (/App/Routes)
   * @return array
   */
  public function getArray() : array {   
    $yamlPhp = new YamlParseFilePhp();
    
    $di = new \RecursiveDirectoryIterator($this->_yamlFileName);
    
    $array_full = [];
    // $file peut être lu avec SplFileInfo, ex: $file->getSize();
    foreach (new \RecursiveIteratorIterator($di) as $fileName => $file) {
      // Si c'est un fichier qui ne commence pas par un "-" (symbole permetant de mettre la route en standby)
      if($file->isFile() && !str_starts_with($file->getFileName(), '-') ){    
        $array = READ_YAML ? yaml_parse_file($fileName) : $yamlPhp->convertYamlToArray($fileName);
        // Si le fichier est vide
        if(empty($array)){ die("Le fichier de route ({$file->getFileName()}) semble vide"); }
        $array_full += $array;
      }
    }
    
    return $array_full;
  }
}