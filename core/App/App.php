<?php
use core\DataBase\Config;
use core\Yaml\YamlParseFilePhp;
/*
 * Ici se trouvent des variables static dont une permettant de sauvegarder la connexion à la base de donnée
 * Pour appeler une méthode statique seule cette syntaxe suffit : App::nomMethode();
 */

/**
 * Description of App : Singleton
 * Ici se trouvent des variables static dont une permettant de sauvegarder la connexion à la base de donnée
 * Pour appeler une méthode statique seule cette syntaxe suffit : App::nomMethode();
 * Cette classe étant utilisée partout, on peut y faire passer d'autres variables comme les nom de page.
 * @author Emmanuel CALLEC
 */
class App
{
  // MODIF : est-ce que je peut les charger directement dans le contructeur ?
  public $title;
  public $description;
  public $keywords;
  public $author;
  public $lang;
  public $copyright;
  public $contact_from;
  public static $_databases;
  private static $_instance;

  /*
   * Récupération (via config/config.yml) des différentes varables comme le nom du site, la description etc...
   * 
   */
  public function __construct(){ 
    // On charge la classe YamlParseFilePhp
    $yamlPhp = new YamlParseFilePhp();
    
    // On converti le fichier yaml en array et on le transmet
    $urlYaml = ROOT . '/config/config.yml';
    $yamlParse = READ_YAML ? yaml_parse_file($urlYaml) : $yamlPhp->convertYamlToArray($urlYaml);
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
   */
  public static function getInstance(){
    if(is_null(self::$_instance)){
      self::$_instance = new App();
    }
    return self::$_instance;
  }
  
  /*
   * On récupère l'ensemble des bdd
   * Cette méthode static est appellée dans core/App/DbController
   */
  public static function getDatabases():array{
    return self::$_databases;
  }

  /**
   * Appelé depuis index.php
   * Cette méthode va charger 1 autoloader (Bundle + vendor) + un session_start
   */
  public static function Load(){
    session_start();
    require_once  ROOT . '/core/App/Autoloader.php';
    // Vendor
    require_once ROOT . '/vendor/autoload.php';
  }
  
  /*
   * Méthode de débug
   */
  public function debug($var, $stop = true){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if($stop){ exit; }
  }
}
