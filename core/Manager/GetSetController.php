<?php

require_once '../Config.php';
require_once '../DataBase/DataBase.php';
require_once '../DataBase/MysqlDataBase.php';

use Core\Config;
/**
 * Description of GetSetController
 * Cette classe permet de créer les getters et setters en fonction de la table qui lui est spécifiée.
 * @author emmanuel.callec
 */
class GetSetController
{
  private $_db;
  private $_nom_base;
  private $_nom_table;
  private $_columns;
  private $_attributes;
  private $_constructor;
  private $_get_set;
  private $_get_entity;
  private $_create_entity;
  private $_get_entities;
  private $_update_entity;
  private $_delete_entity;
  
  public function __construct($nomTable)
  {
    // On récupère une instance de Config
    $c = Config::getInstance('./../../config/config.php');
    $this->_db = new Core\DataBase\MysqlDataBase($c->get('db_name'),$c->get('db_user'),$c->get('db_pass'),$c->get('db_host'),$c->get('db_type'));
    $this->_nom_table = $nomTable;
    $this->_nom_base = $c->get('db_name');
  }
  
  /*
   * On vérifie que la table existe bien.
   */
  public function isTableExist(){
    $res = $this->_db->query("SELECT * FROM information_schema.tables WHERE table_schema = '{$this->_nom_base}' AND table_name = '{$this->_nom_table}' LIMIT 1");
    return (count($res) != 0) ? true : false;
  }
  
  /*
   * On récupère le nom de toutes les colonnes et leur type
   * On place le tout dans un tableau multi avec comme clef le nom de la colonne et comme valeur son type (php et non sql).
   */
  private function listFields(){
    $res = $this->_db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$this->_nom_table}'");
    //var_dump($res);
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
   * @private $_nom_attribut;
   */
  private function createAttributes(){
    $str='';
    foreach ($this->_columns as $k=>$v) {
      $str .= '
  private $_'.$k.';';
    }
    $this->_attributes = $str;
  }
  
  /*
   * Creation du constructeur
   */
  private function createConstructor(){
    $this->_constructor = '
  public function __construct()
  {
    parent::__construct(static::class, null);
    $this->loadModel(\''.$this->_nom_table.'\');
  }';
  }
  
  /*
   * Création de la liste des getters et setters
   */
  private function createGetSet(){
    $str = '';
    foreach ($this->_columns as $k=>$v) {
      // nom_du_champ => NomDuChamp
      $fieldNameFormat = str_replace(' ', '', ucwords(str_replace('_', " ", $k)));
      
      // Return type
      $returnType = isset($v) ? ' : '.$v : '';
      $typage = isset($v) ? $v : '';
      
      $str .= '
  /*
  * '.$k.'
  */
  public function get'.$fieldNameFormat.'()'.$returnType.'{
    return $this->_'.$k.';
  }

  public function set'.$fieldNameFormat.'('.$typage.' $'.lcfirst ( $fieldNameFormat ).' ){
    $this->_'.$k.' = $'.lcfirst ( $fieldNameFormat ).';
  }';
    }
    $this->_get_set = $str;
  }
  
  /*
   * Création du getEntity
   */
  private function createGetEntity(){
    
    // On crée la liste des champs à inclure 
    $list = '';
    foreach ($this->_columns as $k => $v) {
      $list .= "\t".'
       $this->_'.$k.' = $f->'.$k.';';
    }
    
    $this->_get_entity = '
  /*
  * Récupération de l\'entité
  */
  public function getEntity($arrayData){
    $t = '.ucfirst ($this->_nom_table).';
    $f = $t->find($arrayData);

    if(!$f){ return false; }
    '.$list.'
    return $this;
  }';
  }
  
  /*
   * On crée Create entity
   */
  private function createCreateEntity(){
    
    // On crée la liste des champs à inclure 
    $list = '';
    foreach ($this->_columns as $k => $v) {
      $list .= "\t".'
       "'.$k.'"       => $this->_'.$k.',';
    }
    
    $this->_create_entity = '
  /*
  * On crée l\'entité une fois que tous les set sont hydratés
  */
  public function createEntity(){
    $t = '.ucfirst ($this->_nom_table).';
    $isExist = $t->find(["id"=> $this->_id]);

    // S\'il existe déjà
    if($isExist){ return false; }

    // Sinon on enregistre
    return $t->insert([
      '.rtrim($list, ',').'
    ]);
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
  public function getEntities(array $where = null, array $cond = ["order"=>"id ASC"]){
    $t = '.ucfirst ($this->_nom_table).';
    return $t->all($where,$cond);
  }';
  }
  
  /*
   * Création de l'update entity
   */
  private function createUpdateEntity(){
    
    // On crée la liste des champs à inclure 
    $list = '';
    foreach ($this->_columns as $k => $v) {
      // ID ne fait pas parti des champs modifiables
      if($k != 'id'){
        $list .= "\t".'
        "'.$k.'"       => $this->_'.$k.',';
      }
    }
    
    $this->_update_entity = '
  /*
  * On met à jour l\'entité une fois que tous les set sont hydratés
  */
  public function updateEntity(){
    $t = '.ucfirst ($this->_nom_table).';
    $valid = $t->find(["id"=> $this->_id]); 

    // Si l\'id n\'existe pas
    if(!$valid){ return false; }

    return $t->update(["id"=> $this->_id], [
      '.rtrim($list, ',').'
    ]);
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
    $t = '.ucfirst ($this->_nom_table).';
    return $t->delete($this->_id);
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
