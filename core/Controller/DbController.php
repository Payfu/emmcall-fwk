<?php
namespace core\Controller;
use core\DataBase\TypeDataBase;
use App;

/**
 * Description of DbController
 * Cette classe est étendue depuis App/Entities/Objects/[nomTable]Obj.php
 * 
 * @author Emmanuel CALLEC
 */
class DbController 
{
  private $_db_instance;
  
  /**
    * Cette methode magique me permet de récupérer le nom de la clef de la base et de la table souhaité dans les contrôleurs
    */
  public function __call(string $nomBase, array $nomTable){
    $nomBase = strtolower(explode("load", $nomBase)[1]);
    $nomTable = $nomTable[0];

    if(in_array($nomBase, array_keys(App::getInstance()->getDatabases()))){
      $this->$nomTable = $this->getTable($nomTable, $nomBase);
    } else {
      die("Clef BDD ({$nomBase}) incorrecte !");
    } 
  }
  
  /**
    * Cette méthode utlise une Factory permettant d'appeler une succession de tables sans difficulté à partir de leur namespace
    */
  public function getTable($nameTable, $nomBase){
    // Ce chemin est un namespace
    $className = "\\App\\Entities\\Tables\\" . ucfirst($nameTable) . "Table"; // ex : App\Table\CategoriesTable
    // Instanciation de la classe
    return new $className($this->getDb($nomBase));
  }
  
  /**
    * Second Factory pour la base de données
    */
  public function getDb($nomBase){ 
    foreach(App::getDatabases() as $k=>$v){
      if($k === $nomBase){
        $this->_db_instance = new TypeDataBase($v);
        // c'est la bonne base on casse la boucle
        break;
      }
    }
    return $this->_db_instance;
  }
  
  /*
   * Cette méthode est appelée depuis un nomTableObj.
   * On récupère la liste des champs pour le select d'une entité
   * paramètre à false si on veut un tableau
   * IMPORTANT l'objet doit d'abors passer par get_object_vars( ) avant d'être transmis !
  */
  protected function getFields($entity, $isString = false){
    $arrayProprietes = array_keys($entity);
    
    $arr=[];
    foreach ($arrayProprietes as $k) {
      if(substr($k, 0, 2) == '__'){
        // La clef du tableau est le nom du champ (et de la propriété) et la valeur est la propriété
        $arr[] = substr($k, 2);
      }
    }
    // Si isTring = true => array, sinon => string
    return $isString ? $arr : implode(',',$arr);
  }
  
  /*
   * Cette méthode est appelée depuis un nomTableObj.
   * En fonction de si c'est un getEntity, createEntity, ou updateEntity on ne construit pas de la même façons
   */
  protected function constructEntity($entity, $action, $fields=false, $exclusions=[]){
    $arrCreateEntity = $arrUpdateEntity=[];
    
    foreach($fields as $k => $v){
      $prop = "__".$k;
      
      // GetEntity
      if($action === 'getEntity'){
        $entity->$prop = $fields->$k;
      }
      
      // createEntity
      if($action === 'createEntity'){
        $prop2 = "__".$v;
          
        // Les exclusions contiennent les champs auto_incrémentés, ex: id.
        if(!in_array($v, $exclusions) ){
          $arrCreateEntity[$v] = $entity->$prop2;
        } 
      }
      // updateEntity
      // On récupère les données qui ont été modifié par setNomChamp() dans l'entité
      if($action === 'updateEntity'){
        // Les exclusions contiennent les champs auto_incrémentés, par défaut: id
        if(!in_array($k, $exclusions) ){
          $arrUpdateEntity[$k] = $entity->$prop;
        }
      }
    }
    
    if($action === 'getEntity')   { return $entity; }
    if($action === 'createEntity'){ return $arrCreateEntity; }
    if($action === 'updateEntity'){ return $arrUpdateEntity; }
  }
}