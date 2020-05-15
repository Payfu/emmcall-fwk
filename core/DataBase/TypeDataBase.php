<?php

namespace Core\DataBase;

// l'antislashe devant PDO permet d'appeler PDO à la racine de PHP sans tenir compte du namespace
use \PDO;

/**
 * Description of DataBase
 *
 * @author payfu
 */
class TypeDataBase extends DataBase
{
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_host;
    private $db_type;
    private $db_prov;
    private $db_port;
    private $pdo;

    private static $_instance;

    public function __construct( array $dbParam = [])
    { 
      if(count($dbParam) == 0){ die("Aucun paramètres de bdd"); }
      
      $type = strtoupper($dbParam['db_type']);
      $this->db_name = isset($dbParam['db_name']) ? $dbParam['db_name'] : NULL ;
      $this->db_user = isset($dbParam['db_user']) ? $dbParam['db_user'] : NULL ;
      $this->db_pass = isset($dbParam['db_pass']) ? $dbParam['db_pass'] : NULL ;
      $this->db_host = isset($dbParam['db_host']) ? $dbParam['db_host'] : NULL ;
      $this->db_prov = isset($dbParam['db_prov']) ? $dbParam['db_prov'] : NULL ;
      $this->db_port = isset($dbParam['db_port']) ? $dbParam['db_port'] : NULL ;
      
      $this->db_type = $type;
    }
    

    public static function getInstance()
    {
      if(is_null(self::$_instance)){ self::$_instance = new App(); }
      return self::$_instance;
    }

    /**
      * En plaçant la connexion PDO dans une autre méthode, cela permet de n'avoir à changer que getPDO pour modifier le système de connexion à la BDD
      */
    private function getPDO()
    {
      // Si l'objet DataBase n'a pas de propriété PDO, alors on initialise le tout. Ceci évite les connexions à répétition.
      if($this->pdo === null)
      {
        if($this->db_type === "SQLSRV"){
          $options = [];
          $pdo = new PDO("sqlsrv:Server={$this->db_host};Database={$this->db_name}", $this->db_user, $this->db_pass, $options);
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        if($this->db_type === "MYSQL"){
          $options = array( PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
          $pdo = new PDO('mysql:dbname='.$this->db_name.';host='.$this->db_host, $this->db_user, $this->db_pass, $options);
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        $this->pdo = $pdo;  
      }

      return $this->pdo;
    }
    
    /**
     * Récupération des résultats Pour SQLSRV et MYSQL
     * IMPORTANT : ça ne marchera pas pour HFSQL
     */
    public function query($statement, $class_name = null, $one = false)
    { 
        // getPDO revient à appeler la connexion à la base
        $req = $this->getPDO()->query($statement);
        
        // On regarde la commande en première position : update, insert, delete : dans ce cas, nul besoin de faire un fetchall
        // Les 3 '=' sont importants car ils renvoient False avec seulement 2 '=' cela serait True
        if (
            strpos($statement, 'UPDATE') === 0 ||
            strpos($statement, 'INSERT') === 0 ||
            strpos($statement, 'DELETE') === 0
        ){
            return $req;
        }
        
        // Fetch_all met les réultat dans un tableau 
        // et le fetch_obj réorganise les résultats dans un objet plutôt qu'un tableau.
        // en gros il y a un tableau qui contient des objets
        if($class_name === null) {
            $req->setFetchMode(PDO::FETCH_OBJ);
        } else {   
            // Fetch_class fonctionne comme Fetch_obj mais l'objet ne sera plus un stdClass mais $class_name
            // Ce fetchStyle définit le mode de récupération par défaut pour cette requête
            $req->setFetchMode(PDO::FETCH_CLASS, $class_name);
        }
        
        // On renvoie les données
        if($one)    {            $data = $req->fetch();        }  
        else        {            $data = $req->fetchAll();     }
        
        return $data;
    }
    
    /**
     * Les requêtes préparées
     * ex: ('SELECT * FROM articles WHERE id = ? ', [$_GET['id']], 'App\Table\Article', true)
     * Si $one = true alors on ne retourne qu'un seul enregistrement, sinon tout les enregistrements correspondant.
     */
    public function prepare($statement, $attributes, $class_name = null, $one = false )
    {
        // On prépare la requêtes
        $req = $this->getPDO()->prepare($statement);
        // On execute la requête
        $res = $req->execute($attributes);
        
        // On regarde la commande en première position : update, insert, delete : dans ce cas, nul besoin de faire un fetchall
        // Les 3 '=' sont importants car ils renvoient False avec seulement 2 '=' cela serait True
        if (
            strpos($statement, 'UPDATE') === 0 ||
            strpos($statement, 'INSERT') === 0 ||
            strpos($statement, 'DELETE') === 0
        ){
            return $res;
        }
        
        if($class_name === null){
            $req->setFetchMode(PDO::FETCH_OBJ);
        } else {   
            // Fetch_class fonctionne comme Fetch_obj mais l'objet ne sera plus un stdClass mais $class_name
            // Ce fetchStyle définit le mode de récupération par défaut pour cette requête
            $req->setFetchMode(PDO::FETCH_CLASS, $class_name);
        }
        
        // On renvoie les données
        if($one)    {            $data = $req->fetch();        }  
        else        {            $data = $req->fetchAll();     }
        
        return $data;
    }
    
    /**
     * 
     * @return l'id du dernier insert
     * c'est une fonction intégrée à PDO
     */
    public function lastInsertId()
    {
        return $this->getPDO()->lastInsertId();
    }
}
