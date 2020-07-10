<?php
use Core\Config;
use Core\DataBase\TypeDataBase;
use Core\Yaml\YamlParseFilePhp;
/*
 * Ici se trouvent des variables static dont une permettant de sauvegarder la connexion à la base de donnée
 * Pour appeler une méthode statique seule cette syntaxe suffit : App::nomMethode();
 */

/**
 * Description of App : Singleton
 * 
 * Cette classe étant utilisée partout, on peut y faire passer d'autres variables comme les nom de page.
 * @author EmmCall
 */

class App
{
  public $title;
  public $description;
  public $keywords;
  public $author;
  public $lang;
  public $copyright;
  public $contact_from;
  
  private $db_instance;
  public static $_databases;
  private static $_instance;

  /*
   * Récupération (via config/config.yml) des différentes varables comme le nom du site, la description etc...
   */
  public function __construct()
  {
    // On charge la classe YamlParseFilePhp
    $yamlPhp = new YamlParseFilePhp();
    
    //$config = Config::getInstance(ROOT . '/config/config.php');
    // On converti le fichier yaml en array et on le transmet
    $urlYaml = ROOT . '/config/config.yml';
    $yamlParse = READ_YAML ? yaml_parse_file($urlYaml) : $yamlPhp->convertYamlToArray($urlYaml);
    //$config = Config::getInstance(yaml_parse_file(ROOT . '/config/config.yml'));
    $config = Config::getInstance($yamlParse);

    // la clef database nécessite l'appel de la méthode getDatabase
    self::$_databases   = $config->get('database');    
        
    $this->title        = $config->get('title');
    $this->description  = $config->get('description');
    $this->keywords     = $config->get('keywords');
    $this->author       = $config->get('author');
    $this->lang         = $config->get('lang');
    $this->copyright    = $config->get('copyright');

    // Les email
    $this->contact_from     = $config->get('contact_from');
    $this->contact_objet    = $config->get('contact_objet');    
    
  }

  /**
   * Si une instance est déjà en cours on ne la relance pas
   *
   */
  public static function getInstance()
  {
    if(is_null(self::$_instance)){
      self::$_instance = new App();
    }
    return self::$_instance;
  }
  
  /*
   * On récupère l'ensemble des bdd
   */
  public static function getDatabases():array{
    return self::$_databases;
  }

  /**
   * Cette méthode va charger 3 autoloader + un session_start
   * Chaques autoloader chargera les classes dans son dossier respectif
   */
  public static function Load()
  {
    session_start();
    
    require_once  ROOT . '/app/Autoloader.php';
    App\Autoloader::register();

    require_once ROOT . '/core/Autoloader.php';
    Core\Autoloader::register();

    // Vendor de composer
    require_once ROOT . '/vendor/autoload.php';
  }

  /**
  * Cette méthode utlise une Factory permettant d'appeler une succession de tables sans difficulté
  */
  public function getTable($nameTable, $nomBase, $fromBundle = null)
  {
    if($fromBundle){
      $className = "\\app\\Src\\{$fromBundle}\\Table\\" . ucfirst($nameTable) . "Table"; // ex : App\src\NomBundle\Table\CategoriesTable
    } else {
      $className = "\\app\\Table\\" . ucfirst($nameTable) . "Table"; // ex : App\Table\CategoriesTable
    }
    // Instanciation de la classe
    // getDb() doit recevoir le paramètre qui identifie la bonne bdd !
    return new $className($this->getDb($nomBase));
  }

  /**
  * Second Factory pour la base de données
  */
  public function getDb($nomBase)
  { 
    foreach(self::$_databases as $k=>$v){
      if($k === $nomBase){
        $this->db_instance = new TypeDataBase($v);
        // c'est la bonne base on casse la boucle
        break;
      }
    }
    return $this->db_instance;
  }    
}
