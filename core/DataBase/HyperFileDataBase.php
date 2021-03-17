<?php
namespace core\DataBase;

/*
 NOTE : Tester les liens suivant pour la connexion via PDO
 * https://stackoverflow.com/questions/35434755/cant-connect-to-hyperfilesql-using-php-with-odbc
 * https://stackoverflow.com/questions/50001593/data-truncated-at-the-256th-character-php-with-hfsql-database-using-pdo-odbc
 */
use \COM;
//use Core\Config;
use core\DataBase\Config;
use core\Yaml\YamlParseFilePhp;

/**
 * Description of DataBase
 *
 * @author payfu
 */
class HyperFileDataBase extends DataBase
{
    private $db_name;
    private $db_user;
    private $db_host;
    private $db_port;
    private $db_prov;
    private static $_instance;

    public function __construct()
    {
      // On charge la classe YamlParseFilePhp
      $yamlPhp = new YamlParseFilePhp();
      
      // On récupère le fichier converti en tableau
      $array = READ_YAML ? yaml_parse_file(ROOT. '/config/config.yml') : $yamlPhp->convertYamlToArray(ROOT. '/config/config.yml');
      $listKeys = array_keys($array['database']);
      
      // On check les data et on récupère les données hfsql
      foreach ($listKeys as $k) {
        $arr = $array[$k];
        $dbType = strtolower($arr['db_type']);
        if($dbType === 'hfsql'){
          $this->db_name = $arr['db_name'];
          $this->db_user = $arr['db_user'];
          $this->db_host = $arr['db_host'];
          $this->db_port = $arr['db_port'];
          $this->db_prov = $arr['db_prov'];
        } 
      }
    }

    public static function getInstance()
    {
        if(is_null(self::$_instance)){
            self::$_instance = new App();
        }
        return self::$_instance;
    }
    
    private function getConnexion()
    {
      $Provider = ''
        . 'Provider='. $this->db_prov . ';'
        . 'Data Source=' . $this->db_host .':'. $this->db_port . ';'
        . 'User ID=' . $this->db_user . ';'
        . 'Initial Catalog=' . $this->db_name . ';';
      
      try{
        $conn = new COM("ADODB.Connection") or die("Cannot start ADO");
        $conn->Open($Provider);
        return $conn;
        
      } catch (COM_Exception $e) {
        echo 'Échec lors de la connexion : ' . $e->getMessage();
      }
    }
    
    /*
     * Lance une requète
     */
    public function query($query){
      $conn = $this->getConnexion();
      return $conn->Execute($query);    // Recordset
    }
}
