<?php

require_once '../Config.php';
require_once '../DataBase/DataBase.php';
require_once '../DataBase/TypeDataBase.php';

use Core\Config;
/**
 * Description of GetSetController
 * Cette classe permet de créer les getters et setters en fonction de la table qui lui est spécifiée.
 * @author emmanuel.callec
 */
class GetSetController
{
  private $_db;
  private $_db_type;
  private $_nom_clef_base;
  private $_nom_base;
  private $_nom_table;
  private $_nom_table_format;
  private $_columns;
  private $_attributes;
  private $_constructor;
  private $_get_set;
  private $_get_entity;
  private $_create_entity;
  private $_get_entities;
  private $_update_entity;
  private $_delete_entity;
  
  public function __construct(string $nomTable, string $ClefDatabase)
  {
    ini_set('display_errors', '1');
    define('ROOT', dirname(__FILE__));
    
    $c  =  yaml_parse_file(substr(ROOT, 0, -12) . 'config/config.yml')['database'][$ClefDatabase];
    
    $this->_db = new Core\DataBase\TypeDataBase($c['db_name'],$c['db_user'],$c['db_pass'],$c['db_host'],$c['db_type']);
    
    $this->_nom_table = $nomTable;
    // NOM_TABLE => NomTable
    $this->_nom_table_format = str_replace(' ', '', ucwords(str_replace('_', " ", strtolower($nomTable))));
    $this->_nom_base = $c['db_name'];
    $this->_db_type = $c['db_type'];
    $this->_nom_clef_base = $ClefDatabase;
    
  }
  
  /*
   * On vérifie que la table existe bien.
   */
  public function isTableExist(){
    // Si c'est un sqlsrv le nom de la base est sur table_catalog sinon c'est table_schema 
    $db_type = ($this->_db_type == 'sqlsrv') ? 'table_catalog' : 'table_schema';
    $res = $this->_db->query("SELECT * FROM information_schema.tables WHERE {$db_type} = '{$this->_nom_base}' AND table_name = '{$this->_nom_table}' ");
    return (count($res) != 0) ? true : false;
  }
  
  /*
   * On récupère le nom de toutes les colonnes et leur type
   * On place le tout dans un tableau multi avec comme clef le nom de la colonne et comme valeur son type (php et non sql).
   */
  private function listFields(){
    $db_type = ($this->_db_type == 'sqlsrv') ? 'TABLE_CATALOG' : 'TABLE_SCHEMA';
    $res = $this->_db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE {$db_type} = '{$this->_nom_base}' AND TABLE_NAME = '{$this->_nom_table}'");
    
    $columns = [];
    $dataType = null;
    foreach($res as $v){
      if($v->DATA_TYPE == 'varchar'){ $dataType = 'string'; } 
      else if ($v->DATA_TYPE == 'int'){ $dataType = 'int'; }
      else{ $dataType = null; }
      
      $columns[$v->COLUMN_NAME] = $dataType;
    }
    $this->_columns = $columns;
  }
  
  /*
   * On crée la liste des attributs
   * @private $__nom_attribut;
   */
  private function createAttributes(){
    $str='';
    // On crée les attributs avec deux "_" =>"__" ce qui permet de les différencier des classes parentes
    foreach (array_keys($this->_columns) as $k) {
      $str .= '
  protected $__'.$k.';'; 
    }
    
    // On ajoute les deux attributs systèmes
    $str .= '
  protected $_selectFields; // Contient une chaine ex : "champ1, champ2, champ3, ..."
  protected $_dataFields; // Utilisé pour getEntity et updateEntity'; 
    
    $this->_attributes = $str;
  }
  
  /*
   * Creation du constructeur
   */
  private function createConstructor(){
    $nomBase = ucfirst(strtolower($this->_nom_clef_base));
    $this->_constructor = '
  public function __construct()
  {
    parent::__construct(static::class, null);
    $this->load'.$nomBase.'(\''.$this->_nom_table_format.'\');
    $this->_selectFields = $this->getFields(get_object_vars($this));
  }';
  }
  
  /*
   * Création de la liste des getters et setters
   */
  private function createGetSet(){
    $str = '';
    foreach ($this->_columns as $k=>$v) {
      // nom_du_champ => NomDuChamp
      $fieldNameFormat = str_replace(' ', '', ucwords(str_replace('_', " ", strtolower($k))));
      
      // Return type
      $returnType = isset($v) ? ' : '.$v : '';
      $typage = isset($v) ? $v : '';
      
      $str .= '
  /*
  * '.$k.'
  */
  public function get'.$fieldNameFormat.'()'.$returnType.'{
    return $this->__'.$k.';
  }

  public function set'.$fieldNameFormat.'('.$typage.' $'.lcfirst ( $fieldNameFormat ).' ){
    $this->__'.$k.' = $'.lcfirst ( $fieldNameFormat ).';
  }';
    }
    $this->_get_set = $str;
  }
  
  /*
   * Création du getEntity
   * A modifier
   */
  private function createGetEntity(){
    
    // On crée la liste des champs à inclure 
    /*$list = '';
    foreach (array_keys($this->_columns) as $k) {
      $list .= "\t".'
       $this->__'.$k.' = $f->'.$k.';';
    }*/
    
    $this->_get_entity = '
  /*
  * Récupération de l\'entité
  */
  public function getEntity(array $arrayData, $conditions = null, $debug = false){
    $t = $this->'.$this->_nom_table_format.';
      
    // Si dans select et édité dans $conditions alors on initialise à nouveau $_selectFields
    if(is_array($conditions) && array_key_exists("select", $conditions)){$this->_selectFields = $conditions["select"];}
    $conditions = $conditions ? $conditions : [\'select\'=>$this->_selectFields] ;
    
    $this->_dataFields = $t->find($arrayData, $conditions, $debug);
    
    if(!$this->_dataFields){ return false; }
    return $this->constructEntity($this, __FUNCTION__, $this->_dataFields);
  }';
  }
  
  /*
   * On crée Create entity
   * A modifier
   */
  private function createCreateEntity(){
    
    // On crée la liste des champs à inclure à l'exception de __id
    /*$list = '';
    foreach (array_keys($this->_columns) as $k) {
      if($k <> "id"){
        $list .= "\t".'
        "'.$k.'" => $this->__'.$k.',';
      }
    }*/
    
    $this->_create_entity = '
  /*
  * On crée l\'entité une fois que tous les set sont hydratés
  */
  public function createEntity(){
    $t = $this->'.$this->_nom_table_format.';
    $array = $this->constructEntity($this, __FUNCTION__, $this->getFields(get_object_vars($this), true));
    
    // On enregistre la ligne
    return $t->insert($array);
  }';
  }
  
  /*
   * Création du getEntities
   */
  private function createGetEntities(){
    $this->_get_entities = '
  /*
  * On récupère toutes les entités
  */
  public function getEntities(array $where = null, array $cond = ["order"=>"id ASC"], $debug = false){
    $t = $this->'.$this->_nom_table_format.';
    return $t->all($where,$cond,$debug);
  }';
  }
  
  /*
   * Création de l'update entity
   */
  private function createUpdateEntity(){
    
    // On crée la liste des champs à inclure 
    /*$list = '';
    foreach (array_keys($this->_columns) as $k) {
      // ID ne fait pas parti des champs modifiables
      if($k != 'id'){
        $list .= "\t".'
        "'.$k.'"       => $this->__'.$k.',';
      }
    }*/
    
    $this->_update_entity = '
  /*
  * On met à jour l\'entité une fois que tous les set sont hydratés
  */
  public function updateEntity(bool $debug = false){
    $t = $this->'.$this->_nom_table_format.';
    // Si le getEntity n\'est pas lancé
    if(!$this->_selectFields){ return false; }

    return $t->update(["id"=> $this->__id], $this->constructEntity($this, __FUNCTION__, $this->_dataFields), $debug );
  }';
  }
  
  /*
   * Création de deleteEntity
   */
  private function createDeleteEntity(){
    $this->_delete_entity = '
  /*
  * Supprimer
  */
  public function deleteEntity(){
    $t = $this->'.$this->_nom_table_format.';
    return $t->delete($this->__id);
  }';
  }
  
  /*
   * Methode principale
   * Elle permet de construire le corps de l'objet
   */
  public function create() : string{
    $this->listFields();
    $this->createAttributes();
    $this->createConstructor();
    $this->createGetSet();
    $this->createCreateEntity();
    $this->createGetEntity();  
    $this->createGetEntities();  
    $this->createUpdateEntity(); 
    $this->createDeleteEntity();
    
    return "
      {$this->_attributes}
        
      {$this->_constructor}
      
      {$this->_get_set}
      
      {$this->_create_entity}
      
      {$this->_get_entity}
      
      {$this->_get_entities}
        
      {$this->_update_entity}
        
      {$this->_delete_entity}
      ";
  }  
}
