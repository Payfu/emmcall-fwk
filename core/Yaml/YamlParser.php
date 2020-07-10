<?php
namespace Core\Yaml;
use Core\Yaml\YamlParseFilePhp;
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
    // On gère les erreurs
    set_error_handler([$this,'errorHandler']);
    $this->_yamlFileName = $fileName;
  }
  
  /**
   * 
   * @return array
   */
  public function getArray() : array {   
    $yamlPhp = new YamlParseFilePhp();
    // Si READ_YAML est true alors l'extension est installée sinon c'est la classe YamlParseFilePhp qui prend le relais
    $array_full = READ_YAML ? yaml_parse_file($this->_yamlFileName) : $yamlPhp->convertYamlToArray($this->_yamlFileName);
    
    // On vérifie si une clef import@ existe
    if(key_exists("import@", $array_full)){
      // On récupère le contenu
      $importArray = $array_full['import@'];
      
      // On la retire du tableau pour la traiter à part afin de la réinjecter dans le tableau.
      array_pop($array_full);
      
      // On traite les fichier à importer
      $array_full = $this->createImportArray($importArray, $array_full);
    }
    
    return $array_full;
  }
  
  /*
   * On recrée les tableaux en provenance du ou des imports
   */
  private function createImportArray($importArray, $array_full) : array {
    
    // On retire le nom du fichier de la routes originel pour ne garder que le chemin
    $lastWord = explode('/',$this->_yamlFileName);
    $chemin = str_replace(end($lastWord), "", $this->_yamlFileName);
    
    // On charge la classe YamlParseFilePhp
    $yamlPhp = new YamlParseFilePhp();
    
    // On récupère le contenu des fichier yml converti en array
    foreach ($importArray as $v) {  
      
      // Si READ_YAML est true alors l'extension est installée sinon c'est la classe YamlParseFilePhp qui prend le relais
      $tab[] = READ_YAML ? yaml_parse_file($chemin.$v.'.yml') : $yamlPhp->convertYamlToArray($chemin.$v.'.yml');
      //$tab[] = yaml_parse_file($chemin.$v.'.yml');
    }
    
    // On fusionne tous les tableaux
    for($i = 0; $i<= count($tab)-1; $i++){
      $array_full = array_merge($array_full, $tab[$i]);
    }
    return $array_full;
  }
  
  /*
   * Message d'erreur
   */
  private function errorHandler($errno,$errmsg,$errfile) {   
    $msg  =   "L'erreur n°{$errno} s'est produite sur la page <strong>".$_SERVER['REQUEST_URI']."</strong>,<br /> dans le fichier <strong>{$errfile}</strong>.<br /><br />Voici l'erreur:<br /><strong>{$errmsg}</strong>";
    echo $msg;
    exit();     
  }
}
