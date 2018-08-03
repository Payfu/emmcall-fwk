<?php
namespace Core\Table;
/**
 * J'appel pour le constructeur la connexion à la base de données se trouvant dans Core
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
     * @return false s'il ne trouve rien
     */
    public function find($where = [])
    {
      if(count($where) == 0){ die("<strong>La méthode find() ne peut être vide !</strong>");}
      $attr_part = $attributes = [];
        
      foreach($where as $k => $v){
        // Si je trouve une espace dans la clef alors c'est qu'il y a un opérateur ex ["nomClef !=" => "valeur"] 
        $attr_part[] = (strpos($k, ' ')) ? "{$k} ?" : "$k = ?";
        
        $attributes[] = $v;
      }
        
      // implode = 'champ1 = ?, champ2 = ?'
      $attr_part = implode(' AND ', $attr_part);
      
      return $this->query("SELECT * FROM {$this->table} WHERE {$attr_part} ", $attributes, true ); // True : retourne un seul enregistrement 
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
    public function update($where, $fields)
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
        
        return $this->query("UPDATE {$this->table} SET {$sql_part} WHERE {$attr_part} ", $attributes, true );
    }
    
    public function delete($id)
    {
      return $this->query("DELETE FROM {$this->table} WHERE id = ? ", [$id], true );
    }
    
    /**
    * Insert simple
    * $fields =  ['field'=>'value', 'field2'=>'value2']
    */
    public function insert($fields)
    {
      $sql_parts = [];
      $attributes = [];

      foreach($fields as $k => $v){
          $sql_parts[] = "$k = ?";
          $attributes[] = $v;
      }

      // implode = 'titre = ?, contenu = ?'
      $sql_part = implode(', ', $sql_parts);

      return $this->query("INSERT INTO {$this->table} SET {$sql_part} ", $attributes, true );
    }


    /**
    * Insert multiple
    */
    public function createMultiple()
    {
        /*
        $datafields = array('fielda', 'fieldb', ... );

        $data[] = array('fielda' => 'value', 'fieldb' => 'value' ....);
        $data[] = array('fielda' => 'value', 'fieldb' => 'value' ....);
        */

        
    }
    
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
     * Retourne tous les enregistrements
     * where = array : ["nomChamp"=>"valeur"]
     * ["in"=> ["date", "2018-05-28, 2018-05-27, 2018-06-01"]] 
     */
    public function all($where = null, $conditions = null)
    {
      $sql_where = $attributes = '';
      $in    = isset($conditions['in']) ? " AND ". $conditions['in'][0] ." IN ('".str_replace([',', ', '], "','",$conditions['in'][1])."')" : null;
      $order = isset($conditions['order']) ? "ORDER BY ".$conditions['order'] : null;
      $limit = isset($conditions['limit']) ? "LIMIT ".$conditions['limit'] : null;

      if ($where) {
        $sql_where = '';
        $attributes = [];

        foreach($where as $k => $v){
          // IN : La requête préparée ne semble pas fonctionner
          if($k == 'in'){
            $attr_part[] = array_keys($where['in'])[0]." IN ( ".$where['in'][ array_keys($where['in'])[0] ]." )";
            /*$attr_part[] = array_keys($where['in'])[0]." IN ( ? )"; // ex: "date IN ( ? )"
            $attributes[] = $where['in'][ array_keys($where['in'])[0] ];*/
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

      return $this->query("SELECT * FROM {$this->table} {$sql_where} {$in} {$order} {$limit}", $attributes);
    }
    
    /**
     * On appel les requêtes dans les classes du dossier Entity (il suffit de changer le nom de la class ex: PostTable -> PostEntity)
     * La requête est préparée quand il y a des attribues
     */
    public function query($statement, $attributes = null, $one = false)
    {
        
      if($attributes)
      {
        return $this->db->prepare(
          $statement, 
          $attributes, 
          str_replace('Table', 'Entity', get_class($this)), 
          $one
        );
      }
      else
      {
        return $this->db->query(
          $statement, 
          str_replace('Table', 'Entity', get_class($this)), 
          $one
        );
      }
    }
}
