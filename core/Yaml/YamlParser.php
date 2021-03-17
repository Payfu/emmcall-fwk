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
    $this->_yamlFileName = $fileName;
  }
  
  private function debug($var, $stop = true){
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    if($stop){ exit; }
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
    // Si READ_YAML est true alors l'extension est installée sinon c'est la classe YamlParseFilePhp qui prend le relais
    //$array_full = READ_YAML ? yaml_parse_file($this->_yamlFileName) : $yamlPhp->convertYamlToArray($this->_yamlFileName);
    //if(empty($array_full)){ die("Les fichiers de route (.yaml) semblent vides => {$this->_yamlFileName}"); }
    
    // Obsolète
    // On vérifie si une clef import@ existe
    /*if(key_exists("import@", $array_full)){
      // On récupère le contenu
      $importArray = $array_full['import@'];
      // On la retire du tableau pour la traiter à part afin de la réinjecter dans le tableau.
      array_pop($array_full); 
      // On traite les fichier à importer
      $array_full = $this->createImportArray($importArray, $array_full);
    }*/
    
    return $array_full;
  }
  
  /*
   * On recrée les tableaux en provenance du ou des imports
   * OBSOLETE
   */
  private function createImportArray($importArray, $array_full) : array {
    
    // On retire le nom du fichier de la routes originel pour ne garder que le chemin
    $lastWord = explode('/',$this->_yamlFileName);
    $chemin = str_replace(end($lastWord), "", $this->_yamlFileName);
    
    // On charge la classe YamlParseFilePhp
    $yamlPhp = new YamlParseFilePhp();
    
    // On récupère le contenu des fichier yml converti en array
    $tab = [];
    foreach ($importArray as $v) {  
      // Si READ_YAML est true alors l'extension est installée sinon c'est la classe YamlParseFilePhp qui prend le relais
      $tab[] = READ_YAML ? yaml_parse_file($chemin.$v.'.yml') : $yamlPhp->convertYamlToArray($chemin.$v.'.yml');
    }
    
    // On fusionne tous les tableaux
    for($i = 0; $i<= count($tab)-1; $i++){
      $array_full = array_merge($array_full, $tab[$i]);
    }
    return $array_full;
  }
  
}