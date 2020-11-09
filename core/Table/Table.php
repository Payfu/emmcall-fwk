<?php
// Dernière maj : 28/12/2018 :correction suite à un écrasement et modification de query
namespace Core\Table;
/**
 * On appelle pour le constructeur la connexion à la base de données se trouvant dans Core
 */
use Core\DataBase\DataBase;

/**
 * Description of Table
 *
 * @author EmmCall
 */
class Table
{
  protected $table;
  protected $db;

  /**
   * Deviner le nom de la table à partir du nom de la classe
   * '\App\DataBase\DataBase' signifie que pour travailler j'ai besoin de passer en paramètre la base de données
   * Cette "injection de dépendances" permet d'appeler un Parent mais aussi un Enfant
   */
  public function __construct(DataBase $db)
  {
    $this->db = $db;

    // On verifie que le nom de la table est définie
    if(is_null($this->table))
    { 
      $parts = explode('\\', get_class($this));
      $class_name = end($parts);
      $this->table = strtolower(str_replace('Table', '', $class_name)) . 's';
    }  
  }
    
  /**
   * On va chercher un résultat sur un ou pluieurs champs
   * 
   * @param array $where : field => value
   * select = list des champs ) mettre dans le select
   * @return false s'il ne trouve rien
   */
  public function find($where = [], $conditions = null, $debug = false)
  {
    if(count($where) == 0){ die("<strong>La méthode find() ne peut être vide !</strong>");}
    $attr_part = $attributes = [];

    $cache  = isset($conditions['cache'])   ? $conditions['cache']  : null;
    $select = isset($conditions['select'])  ? $conditions['select'] : "*";

    foreach($where as $k => $v){
      // Si je trouve une espace dans la clef alors c'est qu'il y a un opérateur ex ["nomClef !=" => "valeur"] 
      $attr_part[] = (strpos($k, ' ')) ? "{$k} ?" : "$k = ?";

      $attributes[] = $v;
    }

    // implode = 'champ1 = ?, champ2 = ?'
    $attr_part = implode(' AND ', $attr_part);

    $sql = "SELECT {$select} FROM {$this->table} WHERE {$attr_part} ";

    if($debug){
      echo "<pre>";
      print_r($sql);
      print_r($attributes);
      echo "</pre>";
      exit();
    }

    return $this->query($sql, $attributes, true, $cache );
  }
    
  /**
   * On va chercher un résultat
   * @param array $tab : function => field
   * Exemple de fonction : MAX(nomchamp)
   * @return false s'il ne trouve rien
   */
  public function findByFunction($tab = [])
  {
    $functionKey = key($tab);
    $field = $tab[$functionKey];
    $function = strtoupper($functionKey)."(".$field.")"; // ex : MAX(id)

    return $this->query("SELECT {$function} FROM {$this->table} WHERE {$field} != ''", "", true ); // True : retourne un seul enregistrement
  }
    
  /*
   * $where ex : ['id' => 'value']
   * $fields (les champs à modifier) ex : ['name_field1' => 'value', 'name_field2' => 'value']
   */
  public function update($where, $fields, $debug = false)
  {
    $sql_parts = [];
    $attributes = [];

    foreach($fields as $k => $v){
      $sql_parts[] = "$k = ?";
      $attributes[] = $v;
    }

    foreach($where as $k => $v){
      $attr_part[] = "$k = ?";
      $attributes[] = $v;
    }

    // implode = 'titre = ?, contenu = ?'
    $sql_part = implode(', ', $sql_parts);
    $attr_part = implode(' AND ', $attr_part);
    
    $sql = "UPDATE {$this->table} SET {$sql_part} WHERE {$attr_part} ";

    if($debug){
      echo "<pre>";
      print_r($sql);
      print_r($attributes);
      echo "</pre>";
      exit();
    }
    
    return $this->query($sql, $attributes, true );
  }
    
  /*
   * Delete
   */
  public function delete($id, string $nomColonne = 'id')
  {
    return $this->query("DELETE FROM {$this->table} WHERE {$nomColonne} = ? ", [$id], true );
  }
    
  /*
   * Delete par paramètres
   * $where ex : ['colonne1'=>'val1', 'colonne2'=>'val2']
   */
  public function deleteByParams($where, $debug = false)
  {
    foreach($where as $k => $v){
          $attr_part[] = "$k = ?";
          $attributes[] = $v;
    }

    $attr_part = implode(' AND ', $attr_part);

    $sql = "DELETE FROM {$this->table} WHERE {$attr_part} ";

    if($debug){
      echo "<pre>";
      print_r($sql);
      print_r($attributes);
      echo "</pre>";
      exit();
    }

    return $this->query($sql, $attributes, true );
  }
    
  /**
  * Insert simple
  * $fields =  ['field'=>'value', 'field2'=>'value2']
  */
  public function insert($fields, $debug = false)
  {
    $sql_parts = [];
    $attributes = [];

    foreach($fields as $k => $v){
        $sql_parts[] = $k;
        $prepa[] = "?";
        $attributes[] = $v;
    }

    // implode = 'titre = ?, contenu = ?'
    $sql_part = implode(', ', $sql_parts);
    $prepa = implode(', ', $prepa);

    $sql = "INSERT INTO {$this->table} ({$sql_part}) VALUES ({$prepa}) ";

    if($debug){
      echo "<pre>";
      print_r($sql);
      print_r($attributes);
      echo "</pre>";
      exit();
    }

    return $this->query($sql, $attributes, true );
  }
    
  /**
  * Insert multiple
  *
  public function insertMultiple()
  {
      
      $datafields = array('fielda', 'fieldb', ... );
      $data[] = array('fielda' => 'value', 'fieldb' => 'value' ....);
      $data[] = array('fielda' => 'value', 'fieldb' => 'value' ....);
      
  }*/
    
  /*
   * Extract
   */
  public function extract($key, $value)
  {
      $records = $this->all();
      $return = [];
      foreach($records as $v){
         $return[$v->$key] = $v->$value;
      }
      return $return;
  }
    
  /*
   * ALL() 
   * Retourne tous les enregistrements
   * where = array : ["nomChamp"=>"valeur"]
   * ["in"=> ["date" => "2018-05-28, 2018-05-27, 2018-06-01"]] 
   * ["not-in"=> ["date" => "2018-05-28, 2018-05-27, 2018-06-01"]] 
   * ['between'=>['date'=>'2018-01-01', '2019-01-01']]
   * 
   * conditions = array : ["order" => "nomChamp DESC"]
   * ['limit'=>100] 
   * ['top'=>100] 
   * ['cache'=>60] 
   * ['select'=>"colonne1, colonne2"] 
   */
  public function all($where = null, $conditions = null, $debug = false){ 
    $sql_where = $attributes = '';
    $order  = isset($conditions['order'])   ? "ORDER BY {$conditions['order']}" : null;
    $limit  = isset($conditions['limit'])   ? "LIMIT {$conditions['limit']}" : null;
    $top    = isset($conditions['top'])     ? "TOP ({$conditions['top']}) " : null;
    $cache  = isset($conditions['cache'])   ? $conditions['cache']  : null;
    $select = isset($conditions['select'])  ? $conditions['select'] : "*";

    if ($where) {
      $sql_where = '';
      $attributes = [];

      foreach($where as $k => $v){
        // IN : La requête préparée ne semble pas fonctionner alors elle est écrite complètement
        if($k == 'in'){
          $attr_part[] = array_keys($where['in'])[0]." IN ( ".$where['in'][ array_keys($where['in'])[0] ]." )";
        } else if($k == 'not-in'){
          $attr_part[] = array_keys($where['not-in'])[0]." NOT IN ( ".$where['not-in'][ array_keys($where['not-in'])[0] ]." )";
        } else if($k == 'between'){
          $attr_part[] = array_keys($where['between'])[0] . " BETWEEN ? AND ?";
          $attributes[] = $where['between'][ array_keys($where['between'])[0] ];
          $attributes[] = $where['between'][ array_keys($where['between'])[1] ];
        } else {
          // Si je trouve une espace dans la clef alors c'est qu'il y a un opérateur ex ["nomClef !=" => "valeur"] 
          $attr_part[] = (strpos($k, ' ')) ? "{$k} ?" : "$k = ?";
          $attributes[] = $v;
        }
      }
      
      $attr_part = implode(' AND ', $attr_part);

      if($where)
      {
        $sql_where = "WHERE {$attr_part}";    
      }
    }
    
    $sql = "SELECT {$top} {$select} FROM {$this->table} {$sql_where} {$order} {$limit}";

    if($debug){
      echo "<pre>";
      print_r($sql);
      print_r($attributes);
      echo "</pre>";
      exit();
    }
    
    return $this->query($sql, $attributes, null, $cache);
  }
  
  /**
   * On appelle les requêtes dans les classes du dossier Obj (il suffit de changer le nom de la class ex: PostTable -> PostObj)
   * La requête est préparée quand il y a des attribues
   * @cache = (temps de la mise en cache en seconde)
   */
  public function query(string $statement, $attributes = null, $one = false, string $timeCache = null)
  {
    // Si on demande de mettre en cache
    if($timeCache){
      $cache = new Cache();
      return $cache->addCache(compact("timeCache","statement","attributes","one"), $this->finalQuery($statement, $attributes, $one) );
    } 
    /*
       * Pas de mise en cache on retourne les données
       */
    else {
      return $this->finalQuery($statement, $attributes, $one);
    }
  }
  
  /*
   * Requête finale
   * C'est elle qui retourne les données
   */
  private function finalQuery(string $statement, $attributes = null, $one = false){
    // J'ai modifié les requêtes pour retirer l'appel au dossier entity qui deviendra obsolète
    if($attributes){
      return $this->db->prepare($statement, $attributes, null, $one);
    } else {
      return $this->db->query($statement, null, $one);
    }
  }
  
  /*
   * On récupère le dernier id enregistré
   */
  public function lastInsertId(){
    return $this->db->lastInsertId();
  }
}
