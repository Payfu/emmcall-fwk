<?php
namespace Core\Yaml;

/**
 * Description of YamlParser
 * Convertiseur Yaml -> Array()
 *
 * @author dev.hrteam
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
        return yaml_parse_file($this->_yamlFileName);
  }
  
  
  private function errorHandler($errno,$errmsg,$errfile) {   
        $msg  =   "L'erreur n°{$errno} s'est produite sur la page <strong>".$_SERVER['REQUEST_URI']."</strong>,<br /> dans le fichier <strong>{$errfile}</strong>.<br /><br />Voici l'erreur:<br /><strong>{$errmsg}</strong>";
        echo $msg;
        exit();     
    }
  
  
}
