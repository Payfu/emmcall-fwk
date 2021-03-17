<?php

namespace core\DataBase;

/**
 * Par défaut cette classe ira chercher la configuration du site
 * Ceci est un Singleton, c'est à dire : une instance appelée qu'une seule fois.
 *
 * @author EmmCall
 */
class Config
{
  private $settings = [];

  /**
   * la variable static $_instance permettra de stocker l'instance dans le Singleton
   * L'underscore sert à différencier les variables static des variables classiques
   */
  private static $_instance; 

  /**
   * @param type array
   * $file sera le fichier config.yml que je souhaite charger converti en amont en array
   */
  public function __construct(array $file){
    $this->settings = $file;
  }

  /**
   * Cette methode permet de n'appeler qu'une seule fois l'instance
   */
  public static function getInstance($file){
    if(is_null(self::$_instance)){
      self::$_instance = new Config($file);
    }
    return self::$_instance;
  }

  /*
   * Cette methode retourne la clef appelée ex : db_name
   */
  public function get(string $key){
    // On récupère les clefs primaire du tableau multidimensionnel 
    foreach(array_keys($this->settings) as $k){ //$k = meta ... database
      // Si c'est la base de donnée
      // Si la clef 'database' n'est pas vide on traite les données de connexion de la bdd
      if(!empty($this->settings["database"])){
        if($k === 'database' && array_key_exists($key, $this->settings)){
          return !isset($this->settings[$key]) ? null : $this->settings[$key];
        }
      }
      // Si la clef demandé existe on retourne sa valeur
      if(array_key_exists($key, $this->settings[$k])){     
        return !isset($this->settings[$k][$key]) ? null : $this->settings[$k][$key];
      }
    }
  }
     
  /*
   * On récupère le tableau database
   */
  public function getDatabase(string $key){
    if(array_key_exists($key, $this->settings)){

      if(!isset($this->settings[$key])){
          return null;
      }
      return $this->settings[$key];
    }
  }
}